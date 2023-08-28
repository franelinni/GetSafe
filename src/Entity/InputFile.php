<?php

namespace App\Entity;

use App\Repository\InputFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InputFileRepository::class)]
class InputFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    #[ORM\Column(nullable: true)]
    private ?int $n_lines = null;

    #[ORM\Column(length: 255)]
    private ?string $destination_folder = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToOne(mappedBy: 'inputFileId', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getNLines(): ?int
    {
        return $this->n_lines;
    }

    public function setNLines(?int $n_lines): static
    {
        $this->n_lines = $n_lines;

        return $this;
    }

    public function getDestinationFolder(): ?string
    {
        return $this->destination_folder;
    }

    public function setDestinationFolder(string $destination_folder): static
    {
        $this->destination_folder = $destination_folder;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTime("now");

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): static
    {
        // set the owning side of the relation if necessary
        if ($image->getInputFileId() !== $this) {
            $image->setInputFileId($this);
        }

        $this->image = $image;

        return $this;
    }
}
