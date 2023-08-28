<?php
namespace App\Service\Image;

use App\Service\File\FileService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class ImageDownloaderService
{   
    public function __construct(protected ImageProcessor $imageProcessor)
    {   

    }

    public function getImages(\SplFileInfo $file, string $destDir, OutputInterface $output)
    {
        try {
            $splFile = new \SplFileInfo($file);
           
            return $this->imageProcessor->process($splFile, $destDir, $output);   
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}