<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Detail::class)]
    private Collection $details;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: AdresseCommande::class)]
    private Collection $adresseCommandes;

    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->adresseCommandes = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

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
            $detail->setCommande($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getCommande() === $this) {
                $detail->setCommande(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, AdresseCommande>
     */
    public function getAdresseCommandes(): Collection
    {
        return $this->adresseCommandes;
    }

    public function addAdresseCommande(AdresseCommande $adresseCommande): static
    {
        if (!$this->adresseCommandes->contains($adresseCommande)) {
            $this->adresseCommandes->add($adresseCommande);
            $adresseCommande->setCommande($this);
        }

        return $this;
    }

    public function removeAdresseCommande(AdresseCommande $adresseCommande): static
    {
        if ($this->adresseCommandes->removeElement($adresseCommande)) {
            // set the owning side to null (unless already changed)
            if ($adresseCommande->getCommande() === $this) {
                $adresseCommande->setCommande(null);
            }
        }

        return $this;
    }

}
