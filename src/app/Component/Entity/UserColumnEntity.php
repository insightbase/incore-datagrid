<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;

class UserColumnEntity extends ColumnEntity
{
    public function __construct(
        string $column,
        string $label,
        bool $sort = false,
        SortDirEnum $sortDir = SortDirEnum::ASC
    )
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->setGetRowCallback(function(ActiveRow $row) use ($column):ActiveRow{
            return $row->ref('user', $column);
        });
        $this->templateFile = 'user.latte';
    }


}