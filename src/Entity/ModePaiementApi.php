<?php

namespace App\Entity;

class ModePaiementApi
{
    public function __construct($id, $libelle, $actif, $debit)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->actif = $actif;
        $this->debit = $debit;
    }

    public $id;
    public $libelle;
    public $actif;
    public $debit;
}
