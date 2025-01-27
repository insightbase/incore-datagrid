<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;

class DateTimeColumnEntity extends ColumnEntity
{
    private string $format = 'j.n.Y H:i';

    public function __construct(string $column, string $label, bool $sort = false, SortDirEnum $sortDir = SortDirEnum::ASC)
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->setGetColumnCallback(function (ActiveRow $activeRow): string {
            return $activeRow[$this->column]->format($this->format);
        });
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}
