<?php
namespace App\Service\File\FileProcessor;

class CsvFileProcessor extends FileProcessorAbstract  
{

    public function readFile(\SplFileInfo $filePath): array 
    {
        return[];// Implement CSV file reading logic here
    }
}