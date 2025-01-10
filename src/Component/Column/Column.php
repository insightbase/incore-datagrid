<?php

namespace App\Component\Datagrid\Column;

use App\Component\Datagrid\Entity\ColumnEntity;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

class Column extends Control
{
    /**
     * @var callable
     */
    protected $getRowCallback;
    /**
     * @var callable
     */
    protected $getRowExportCallback;
    private bool $enabledSort = true;
    private bool $noEscape = false;

    public function __construct(
        private readonly string       $column,
        private readonly string       $label,
        private readonly ColumnEntity $columnEntity,
        private readonly string       $id,
    )
    {
        $this->getRowCallback = function(ActiveRow $activeRow):string{
            return (string)$activeRow[$this->column];
        };
        $this->getRowExportCallback = function(ActiveRow $activeRow):string{
            return (string)$activeRow[$this->column];
        };
    }

    public function getRowExport(ActiveRow $activeRow):string{
        $callback = $this->getRowExportCallback;
        return $callback($activeRow);
    }

    public function getRow(ActiveRow $activeRow): string
    {
        $callback = $this->getRowCallback;
        return $callback($activeRow);
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setGetRowCallback(callable $getRowCallback): self
    {
        $this->getRowCallback = $getRowCallback;
        return $this;
    }

    public function isEnabledSort(): bool
    {
        return $this->enabledSort;
    }

    public function setEnabledSort(bool $enabledSort): self
    {
        $this->enabledSort = $enabledSort;
        return $this;
    }

    public function isNoEscape(): bool
    {
        return $this->noEscape;
    }

    public function setNoEscape(bool $noEscape): self
    {
        $this->noEscape = $noEscape;
        return $this;
    }

    public function getGetRowExportCallback(): callable|\Closure
    {
        return $this->getRowExportCallback;
    }

    public function setGetRowExportCallback(callable|\Closure $getRowExportCallback): void
    {
        $this->getRowExportCallback = $getRowExportCallback;
    }

    public function getColumnEntity(): ColumnEntity
    {
        return $this->columnEntity;
    }

    public function getId(): string
    {
        return $this->id;
    }
}