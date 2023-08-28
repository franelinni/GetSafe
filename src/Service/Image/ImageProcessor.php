<?php 
namespace App\Service\Image;
use App\Entity\Image;
use App\Entity\InputFileImageRelation;
use App\Entity\ValidationLog;
use App\Service\File\FileService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ImageProcessor
{
    protected const URL_REGEX = '^(https?|http)://.*^';

    public function __construct(protected FileService $fileService, protected EntityManagerInterface $entityManager)
    {
        
    }

    public function process(\SplFileInfo $sourceFile, string $destDir, OutputInterface $output): array
    {
        $inputFile = $this->fileService->processFile($sourceFile);
        $fileContent = $this->fileService->getFileContents();

        $lines = $fileContent['urls'];
        $totalLinesCount = count($lines);
        $errors = 0;
        $totalDownloaded = 0;
        
        $progressBar = new ProgressBar($output, $totalLinesCount);
        $progressBar->setFormat('debug');

        foreach ($progressBar->iterate($lines) as $i => $line) {
            $line = trim("$line");

            $image = $this->entityManager->getRepository(Image::class)
                ->findOneBy(['url' => $line]) ?? new Image();
            $validationLog = $image->getValidationLog() ?? new ValidationLog();
            
            try{
                
                if($this->isImageUrl($line)) {
                    $size = $this->download($line, $destDir);
                    $validationLog->setIsValid(true);
                    $image->setSize($size);
                    $totalDownloaded++;
                } else {
                    $validationLog->setErrorMessage('File url is invalid');
                    $validationLog->setIsValid(false);
                    $errors++;
                }
            } catch (\Exception $e) {
                $validationLog->setErrorMessage(sprintf('ErrorCode: %d, Error: %s', $e->getCode(), $e->getMessage()));
                $validationLog->setIsValid(false);
                $errors++;
            }

            $now = new DateTime("now");
            
            if (!$image->getId()) {
                $image->setUrl($line)
                    ->setCreatedAt($now);
                $validationLog->setImage($image)
                    ->setCreatedAt($now);
                $fileImageRelation = new InputFileImageRelation();
            } else {
                $image->setUpdatedAt($now);
                $fileImageRelation = $this->entityManager->getRepository(InputFileImageRelation::class)
                    ->findOneBy(['input_file_id','image_id'], [$inputFile->getId(), $image->getId()]);
            }
            
            $this->entityManager->persist($image);
            $this->entityManager->persist($validationLog);

            if ($fileImageRelation) {
                $fileImageRelation->setImageId($image->getId())
                    ->setInputFileId($inputFile->getId());
                $this->entityManager->persist($fileImageRelation);
            } 
            $this->entityManager->flush();
        }

        $progressBar->finish();

        return [
            'totalLines' => $totalLinesCount,
            'totalDownloads' => $totalDownloaded,
            'errors' => $errors
        ];
    }

    protected function download (string $url, string $destDir): int
    {
        if (!file_exists($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $imageName = sha1($url) . '.jpg';
        $filePath = $destDir . '/' . $imageName;

        $data = $this->imageGetContent($url);
        file_put_contents($filePath, $data);

        $fileSize = filesize($filePath);
        if (!$fileSize) {
            throw new \Exception(sprintf('Image file [%s] is empty', $url));
        }

        return $fileSize;
    }

    protected function imageGetContent (string $url): string
    {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        
        $data = curl_exec($ch);
        if (!$data) {
            $error = curl_error($ch);
            $errorCode = curl_errno($ch);
            throw new \Exception(sprintf('Couldn\'t download image from %s. cURL Error: %s (Code: %d)', $url, $error, $errorCode));
        }
        
        curl_close($ch);

        return $data;
    }

    protected function isImageUrl (string $url): bool
    {
        if (!preg_match(self::URL_REGEX, $url)) {
            return false;
        }

        $headers = get_headers($url, true);
        if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') === 0) {
            return true;
        }

        return false;
    }
}