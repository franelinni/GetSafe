<?php
namespace App\Service\File\FileProcessor;

interface FileProcessorInterface 
{
    /**
     * @param \SplFileInfo $filePath
     * @return bool
     */
    public function validateFile(string $filePath): bool;
    public function readFile(\SplFileInfo $filePath): array;
}