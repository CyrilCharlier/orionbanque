<?php

namespace App\Entity;

class OperationApi
{
    public function __construct($id, $date, $libelle, $tiers, $modePaiement, $categorie, $montant, $pointe)
    {
        $this->id = $id;
        $this->date = $date->format('Y-m-d');
        $this->libelle = $libelle;
        $this->tiers = $tiers;
        $this->modePaiement = $modePaiement;
        $this->categorie = $categorie;
        $this->montant = $montant;
        $this->pointe = $pointe;
    }

    public $id;
    public $date;
    public $libelle;
    public $tiers;
    public $modePaiement;
    public $categorie;
    public $montant;
    public $pointe;
}
