<?php
namespace App\Command;

use App\Service\Image\ImageDownloaderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:get-images',
    description: 'Download images from a list of URLs and store them locally.',
)]
class GetImagesCommand extends Command
{
    protected ImageDownloaderService $imageDownloaderService;

    public function __construct(ImageDownloaderService $imageDownloaderService)
    {
        parent::__construct();
        $this->imageDownloaderService = $imageDownloaderService;
    }

    protected function configure(): void
    {
        $this->setName('download:images')
            ->setDescription('Download images from a list of URLs and store them locally.')
            ->setHelp('This command allows you to download images from URLs and store them locally.')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'File with list of URLs. Can have following formats: .txt, .csv')
            ->addOption('destination', 'd', InputOption::VALUE_REQUIRED, 'Where should script save downloaded images? If none is provided, default <code>data/storage</code> will be used.');
            //todo: options for amount of threads, retries, logging
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $inputFile = $input->getArgument('inputFile');
        $destinationDir = $input->getOption('destination');

        $io->title('====**** Download Images Console App ****====');
        if (!$inputFile) {
            $io->warning('No Input file is provided. Please, specify file name with urls list');
        }

        $io->text(sprintf('You\'re about to download images from the file: <info>%s</info>', $inputFile));
        if (!$destinationDir) {
            $io->warning('No Destination is provided. Default <code>data/storage</code> will be used.');
        } else {
            $io->text(sprintf('Downloaded files will be saved into <info>%s</info>', $destinationDir));
        }
        
        try {
            $io->newLine(2);
            $io->section('Download has started');
            $file = new \SplFileInfo($inputFile);
            $result = $this->imageDownloaderService->getImages($file, $destinationDir, $output);
            
        } catch (\Exception $e) {
            $io->section('Error:');
            $io->error(['<error>' . $e->getMessage() . '</error>', $e->getTraceAsString()]);
            return Command::FAILURE;
        }

        $io->newLine(2);
        $io->section('Download has completed');
        $io->table([
            'Total Lines in the file', 
            'Total downloaded images', 
            '# of Errors'
        ], 
        [
            [
                $result['totalLines'], 
                $result['totalDownloads'], 
                $result['errors']
            ]
        ]);
        return Command::SUCCESS;
    }
}
