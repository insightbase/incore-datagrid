<?php

namespace App\Component\Datagrid;

use Nette\Database\Table\ActiveRow;

interface InlineEdit
{
    public function isEnabled(ActiveRow $row):bool;
}