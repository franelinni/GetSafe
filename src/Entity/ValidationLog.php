<?php

namespace App\Entity;

use App\Repository\ValidationLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationLogRepository::class)]
class ValidationLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?InputFile $input_file_id = null;

    #[ORM\OneToOne(inversedBy: 'log', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $image_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_valid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $error_mesage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputFileId(): ?InputFile
    {
        return $this->input_file_id;
    }

    public function setInputFileId(InputFile $input_file_id): static
    {
        $this->input_file_id = $input_file_id;

        return $this;
    }

    public function getImageId(): ?Image
    {
        return $this->image_id;
    }

    public function setImageId(Image $image_id): static
    {
        $this->image_id = $image_id;

        return $this;
    }

    public function isIsValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setIsValid(?bool $is_valid): static
    {
        $this->is_valid = $is_valid;

        return $this;
    }

    public function getErrorMesage(): ?string
    {
        return $this->error_mesage;
    }

    public function setErrorMesage(?string $error_mesage): static
    {
        $this->error_mesage = $error_mesage;

        return $this;
    }
}
