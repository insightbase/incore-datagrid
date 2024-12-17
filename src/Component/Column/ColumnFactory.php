<?php

namespace App\Component\Datagrid\Column;

interface ColumnFactory
{
    public function create(string $column, string $label):Column;
}