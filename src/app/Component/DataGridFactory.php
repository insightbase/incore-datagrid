<?php

namespace App\Component\Datagrid;

use App\Component\Datagrid\Entity\DataGridEntity;
use Nette\Database\Table\Selection;

interface DataGridFactory
{
    public function create(Selection $selection, DataGridEntity $dataGridEntity): DataGrid;
}
