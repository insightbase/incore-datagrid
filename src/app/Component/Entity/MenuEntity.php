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
    private array $params = [];

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

    public function getParams(): array
    {
        return $this->params;
    }

    public function addParam(string $key, string $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }
}