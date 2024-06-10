<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Assert\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:'description est obligatoire')]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank (message:'description détaillé est obligatoire')]
    private ?string $descriptionDetaille = null;

    #[ORM\Column]
    #[Assert\NotBlank (message:'prix est obligatoire')]
    private ?int $prix = null;

   

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:'couleur est obligatoire')]
    private ?string $couleur = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
  
    private ?Categories $category = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Detail::class)]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
         
    }

    //  public function __construct($prix,$couleur)
    //  {
    //      $this->details = new ArrayCollection();
    //      $this->prix=$prix;
    //      $this->couleur=$couleur;
    //      $this->description='default description'  ; 
    //   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionDetaille(): ?string
    {
        return $this->descriptionDetaille;
    }

    public function setDescriptionDetaille(string $descriptionDetaille): static
    {
        $this->descriptionDetaille = $descriptionDetaille;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

   

  

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Detail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setProduit($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getProduit() === $this) {
                $detail->setProduit(null);
            }
        }

        return $this;
    }
}
