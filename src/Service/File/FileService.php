<?php
namespace App\Service\File;

use App\Entity\InputFile;
use App\Service\File\FileProcessor\FileProcessorInterface;
use App\Service\File\FileProcessor\TxtFileProcessor;
use App\Service\File\FileProcessor\CsvFileProcessor;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;

class FileService
{
    protected \SplFileInfo $currentFile;

    protected const DEST_DIR = 'data/storage';

    protected const SOURCE = 'file';

    protected array $allowedExtensions = [
        'txt', 'csv'//, 'rtf'
    ];

    protected array $fileContent;

    public function __construct(protected array $fileProcessors, protected EntityManagerInterface $entityManager)
    {
       
    }

    public function processFile(\SplFileInfo $inputFile): ?InputFile
    {
       try {
            $this->currentFile = $inputFile;
            $fileProcessor = $this->getFileProcessor();
            
            $this->fileContent = $fileProcessor->readFile($inputFile);
            $inputFile = $this->saveFileMeta($inputFile, $this->fileContent['count']);
            
            return $inputFile;
            
        } catch(\Exception $e) {
            throw new \Exception($e);
        }
    }

    public function getFileContents(): ?array
    {
        return $this->fileContent ?: null;
    }

    private function getFileExtension(): ?String
    {
        $ext = $this->currentFile->getExtension();
        return in_array($ext, $this->allowedExtensions) ? $ext : null;
    }

    /**
     * @throws \Exception
     * @return FileProcessorInterface
     */
    private function getFileProcessor(): FileProcessorInterface | \Exception
    {
        if (!$ext = $this->getFileExtension()) {
            return new \Exception('File extension is not allowed.');
        }
        $processorName = $this->fileProcessors[$ext];

        return new ($processorName)();
    }

    protected function saveFileMeta(\SplFileInfo $splFileInfo, int $lines, string $desDir = self::DEST_DIR): InputFile
    {
        $inputFile = $this->entityManager->getRepository(InputFile::class)
            ->findOneBy(['filename' => $splFileInfo->getFilename()]);
        
        if (!$inputFile) {
            $inputFile = new InputFile();
            $inputFile->setFilename($splFileInfo->getFilename())
                ->setSize($splFileInfo->getSize())
                ->setSource(SELF::SOURCE)
                ->setNLines($lines)
                ->setDestination($desDir)
                ->setCreatedAt(new DateTime('now'));
        } else {
            $inputFile->setUpdatedAt(new DateTime('now'));
        }
        
        $this->entityManager->persist($inputFile);
        $this->entityManager->flush();

        return $inputFile;
    }

    public function getFileUrls (string $inputFileName): array
    {
        $sourceFile = new \SplFileInfo($inputFileName);
        $inputFile = $this->processFile($sourceFile);
        $fileContent = $this->getFileContents();

        return $fileContent['urls'];
    }
}