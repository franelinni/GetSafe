<?php
namespace App\Service\Image;

use App\Service\File\FileService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class ImageDownloaderService
{
    protected const IMG_URL_REGEX = '/^(http(s?):)([/|.|\w|\s|-])*\.(?:jpg|gif|png)/';
    
    public function __construct(protected FileService $fileService)
    {   

    }

    public function getImages(\SplFileInfo $file, string $destDir)
    {
        try {
            $splFile = new \SplFileInfo($file);
            $fileData = $this->fileService->processFile($splFile, $destDir);
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}