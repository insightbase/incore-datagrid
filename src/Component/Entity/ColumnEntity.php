<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;

class ColumnEntity
{
    private bool $enableSearchGlobal = false;

    public function __construct(
        public string $column,
        public string $label,
        public bool $sort = false,
        public SortDirEnum $sortDir = SortDirEnum::ASC,
    )
    {
    }

    public function isEnableSearchGlobal(): bool
    {
        return $this->enableSearchGlobal;
    }

    public function setEnableSearchGlobal(bool $enableSearchGlobal): self
    {
        $this->enableSearchGlobal = $enableSearchGlobal;
        return $this;
    }
}