<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Html;

class BooleanColumnEntity extends ColumnEntity
{
    public function __construct(string $column, string $label, bool $sort = false, SortDirEnum $sortDir = SortDirEnum::ASC)
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->setGetRowCallback(function(ActiveRow $activeRow):string{
            if($activeRow[$this->column]){
                $class = 'ki-filled ki-check-squared text-success';
            }else{
                $class = 'ki-filled ki-cross-square text-danger';
            }
            return Html::el('i')->class($class);
        });
    }
}