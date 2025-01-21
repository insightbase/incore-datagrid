<?php

namespace App\Component\Datagrid\Menu;

use Nette\Application\UI\Control;

class Menu extends Control
{
    private ?string $icon = null;

    public function __construct(
        public string $label,
        public string $plink,
    )
    {
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }
}