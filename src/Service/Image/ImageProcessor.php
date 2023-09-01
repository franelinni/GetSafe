<?php 
namespace App\Service\Image;
use App\Service\File\FileService;
use Doctrine\ORM\EntityManagerInterface;
use \SplFileObject;

use Symfony\Component\Console\Output\OutputInterface;

class ImageProcessor
{
    protected const URL_REGEX = '^(https?|http)://.*^';

    public const DESTINATION_DIRECTORY = '/storage';

    public function __construct(protected FileService $fileService, protected ImageService $imageService, protected EntityManagerInterface $entityManager)
    {
        
    }

    public function processImage (?string $url, ?string $destDir): ?SplFileObject
    {
        if (!$this->isImageUrl($url)) {
            return null;     
        }

        return $this->download($url, $destDir);
    }

    protected function download (string $url, ?string $destDir = self::DESTINATION_DIRECTORY): SplFileObject
    {
        $imageName = sha1($url) . '.jpg';
        $filePath = $destDir . '/' . $imageName;

        $data = $this->imageGetContent($url);

        if (!file_put_contents($filePath, $data)) {
            throw new \Exception(sprintf('Image file [%s] is empty', $url));
        }

        return new SplFileObject($filePath);
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