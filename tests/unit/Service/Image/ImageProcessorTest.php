<?php 

namespace App\Tests\Service\Image;

use App\Entity\Image;
use App\Entity\InputFile;
use App\Entity\InputFileImageRelation;
use App\Entity\ValidationLog;
use App\Repository\ImageRepository;
use App\Repository\InputFileImageRelationRepository;
use App\Service\File\FileService;
use App\Service\Image\ImageDownloaderService;
use App\Service\Image\ImageProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ImageProcessorTest extends TestCase
{
    protected OutputInterface | MockObject $outputMock;

    protected \SplFileInfo $sourceFile;

    protected FileService | MockObject $fileServiceMock;

    protected InputFile | MockObject $inputFileMock;

    protected ImageRepository | MockObject $imageRepositoryMock;

    protected InputFileImageRelationRepository | MockObject $fileImageRelationRepositoryMock;

    protected EntityManagerInterface | MockObject $entityManagerMock;

    protected Image | MockObject $imageMock;

    protected ValidationLog | MockObject $validationLogMock;

    protected InputFileImageRelation | MockObject $fileImageRelationMock;

    public function setUp():void
    {
        $this->inputFileMock = $this->createMock(InputFile::class);
        $this->fileServiceMock = $this->createMock(FileService::class);
        $this->fileServiceMock->expects($this->once())
            ->method('processFile')
            ->willReturn($this->inputFileMock);
        $fileContent = ['urls' => []];
        $this->fileServiceMock->expects($this->once())
            ->method('getFileContents')
            ->willReturn($fileContent);

        $this->imageMock = $this->createMock(Image::class);
        $this->validationLogMock = $this->createMock(ValidationLog::class);
        $this->fileImageRelationMock = $this->createMock(InputFileImageRelation::class);
        
        $this->imageRepositoryMock = $this->createMock(ImageRepository::class);
        // $this->imageRepositoryMock->expects($this->once())
        //     ->method('findOneBy')
        //     ->willReturn($this->imageMock);
        
        $this->fileImageRelationRepositoryMock = $this->createMock(InputFileImageRelationRepository::class);
        $this->fileImageRelationRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->fileImageRelationMock);
        
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        // $this->entityManagerMock->expects($this->once())
        //     ->method('getRepository')
        //     ->with(Image::class)
        //     ->willReturn($this->imageRepositoryMock);
        // $this->entityManagerMock->expects($this->once())
        //     ->method('getRepository')
        //     ->with(InputFileImageRelation::class)
        //     ->willReturn($this->fileImageRelationRepositoryMock);
        // $this->entityManagerMock->expects($this->atLeastOnce())
        //     ->method('persist');
        // $this->entityManagerMock->expects($this->once())
        //     ->method('flush');    

        $this->outputMock = $this->createMock(OutputInterface::class);
        
    }

    public function testProcessImagesDownloadSuccessNoImageId()
    {
        $file = new \SplFileInfo('');
        $fileContent = ['urls' => [
            ' https://fastly.picsum.photos/id/408/400/400.jpg?hmac=H6nrCIt7s8kTZ12hSqoeSPxOCCmDytuE6e0AYiToOZQ',
            'https://images.unsplash.com/photo-1560807707-8cc77767d783 ',
            'https://cdn.discordapp.com/attachments/1092950568466661496/1142164961209106502/franelinni_scene_with_hands_only_models_arms_holding_a_luxuriou_50911f22-2bc3-4c00-9ee5-cee05dcbb184.png'

        ]];
        $this->fileServiceMock->expects($this->once())
            ->method('getFileContents')
            ->willReturn($fileContent);

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Image::class)
            ->willReturn($this->imageRepositoryMock);

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(InputFileImageRelation::class)
            ->willReturn($this->fileImageRelationRepositoryMock);

        $this->imageMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);
        $this->imageMock->expects($this->atLeastOnce())
            ->method('setSize');
        $this->imageMock->expects($this->atLeastOnce())
            ->method('setUrl');
        $this->imageMock->expects($this->atLeastOnce())
            ->method('setCreatedAt');
        $this->imageMock->expects($this->atLeastOnce())
            ->method('getValidationLog')
            ->willReturn($this->validationLogMock);

        $this->imageRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->imageMock);

        $this->validationLogMock->expects($this->atLeastOnce())
            ->method('setIsValid');
        $this->validationLogMock->expects($this->atLeastOnce())
            ->method('setImage');
        $this->validationLogMock->expects($this->atLeastOnce())
            ->method('setCreatedAt');

        $this->inputFileMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(1);
        
        $this->fileImageRelationMock->expects($this->atLeastOnce())
            ->method('setImageId');
        $this->fileImageRelationMock->expects($this->atLeastOnce())
            ->method('setInputFileId');

        $this->entityManagerMock->expects($this->atLeastOnce())
            ->method('persist');
        $this->entityManagerMock->expects($this->once())
            ->method('flush');  

        $imageProcessor = new ImageProcessor($this->fileServiceMock, $this->entityManagerMock);
        $result = $imageProcessor->processImage($this->outputMock, $file, null);
   
        $this->assertArrayHasKey('totalLines', $result);
        $this->assertArrayHasKey('totalDownloads', $result);
        $this->assertArrayNotHasKey('message', $result);
    }

    protected function _GetImagesThrowsException()
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