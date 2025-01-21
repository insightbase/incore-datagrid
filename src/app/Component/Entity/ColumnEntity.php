<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;

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
    /**
     * @var ?callable
     */
    private $inlineEditCallback = null;
    /**
     * @var callable
     */
    private $getInlineEditIdCallback;
    private ?int $truncate = null;

    public function __construct(
        public string $column,
        public string $label,
        public bool $sort = false,
        public SortDirEnum $sortDir = SortDirEnum::ASC,
    )
    {
        $this->getInlineEditIdCallback = function(ActiveRow $row):int{
            return $row['id'];
        };
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

    public function setEnableSearchGlobal(bool $enableSearchGlobal = true): self
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

    public function getInlineEditCallback(): ?callable
    {
        return $this->inlineEditCallback;
    }

    public function setInlineEditCallback(?callable $inlineEditCallback): self
    {
        $this->inlineEditCallback = $inlineEditCallback;
        return $this;
    }

    public function getGetInlineEditIdCallback(): callable
    {
        return $this->getInlineEditIdCallback;
    }

    public function setGetInlineEditIdCallback(callable $getInlineEditIdCallback): self
    {
        $this->getInlineEditIdCallback = $getInlineEditIdCallback;
        return $this;
    }

    public function getTruncate(): ?int
    {
        return $this->truncate;
    }

    public function setTruncate(?int $truncate): self
    {
        $this->truncate = $truncate;
        return $this;
    }
}