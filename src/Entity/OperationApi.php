<?php

namespace App\Entity;

class OperationApi
{
    public function __construct(Operation $o)
    {
        $this->id = $o->getId();
        $this->date = $o->getDate()->format('Y-m-d');
        $this->libelle = $o->getLibelle();
        $this->tiers = $o->getTiers() ? $o->getTiers()->getLibelle() : '';
        $this->tiersId = $o->getTiers() ? $o->getTiers()->getId() : '';
        $this->modePaiement = $o->getModePaiement()->getLibelle();
        $this->modePaiementId = $o->getModePaiement()->getId();
        $this->categorie = $o->getCategorie()->getLibelle();
        $this->categorieId = $o->getCategorie()->getId();
        $this->montant = $o->getMontant();
        $this->pointe = $o->isPointe();
    }

    public $id;
    public $date;
    public $libelle;
    public $tiers;
    public $tiersId;
    public $modePaiement;
    public $modePaiementId;
    public $categorie;
    public $categorieId;
    public $montant;
    public $pointe;
}
