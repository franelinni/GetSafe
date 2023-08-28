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
    private ?Image $image = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $retries = null;

    #[ORM\Column(length: 255)]
    private ?string $log_message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $error_message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $started_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $downloaded_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $restarted_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRetries(): ?int
    {
        return $this->retries;
    }

    public function setRetries(int $retries): static
    {
        $this->retries = $retries;

        return $this;
    }

    public function getLogMessage(): ?string
    {
        return $this->log_message;
    }

    public function setLogMessage(string $log_message): static
    {
        $this->log_message = $log_message;

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

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeInterface $started_at): static
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getDownloadedAt(): ?\DateTimeInterface
    {
        return $this->downloaded_at;
    }

    public function setDownloadedAt(?\DateTimeInterface $downloaded_at): static
    {
        $this->downloaded_at = $downloaded_at;

        return $this;
    }

    public function getRestartedAt(): ?\DateTimeInterface
    {
        return $this->restarted_at;
    }

    public function setRestartedAt(?\DateTimeInterface $restarted_at): static
    {
        $this->restarted_at = $restarted_at;

        return $this;
    }
}
