<?php
namespace App\Service\File\FileProcessor;

class TxtFileProcessor extends FileProcessorAbstract 
{
    /**
     * Summary of readFile
     * 
     * @throws \Exception
     */
    public function readFile(\SplFileInfo $file): array 
    {
        $f = fopen($file->getPathname(), 'r');

        if (!$f) {
            throw new \Exception('Input File cannot be opened.');
        }

        $urls = [];
        while (!feof($f)) {
            $urls[] = fgets($f);
        }

        if (!$urls) {
            throw new \Exception('Input File is empty.');
        }

        fclose($f);

        return [
            'count' => (count($urls)),
            'urls' => $urls
        ];
    }
}