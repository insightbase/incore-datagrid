<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\DefaultIconEnum;

class DeleteMenuEntity extends MenuEntity
{
    public function __construct(string $label, string $plink)
    {
        parent::__construct($label, $plink);
        $this->setIcon(DefaultIconEnum::Delete->value);
    }
}