<?php

namespace App\Entity;

class OperationApi
{
    public function __construct($id, $date, $libelle, $tiers, $modePaiement, $categorie, $montant, $pointe, $tiersId, $modePaiementId, $categorieId)
    {
        $this->id = $id;
        $this->date = $date->format('Y-m-d');
        $this->libelle = $libelle;
        $this->tiers = $tiers;
        $this->tiersId = $tiersId;
        $this->modePaiement = $modePaiement;
        $this->modePaiementId = $modePaiementId;
        $this->categorie = $categorie;
        $this->categorieId = $categorieId;
        $this->montant = $montant;
        $this->pointe = $pointe;
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
