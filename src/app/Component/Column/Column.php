<?php

namespace App\Component\Datagrid\Column;

use App\Component\Datagrid\Entity\ColumnEntity;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

class Column extends Control
{
    private bool $enabledSort = true;
    private bool $noEscape = false;

    public function __construct(
        private readonly string       $column,
        private readonly string       $label,
        private readonly ColumnEntity $columnEntity,
        private readonly string       $id,
    )
    {
    }

    public function getInlineEditId(ActiveRow $row):string{
        return ($this->getColumnEntity()->getGetInlineEditIdCallback())($row);
    }

    public function getRowExport(ActiveRow $activeRow):string{
        return ($this->columnEntity->getGetColumnExportCallback())($activeRow);
    }

    public function getRow(ActiveRow $activeRow): string
    {
        return ($this->columnEntity->getColumnCallback())(($this->columnEntity->getGetRowCallback())($activeRow));
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getLabel(): string
    {
        return $this->label;
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

    public function getColumnEntity(): ColumnEntity
    {
        return $this->columnEntity;
    }

    public function getId(): string
    {
        return $this->id;
    }
}