<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;


    /**
     * @ORM\OneToMany(targetEntity=CommandeInformation::class, mappedBy="produit")
     */
    private $commandeInformations;

    public function __construct()
    {
        $this->commandeInformations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommandeInformations()
    {
        return $this->commandeInformations;
    }

    /**
     * @param mixed $commandeInformations
     */
    public function setCommandeInformations($commandeInformations): void
    {
        $this->commandeInformations = $commandeInformations;
    }

    public function addCommandeInformation(CommandeInformation $commandeInformation): self
    {
        if (!$this->commandeInformations->contains($commandeInformation)) {
            $this->commandeInformations[] = $commandeInformation;
            $commandeInformation->setProduit($this);
        }

        return $this;
    }

    public function removeCommandeInformation(CommandeInformation $commandeInformation): self
    {
        if ($this->commandeInformations->removeElement($commandeInformation)) {
            // set the owning side to null (unless already changed)
            if ($commandeInformation->getProduit() === $this) {
                $commandeInformation->setProduit(null);
            }
        }

        return $this;
    }

}
