<?php

namespace App\Entity;

class CategorieApi
{
    public function __construct($id, $libelle)
    {
        $this->id = $id;
        $this->libelle = $libelle;
    }

    public $id;
    public $libelle;
}
