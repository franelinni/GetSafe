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

        $invalidLines = [];
        $urls = [];
        while (!feof($f)) {
            $line = fgets($f);
            if(preg_match('^(http(s?):)([/|.|\w|\s|-])*\.(?:jpg|gif|png)^', $line)) {
                $urls[] = $line;
            } else {
                $invalidLines[] = $line;
            }
        }


        print_r(['validUrls', $urls]);
        print_r(['invalidStrings', $invalidLines]);

        fclose($f);

        return [
            'count' => (count($urls) + count($invalidLines)),
            'validUrls' => $urls,
            'invalidUrls' => $invalidLines
        ];
    }
}