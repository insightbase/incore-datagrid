<?php

namespace App\Component\Datagrid\Entity;

use Nette\Database\Table\ActiveRow;

class MenuEntity
{
    private ?string $icon = null;
    /** ACL resource for the visibility check, null = module of the grid */
    private ?string $resource = null;

    /**
     * @var callable
     */
    private $showCallback;
    private array $params = [];
    /**
     * @var ?callable
     */
    private $customLinkCallback = null;

    public function __construct(
        public string $label,
        public string $plink,
    ) {
        $this->showCallback = function (ActiveRow $row): bool {
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

    public function getCustomLinkCallback(): ?callable
    {
        return $this->customLinkCallback;
    }

    public function setCustomLinkCallback(?callable $customLinkCallback): self
    {
        $this->customLinkCallback = $customLinkCallback;
        return $this;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    /** checks the privilege (plink) against another ACL resource than the module of the grid */
    public function setResource(?string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }
}
