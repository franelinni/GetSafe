<?php
namespace App\Service\File;

use App\Entity\InputFile;
use App\Service\File\FileProcessor\FileProcessorContext;
use App\Service\File\FileProcessor\FileProcessorInterface;
use App\Service\File\FileProcessor\TxtFileProcessor;
use App\Service\File\FileProcessor\CsvFileProcessor;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;

class FileService
{
    protected \SplFileInfo $currentFile;

    protected const DEST_DIR = 'data/storage';

    protected array $allowedExtensions = [
        'txt', 'csv'//, 'rtf'
    ];

    public function __construct(protected array $fileProcessors, protected EntityManagerInterface $entityManager)
    {
       
    }

    public function processFile(\SplFileInfo $inputFile, string $destDir)
    {
        $this->currentFile = $inputFile;
        $fileProcessor = $this->getFileProcessor();
        $fileProcessorContext = new FileProcessorContext($fileProcessor);
        
        $data = $fileProcessorContext->processFile($inputFile);

        $this->saveFileMeta($inputFile, $data['count']);

        return $data;
    }

    private function getFileExtension(): String|null
    {
        $ext = $this->currentFile->getExtension();
        return in_array($ext, $this->allowedExtensions) ? $ext : null;
    }

    /**
     * Summary of getFileProcessor
     * 
     * @throws \Exception
     * @return FileProcessorInterface
     */
    private function getFileProcessor(): FileProcessorInterface
    {
        if (!$ext = $this->getFileExtension()) {
            return new \Exception('File extension is not allowed.');
        }
        $processorName = $this->fileProcessors[$ext];

        return new ($processorName)();
    }

    protected function saveFileMeta(\SplFileInfo $splFileInfo, int $lines, string $desDir = self::DEST_DIR)
    {
        $inputFile = new InputFile();
        $inputFile->setName($splFileInfo->getFilename())
            ->setSize($splFileInfo->getSize())
            ->setNLines($lines)
            ->setDestinationFolder($desDir)
            ->setCreatedAt(new \DateTime("now"));
        $this->entityManager->persist($inputFile);
        $this->entityManager->flush();
    }
}