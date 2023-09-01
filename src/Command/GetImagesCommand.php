<?php
namespace App\Command;

use App\Service\File\FileService;
use App\Service\Image\ImageProcessor;
use App\Service\Image\ImageService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

#[AsCommand(
    name: 'app:image-downloader',
    description: 'Download images from a list of URLs and store them locally.',
)]
class GetImagesCommand extends Command
{
    protected ImageProcessor $imageProcessor;

    protected FileService $fileService;

    protected ImageService $imageService;

    public function __construct(ImageProcessor $imageProcessor, ImageService $imageService, FileService $fileService)
    {
        parent::__construct();
        $this->imageProcessor = $imageProcessor;
        $this->fileService = $fileService;
        $this->imageService = $imageService;
    }

    protected function configure(): void
    {
        $this->setName('app:image-downloader')
            ->setDescription('Download images from a list of URLs and store them locally.')
            ->setHelp('This command allows you to download images from URLs and store them locally.')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'File with list of URLs. Can have following formats: .txt, .csv')
            ->addOption('destination', 'd', InputOption::VALUE_REQUIRED, 'Where should script save downloaded images? If none is provided, default <code>data/storage</code> will be used.');
            //todo: options for amount of threads, retries, logging, file source
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $inputFileName = $input->getArgument('inputFile');
        $destinationDir = $input->getOption('destination');
        $this->showConsoleMessageStart($io, $inputFileName, $destinationDir);

        try {
            $this->showConsoleMessageProcess($io);
            $result = $this->processImages($output, $inputFileName, $destinationDir);
        } catch (\Throwable $e) {
            $this->showConsoleMessageError($io, $e);
            //todo: log error
            return Command::FAILURE;
        }

        $this->showConsoleMessageFinish($io, $result);
        return Command::SUCCESS;
    }

    protected function processImages(OutputInterface $output, string $inputFileName, ?string $destinationDir): array
    {
        $sourceFile = new \SplFileInfo($inputFileName);
        $inputFile = $this->fileService->processFile($sourceFile);
        $fileContent = $this->fileService->getFileContents();

        $urls = $fileContent['urls'];
        $totalLinesCount = count($urls);
        $errors = 0;
        $totalDownloaded = 0;
        $errMsg = '';
        $isValid = true;
        
        $progressBar = new ProgressBar($output, $totalLinesCount);
        $progressBar->setFormat('debug');
        
        $dir = $this->imageService->getDestinationDirectory($destinationDir);
        foreach ($progressBar->iterate($urls) as $i => $url) {
            $url = trim($url);
            $image = $this->imageService->getImageByUrl($url);
            $size = 0;
            try {
                $imageFileObject = $this->imageProcessor->processImage($url, $dir);
                if ($imageFileObject) {
                    $totalDownloaded++;
                    $size = $imageFileObject->getSize();
                } else {
                    $isValid = false;
                    $errMsg = 'File url is invalid';
                    
                    $errors++;
                }
            } catch (\Exception $e) {
                $isValid = false;
                $errMsg = sprintf('ErrorCode: %d, Error: %s', $e->getCode(), $e->getMessage());
                
                $errors++;
            }

            $this->imageService->updateImage($url, $size);
            $this->imageService->saveImageValidationLog($image, $isValid, $errMsg);
            $this->imageService->saveImageFileRelation($image, $inputFile);
        }
    
        $progressBar->finish();

        return [
            'totalLines' => $totalLinesCount,
            'totalDownloads' => $totalDownloaded,
            'errors' => $errors
        ];
    }

    /**
     * SymfonyStyle $io - handler for the console output
     */
    protected function showConsoleMessageStart(SymfonyStyle $io, string $inputFileName, ?string $destinationDir): void
    {
        $io->title('====**** Download Images Console App ****====');
        $io->text(sprintf('You\'re about to download images from the file: <info>%s</info>', $inputFileName));
        
        if (!$destinationDir) {
            $io->warning(sprintf('No Destination is provided. Default %s will be used.', ImageProcessor::DESTINATION_DIRECTORY));
        } else {
            $io->text(sprintf('Downloaded files will be saved into %s', $destinationDir));
        }
    }

    /**
     * SymfonyStyle $io - handler for the console output
     */
    protected function showConsoleMessageProcess(SymfonyStyle $io): void
    {
        $io->newLine(2);
        $io->section('Download has started');
        
    }

    /**
     * SymfonyStyle $io - handler for the console output
     * array $tableData - command execution result to display for the user
     */
    protected function showConsoleMessageFinish(SymfonyStyle $io, array $tableData): void
    {
        $io->newLine(2);
        $io->section('Download has completed');
        $io->table([
            'Total Lines in the file', 
            'Total downloaded images', 
            '# of Errors'
        ], 
        [
            [
                $tableData['totalLines'], 
                $tableData['totalDownloads'], 
                $tableData['errors']
            ]
        ]);
    }

    /**
     * SymfonyStyle $io - handler for the console output
     */
    protected function showConsoleMessageError(SymfonyStyle $io, \Throwable $error) 
    {
        $io->newLine(2);
        $io->section('Error:');
        $io->error(['<error>' . $error->getMessage() . '</error>', $error->getTraceAsString()]);
    }
}
