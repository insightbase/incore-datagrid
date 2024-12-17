<?php

namespace App\Component\Datagrid\Entity;

class MenuEntity
{
    public function __construct(
        public string $label,
        public string $plink,
    )
    {
    }
}