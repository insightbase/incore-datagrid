<?php

namespace App\Component\Datagrid\Menu;

use App\Component\Datagrid\Entity\MenuEntity;
use Nette\Application\UI\Control;

class Menu extends Control
{
    private ?string $icon = null;

    public function __construct(
        public string $label,
        public string $plink,
        private readonly MenuEntity $entity,
    ) {}

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getEntity(): MenuEntity
    {
        return $this->entity;
    }
}
