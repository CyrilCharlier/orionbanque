<?php

namespace App\Entity;

class TiersApi
{
    public function __construct($id, $libelle, $actif)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->actif = $actif;
    }

    public $id;
    public $libelle;
    public $actif;
}
