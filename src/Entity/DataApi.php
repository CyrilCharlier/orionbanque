<?php

namespace App\Entity;

class DataApi
{
    public function __construct($data, $result)
    {
        $this->data = $data;
        $this->total = (is_countable($data) ? count($data) : 1);
        $this->result = $result;
    }

    public $data;
    public $total;
    public $result;
}
