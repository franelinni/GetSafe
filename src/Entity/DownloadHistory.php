<?php

namespace App\Entity;

use App\Repository\DownloadHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DownloadHistoryRepository::class)]
class DownloadHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $image_id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $status = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $error_message = null;

    #[ORM\Column]
    private ?int $n_retries = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_retry_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $downloaded_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }

    public function setErrorMessage(?string $error_message): static
    {
        $this->error_message = $error_message;

        return $this;
    }

    public function getNRetries(): ?int
    {
        return $this->n_retries;
    }

    public function setNRetries(int $n_retries): static
    {
        $this->n_retries = $n_retries;

        return $this;
    }

    public function getLastRetryAt(): ?\DateTimeInterface
    {
        return $this->last_retry_at;
    }

    public function setLastRetryAt(\DateTimeInterface $last_retry_at): static
    {
        $this->last_retry_at = $last_retry_at;

        return $this;
    }

    public function getDownloadedAt(): ?\DateTimeImmutable
    {
        return $this->downloaded_at;
    }

    public function setDownloadedAt(?\DateTimeImmutable $downloaded_at): static
    {
        $this->downloaded_at = $downloaded_at;

        return $this;
    }
}
