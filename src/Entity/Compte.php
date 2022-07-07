<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Compte implements SluggableInterface
{
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $libelle;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comptes')]
    #[ORM\JoinColumn(nullable: false)]
    private $proprietaire;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: Operation::class, orphanRemoval: true)]
    private $operations;

    #[ORM\ManyToOne(targetEntity: Banque::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $banque;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getProprietaire(): ?User
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?User $proprietaire): self
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getSluggableFields(): array
    {
        return ['libelle'];
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setCompte($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getCompte() === $this) {
                $operation->setCompte(null);
            }
        }

        return $this;
    }

    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): self
    {
        $this->banque = $banque;

        return $this;
    }

    public function getData()
    {
        $data = [];
        foreach($this->operations as $operation)
        {
            $data[] = $operation->getOperationApi();
        }
        return $data;
    }

    public function getOperationsNonPointe()
    {
        $data = [];
        foreach($this->operations as $operation)
        {
            if(!$operation->isPointe())
            {
                $data[] = $operation;
            }
        }
        return $data;
    }

    public function getTotalfinal() : float
    {
        $solde = 0.0;
        foreach($this->operations as $operation)
        {
            $solde += $operation->getMontantSigne();
        }
        return $solde;
    }

    public function getAvenir() : float
    {
        $solde = 0.0;
        foreach($this->operations as $operation)
        {
            ($operation->isPointe() ? true : $solde += $operation->getMontantSigne());
        }
        return $solde;
    }

    public function getTotalpointe() : float
    {
        $solde = 0.0;
        foreach($this->operations as $operation)
        {
            ($operation->isPointe() ? $solde += $operation->getMontantSigne() : false);
        }
        return $solde;
    }
}
