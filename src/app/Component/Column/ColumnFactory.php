<?php

namespace App\Component\Datagrid\Column;

use App\Component\Datagrid\Entity\ColumnEntity;

interface ColumnFactory
{
    public function create(string $column, string $label, ColumnEntity $columnEntity, string $id): Column;
}
