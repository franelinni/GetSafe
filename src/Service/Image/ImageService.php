<?php
namespace App\Service\Image;
use App\Entity\Image;
use App\Entity\InputFile;
use App\Entity\InputFileImageRelation;
use App\Entity\ValidationLog;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ImageService
{   
    protected Image $image;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
       
    }

    public function getImageByUrl(string $url) 
    {
        return $this->entityManager->getRepository(Image::class)
                    ->findOneBy(['url' => $url]) 
                    ?? new Image();
    }

    public function updateImage(string $url, ?int $size): ?Image
    {
        $image = $this->entityManager->getRepository(Image::class)
                ->findOneBy(['url' => $url]) ?? new Image();

        $image->setSize($size);
        $image->setUrl($url);

        $now = new DateTime('now');

        if (!$image->getId()) {
            $image->setUrl($url);
            $image->setCreatedAt($now);
        } else {
            $image->setUpdatedAt($now);
        }

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $image ?? null;
    }

    public function getDestinationDirectory (?string $destDir = ImageProcessor::DESTINATION_DIRECTORY): string 
    {
        $destDir = __DIR__ . '/../../data'. $destDir;
        if (!file_exists($destDir) || !is_readable($destDir)) {
            mkdir($destDir, 0777, true);
        }
        
        return $destDir;
    }

    public function saveImageValidationLog(Image $image, bool $isValid = true, ?string $errorMessage): ?ValidationLog
    {
        $validationLog = $image->getValidationLog() ?? new ValidationLog();
        $now = new DateTime('now');
        
        $validationLog->setIsValid($isValid);
        $validationLog->setImage($image);
        $validationLog->setCreatedAt($now);
        $validationLog->setErrorMessage($errorMessage);

        $this->entityManager->persist($validationLog);
        $this->entityManager->flush();

        return $validationLog ?? null;
    }

    public function saveImageFileRelation(Image $image, InputFile $inputFile): ?InputFileImageRelation
    {
        if (!$image->getId()) {
            return null;
        }

        $fileImageRelationRepository = $this->entityManager->getRepository(InputFileImageRelation::class);         
        $fileImageRelation = $fileImageRelationRepository->findOneBy(
            ['input_file_id' => $inputFile->getId(), 'image_id' => $image->getId()]
        ) ?? new InputFileImageRelation();

        $fileImageRelation->setImageId($image->getId());
        $fileImageRelation->setInputFileId($inputFile->getId());

        $this->entityManager->persist($fileImageRelation);
        $this->entityManager->flush();

        return $fileImageRelation ?? null;
    }
}