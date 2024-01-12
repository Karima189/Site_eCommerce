<?php

namespace App\Entity;

use App\Repository\LogosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogosRepository::class)]
class Logos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image_logo = null;

    #[ORM\Column(length: 255)]
    private ?string $description_logo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageLogo(): ?string
    {
        return $this->image_logo;
    }

    public function setImageLogo(string $image_logo): static
    {
        $this->image_logo = $image_logo;

        return $this;
    }

    public function getDescriptionLogo(): ?string
    {
        return $this->description_logo;
    }

    public function setDescriptionLogo(string $description_logo): static
    {
        $this->description_logo = $description_logo;

        return $this;
    }
}
