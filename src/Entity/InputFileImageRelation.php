<?php

namespace App\Entity;

use App\Repository\InputFileImageRelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InputFileImageRelationRepository::class)]
class InputFileImageRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $input_file_id = null;

    #[ORM\Column]
    private ?int $image_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputFileId(): ?int
    {
        return $this->input_file_id;
    }

    public function setInputFileId(int $input_file_id): static
    {
        $this->input_file_id = $input_file_id;

        return $this;
    }

    public function getImageId(): ?int
    {
        return $this->image_id;
    }

    public function setImageId(int $image_id): static
    {
        $this->image_id = $image_id;

        return $this;
    }
}
