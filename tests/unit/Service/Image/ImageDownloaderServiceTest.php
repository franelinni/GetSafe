<?php 

namespace App\Tests\Service\Image;

use App\Service\Image\ImageDownloaderService;
use App\Service\Image\ImageProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ImageDownloaderServiceTest extends TestCase
{
    public function testGetImagesSucess()
    {
       $outputInterfaceMock = $this->createMock(OutputInterface::class);
        $file = new \SplFileInfo(__DIR__ . '/../data/urls_ok.txt');
        $expectedReturn = [
            'totalLines' => 1,
            'totalDownloads' => 1,
            'errors' => 0
        ];
        $imageProcessorMock = $this->createMock(ImageProcessor::class);
        $imageProcessorMock->expects($this->once())
            ->method('process')
            ->willReturn($expectedReturn);

        $imageService = new ImageDownloaderService($imageProcessorMock);
        $result = $imageService->getImages($outputInterfaceMock, $file, null);
        
        $this->assertArrayHasKey('totalLines', $result);
        $this->assertArrayHasKey('totalDownloads', $result);
        $this->assertArrayNotHasKey('message', $result);
    }

    public function testGetImagesThrowsException()
    {
       $outputInterfaceMock = $this->createMock(OutputInterface::class);
        $file = new \SplFileInfo(__DIR__ . '/../data/image_to_txt.txt');
        $expectedException = new \Exception('message');
        
        $imageProcessorMock = $this->createMock(ImageProcessor::class);
        $imageProcessorMock->expects($this->once())
            ->method('process')
            ->willThrowException($expectedException);

        $this->expectException($expectedException::class);
        $imageService = new ImageDownloaderService($imageProcessorMock);
        $imageService->getImages($outputInterfaceMock, $file, null);
    }
}