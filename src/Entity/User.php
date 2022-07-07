<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'user.email.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Compte::class, orphanRemoval: true)]
    private $comptes;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: ModePaiement::class, orphanRemoval: true)]
    #[ORM\OrderBy(["libelle" => "ASC"])]
    private $modePaiements;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Banque::class, orphanRemoval: true)]
    #[ORM\OrderBy(["libelle" => "ASC"])]
    private $banques;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Tiers::class, orphanRemoval: true)]
    #[ORM\OrderBy(["libelle" => "ASC"])]
    private $tiers;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Categorie::class, orphanRemoval: true)]
    private $categories;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
        $this->modePaiements = new ArrayCollection();
        $this->banques = new ArrayCollection();
        $this->tiers = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUrlGravatar(): string
    {
        return (string) 'https://www.gravatar.com/avatar/'.md5(strtolower(trim( $this->email)));
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setProprietaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getProprietaire() === $this) {
                $compte->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModePaiement>
     */
    public function getModePaiementsApi(): array
    {
        $retour = [];
        foreach($this->modePaiements as $temp) {
            $retour[] = $temp->getModePaiementApi();
        }
        return $retour;
    }

    /**
     * @return Collection<int, ModePaiement>
     */
    public function getModePaiements(): Collection
    {
        return $this->modePaiements;
    }

    public function addModePaiement(ModePaiement $modePaiement): self
    {
        if (!$this->modePaiements->contains($modePaiement)) {
            $this->modePaiements[] = $modePaiement;
            $modePaiement->setProprietaire($this);
        }

        return $this;
    }

    public function removeModePaiement(ModePaiement $modePaiement): self
    {
        if ($this->modePaiements->removeElement($modePaiement)) {
            // set the owning side to null (unless already changed)
            if ($modePaiement->getProprietaire() === $this) {
                $modePaiement->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Banque>
     */
    public function getBanques(): Collection
    {
        return $this->banques;
    }

    public function addBanque(Banque $banque): self
    {
        if (!$this->banques->contains($banque)) {
            $this->banques[] = $banque;
            $banque->setProprietaire($this);
        }

        return $this;
    }

    public function removeBanque(Banque $banque): self
    {
        if ($this->banques->removeElement($banque)) {
            // set the owning side to null (unless already changed)
            if ($banque->getProprietaire() === $this) {
                $banque->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tiers>
     */
    public function getTiers(): Collection
    {
        return $this->tiers;
    }

    /**
     * @return Collection<int, Tiers>
     */
    public function getTiersApi(): array
    {
        $retour = [];
        foreach($this->tiers as $temp) {
            $retour[] = $temp->getTiersApi();
        }
        return $retour;
    }

    public function addTier(Tiers $tier): self
    {
        if (!$this->tiers->contains($tier)) {
            $this->tiers[] = $tier;
            $tier->setProprietaire($this);
        }

        return $this;
    }

    public function removeTier(Tiers $tier): self
    {
        if ($this->tiers->removeElement($tier)) {
            // set the owning side to null (unless already changed)
            if ($tier->getProprietaire() === $this) {
                $tier->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategorieApi(): array
    {
        $retour = [];
        foreach($this->categories as $temp) {
            $retour[] = $temp->getCategorieApi();
        }
        return $retour;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setProprietaire($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getProprietaire() === $this) {
                $category->setProprietaire(null);
            }
        }

        return $this;
    }
}
