<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\OneToOne(targetEntity=Livraison::class,
     *     mappedBy="commande", cascade={"persist", "remove"})
     */
    private $livraison;


    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->commandeInformations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\OneToMany(targetEntity=CommandeInformation::class, mappedBy="commande",fetch="EAGER")
     */
    private $commandeInformations;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @ORM\Column(type="float")
     */
    private $totale;

    /**
     * @return mixed
     */
    public function getTotale()
    {
        return $this->totale;
    }

    /**
     * @param mixed $totale
     */
    public function setTotale($totale): void
    {
        $this->totale = $totale;
    }


    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(Livraison $livraison): self
    {
        // set the owning side of the relation if necessary
        if ($livraison->getCommande() !== $this) {
            $livraison->setCommande($this);
        }

        $this->livraison = $livraison;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $commandeInformation->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeInformation(CommandeInformation $commandeInformation): self
    {
        if ($this->commandeInformations->removeElement($commandeInformation)) {
            // set the owning side to null (unless already changed)
            if ($commandeInformation->getCommande() === $this) {
                $commandeInformation->setCommande(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }




}
