<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Html;

class BooleanColumnEntity extends ColumnEntity
{
    /**
     * @var ?callable
     */
    private $onClickCallback;

    public function __construct(string $column, string $label, bool $sort = false, SortDirEnum $sortDir = SortDirEnum::ASC)
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->setGetColumnCallback(function (ActiveRow $activeRow): string {
            if ($activeRow[$this->column]) {
                $class = 'ki-filled ki-check-squared text-success';
            } else {
                $class = 'ki-filled ki-cross-square text-danger';
            }

            return Html::el('i')->class($class);
        });
        $this->setGetColumnExportCallback(function (ActiveRow $activeRow): string {
            return (int) $activeRow[$this->column];
        });
        $this->noEscape = true;
    }

    public function getOnClickCallback(): ?callable
    {
        return $this->onClickCallback;
    }

    public function setOnClickCallback(?callable $onClickCallback): self
    {
        $this->onClickCallback = $onClickCallback;

        return $this;
    }
}
