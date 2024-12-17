<?php

namespace App\Component\Datagrid\Menu;

interface MenuFactory
{
    public function create(string $label, string $plink):Menu;
}