<?php

namespace App\Entity;

use App\Repository\ResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResetTokenRepository::class)]
class ResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?\DateTime $expiry = null;

    #[ORM\Column]
    private ?bool $used = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiry(): ?\DateTime
    {
        return $this->expiry;
    }

    public function setExpiry(\DateTime $expiry): static
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
