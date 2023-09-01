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
        if (!file_exists($file->getPathname())) {
            throw new \Exception(sprintf('Input File %s doesn\'t exist.', $file->getPathname()));
        }

        $f = fopen($file->getPathname(), 'r');

        if (!$f) {
            throw new \Exception(sprintf('Input File %s cannot be opened.', $file->getPathname()));
        }

        $urls = [];
        while (!feof($f)) {
            $urls[] = fgets($f);
        }

        if (!$urls) {
            throw new \Exception(sprintf('Input File %s is empty.', $file->getPathname()));
        }

        fclose($f);

        return [
            'count' => (count($urls)),
            'urls' => $urls
        ];
    }
}