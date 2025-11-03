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
                $input = Html::el('input')->class('checkbox')->type('checkbox')->checked($activeRow[$this->column]);
            } else {
                $input = Html::el('input')->class('checkbox')->type('checkbox');
            }

            return Html::el('span')->class('switch')->addHtml(
                $input
            );
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
