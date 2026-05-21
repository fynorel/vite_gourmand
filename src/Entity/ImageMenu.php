<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'image_menu')]
class ImageMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_image')]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    #[ORM\Column]
    private ?int $ordre = 0;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(name: 'id_menu', referencedColumnName: 'id_menu')]
    private ?Menu $menu = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
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
}
