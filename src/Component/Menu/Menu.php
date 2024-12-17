<?php

namespace App\Component\Datagrid\Menu;

use Nette\Application\UI\Control;

class Menu extends Control
{
    public function __construct(
        public string $label,
        public string $plink,
    )
    {
    }
}