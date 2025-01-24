<?php

namespace App\Component\Datagrid\Entity;

use Nette\Database\Table\ActiveRow;

class MenuEntity
{
    private ?string $icon = null;
    /**
     * @var callable
     */
    private $showCallback;

    public function __construct(
        public string $label,
        public string $plink,
    )
    {
        $this->showCallback = function(ActiveRow $row):bool{
            return true;
        };
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

    public function getShowCallback(): callable
    {
        return $this->showCallback;
    }

    public function setShowCallback(callable $showCallback): self
    {
        $this->showCallback = $showCallback;
        return $this;
    }
}