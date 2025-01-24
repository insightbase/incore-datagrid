<?php

namespace App\Component\Datagrid\Menu;

use App\Component\Datagrid\Entity\MenuEntity;

interface MenuFactory
{
    public function create(string $label, string $plink, MenuEntity $entity):Menu;
}