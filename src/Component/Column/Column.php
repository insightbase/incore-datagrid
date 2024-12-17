<?php

namespace App\Component\Datagrid\Column;

use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

class Column extends Control
{
    /**
     * @var callable
     */
    private $getRowCallback;

    public function __construct(
        private readonly string $column,
        private readonly string $label,
    )
    {
        $this->getRowCallback = function(ActiveRow $activeRow):string{
            return (string)$activeRow[$this->column];
        };
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
}