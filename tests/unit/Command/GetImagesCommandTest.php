<?php 

namespace App\Tests\Command;

use App\Command\GetImagesCommand;
use App\Entity\Image;
use App\Entity\InputFile;
use App\Service\File\FileService;
use App\Service\Image\ImageService;
use App\Service\Image\ImageProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\DataProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class GetImagesCommandTest extends TestCase
{
    protected string $commandName = 'app:image-downloader';

    /**
     * @var MockObject | ImageService
     */
    protected $imageServiceMock;

    protected $imageProcessorMock;

    protected $fileServiceMock;

    protected array $fileContents = [
        'urls' => [
            'https://fastly.picsum.photos/id/408/400/400.jpg?hmac=H6nrCIt7s8kTZ12hSqoeSPxOCCmDytuE6e0AYiToOZQ',
            'https://images.unsplash.com/photo-1560807707-8cc77767d783 ',
            ' https://cdn.discordapp.com/attachments/1092950568466661496/1142164961209106502/franelinni_scene_with_hands_only_models_arms_holding_a_luxuriou_50911f22-2bc3-4c00-9ee5-cee05dcbb184.png'
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageServiceMock = $this->createMock(ImageService::class);
        $this->imageProcessorMock = $this->createMock(ImageProcessor::class);
        $this->fileServiceMock = $this->createMock(FileService::class);

        $this->fileServiceMock = $this->createMock(FileService::class);
        $this->fileServiceMock->expects($this->once())
            ->method('processFile')
            ->willReturn(new InputFile());
        $this->fileServiceMock->expects($this->once())
            ->method('getFileContents')
            ->willReturn($this->fileContents);
    }

    public static function commandDataProvider(): array
    {
        return [
            'success' => [
                [
                    'urls' => [
                        'https://fastly.picsum.photos/id/408/400/400.jpg?hmac=H6nrCIt7s8kTZ12hSqoeSPxOCCmDytuE6e0AYiToOZQ',
                        'https://images.unsplash.com/photo-1560807707-8cc77767d783 ',
                        ' https://cdn.discordapp.com/attachments/1092950568466661496/1142164961209106502/franelinni_scene_with_hands_only_models_arms_holding_a_luxuriou_50911f22-2bc3-4c00-9ee5-cee05dcbb184.png'
                    ]
                ],
                [
                    Command::SUCCESS,
                    [
                    'totalLines' => 3,
                    'totalDownloads' => 3,
                    'errors' => 0
                    ]
                ]

            ],
            'invalid Url' => [
                [
                    'urls' => [
                        'https://fastlyphotos/id/408/400/400.jpg?hmac=H6nrCIt7s8kTZ12hSqoeSPxOCCmDytuE6e0AYiToOZQ',
                        'https://images.photo-1560807707-8cc77767d783 ',
                        ' https://cdn.discordapp.com/attachments/1092950568466661496/1142164961209106502/franelinni_scene_with_hands_only_models_arms_holding_a_luxuriou_50911f22-2bc3-4c00-9ee5-cee05dcbb184.png'
                    ]
                ],
                Command::SUCCESS,
                [
                    'totalLines' => 3,
                    'totalDownloads' => 1,
                    'errors' => 2
                ]
            ],
            'exception' => [
                [
                    'urls' => [
                        'https://fastlyphotos/id/408/400/400.jpg?hmac=H6nrCIt7s8kTZ12hSqoeSPxOCCmDytuE6e0AYiToOZQ',
                    ]
                ],
                Command::FAILURE,
                [
                    'totalLines' => 3,
                    'totalDownloads' => 1,
                    'errors' => 2
                ]
            ]
        ];
    }
    
    #[DataProvider('commandDataProvider')]
    public function testExecuteSuccess($fileContent, $expectedResult, $expectedOutput): void
    {
        $file = __DIR__ . '/../../data/urls_ok.txt';
        $destination = __DIR__ . '/../../data/images';

        // $expectedResult = [
        //     'totalLines' => 3,
        //     'totalDownloads' => 3,
        //     'errors' => 0
        // ];

        foreach ($fileContent['urls'] as $url) {
            $image = new Image();
            $image->setUrl(trim($url));

            $this->imageServiceMock->expects($this->once())
                ->method('getImageByUrl')
                ->willReturn($image);
            $this->imageServiceMock->expects($this->once())
                ->method('updateImage');
            $this->imageServiceMock->expects($this->once())
                ->method('saveImageValidationLog');
            $this->imageServiceMock->expects($this->once())
                ->method('saveImageFileRelation');

            $fileObject = new \SplFileObject($file);
            
            $this->imageProcessorMock->expects($this->atMost(3)) 
                ->method('processImage')
                ->with(trim($url), $destination)
                ->willReturn($fileObject);
        }
        
        $application = new Application();
        $application->add(new GetImagesCommand($this->imageProcessorMock, $this->imageServiceMock, $this->fileServiceMock));

        $command = $application->find($this->commandName);
        $commandTester = new CommandTester($command);

        // Simulate running the command with arguments and options
        $result = $commandTester->execute([
            'command' => $command->getName(),
            'inputFile' => $file, 
            '--destination' => $destination,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Download has completed', $output);
        $this->assertStringContainsString('Downloaded files will be saved into', $output);
        $this->assertStringContainsString($destination, $output);
        $this->assertEquals($expectedResult, $result);
        $this->assertStringContainsString('Total downloaded images', $output);
    }

    public function testExecuteNoInputFile ()
    {
        
        $application = new Application();
        $application->add(new GetImagesCommand($this->imageProcessorMock, $this->imageServiceMock, $this->fileServiceMock));

        $this->expectExceptionMessage('Not enough arguments (missing: "inputFile")');

        $command = $application->find($this->commandName);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName()
        ]);
    }

    public function testExecuteNoDestinationDirectoryFileThrowsException (): void
    {
        $file = __DIR__ . '/.../../data/image_to_txt.txt';
        $default_destination = ImageProcessor::DESTINATION_DIRECTORY;

        $imageServiceMock = $this->createMock(ImageService::class);
        $imageServiceMock->expects($this->once())
            ->method('getImages')
            ->willThrowException(new \Exception('message'));

        $application = new Application();
        $application->add(new GetImagesCommand($this->imageProcessorMock, $this->imageServiceMock, $this->fileServiceMock));

        $command = $application->find($this->commandName);
        $commandTester = new CommandTester($command);

        // Simulate running the command with arguments and options
        $result = $commandTester->execute([
            'command' => $command->getName(),
            'inputFile' => $file
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('You\'re about to download images from the file:', $output);
        $this->assertStringContainsString('data/image_to_txt.txt', $output);
        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('<error>', $output);
        $this->assertStringContainsString(sprintf('[WARNING] No Destination is provided. Default <code>%s</code> will be used', $default_destination), $output);
    }
}
