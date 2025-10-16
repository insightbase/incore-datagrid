<?php

namespace App\Component\Datagrid\Column;

use Nette\Database\Table\ActiveRow;

class Template extends \Nette\Bridges\ApplicationLatte\Template
{
    public Column $column;
    public ActiveRow $activeRow;
    public ActiveRow $activeRowOrig;
}