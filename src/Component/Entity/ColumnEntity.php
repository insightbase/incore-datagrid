<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;

class ColumnEntity
{
    private bool $enableSearchGlobal = false;
    /**
     * @var null|callable
     */
    private $getRowCallback = null;
    /**
     * @var null|callable
     */
    private $getRowExportCallback = null;
    private bool $enabledSort = true;

    public function __construct(
        public string $column,
        public string $label,
        public bool $sort = false,
        public SortDirEnum $sortDir = SortDirEnum::ASC,
    )
    {
    }

    public function disableSort():self
    {
        $this->enabledSort = false;
        return $this;
    }

    public function enableSort():self
    {
        $this->enabledSort = true;
        return $this;
    }

    public function getRowCallback():?callable
    {
        return $this->getRowCallback;
    }

    public function setGetRowCallback(callable $getRowCallback):self{
        $this->getRowCallback = $getRowCallback;
        return $this;
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

    public function isEnabledSort(): bool
    {
        return $this->enabledSort;
    }

    public function getGetRowExportCallback(): ?callable
    {
        return $this->getRowExportCallback;
    }

    public function setGetRowExportCallback(?callable $getRowExportCallback): self
    {
        $this->getRowExportCallback = $getRowExportCallback;
        return $this;
    }
}