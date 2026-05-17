<?php

namespace App\Entity;

use App\Repository\ImageMenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageMenuRepository::class)]
#[ORM\Table(name: 'image_menu')]
#[ORM\Index(name: 'idx_id_menu', columns: ['id_menu'])]
#[ORM\Index(name: 'idx_id_menu_ordre', columns: ['id_menu', 'ordre'])]
class ImageMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_image')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'images', cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'id_menu', nullable: false, onDelete: 'CASCADE')]
    private ?Menu $menu = null;

    #[ORM\Column(type: 'string', length: 500)]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $alt = null;

    #[ORM\Column(type: 'integer')]
    private int $ordre = 0;

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;
        return $this;
    }

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
    }
}