<?php
namespace App\Service\File\FileProcessor;

abstract class FileProcessorAbstract implements FileProcessorInterface
{
   
    public function validateFile(string $filePath): bool 
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return false;
        }   
        return true;
    }
}