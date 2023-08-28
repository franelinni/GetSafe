<?php
namespace App\Service\File\FileProcessor;

class FileProcessorContext {
    private $fileProcessor;

    public function __construct(FileProcessorInterface $fileProcessor) 
    {
        $this->fileProcessor = $fileProcessor;
    }

    
    public function processFile(\SplFileInfo $filePath): array 
    {
        if (!$this->fileProcessor->validateFile($filePath)) {
            throw new \RuntimeException(sprintf('<error>Input file %s does not exist or is not readable.</error>', $filePath));
        }

        return $this->fileProcessor->readFile($filePath);
    }
}
