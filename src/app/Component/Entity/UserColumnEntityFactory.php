<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;

interface UserColumnEntityFactory
{
    public function create(string $column, string $label, bool $sort = false, SortDirEnum $sortDir = SortDirEnum::ASC):UserColumnEntity;
}