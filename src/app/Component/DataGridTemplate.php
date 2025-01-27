<?php

namespace App\Component\Datagrid;

use App\Component\Datagrid\Column\Column;
use App\Component\Datagrid\Menu\Menu;
use App\Model\Entity\ModuleEntity;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\Paginator;

class DataGridTemplate extends Template
{
    public Selection $selection;

    /**
     * @var array<Column>
     */
    public array $columns;
    public string $sort;
    public string $sortDir;
    public Paginator $paginator;
    public bool $isEnabledGlobalSearch;

    /**
     * @var array<Menu>
     */
    public array $menus;
    public string $globalSearchText;
    public bool $isEnabledExport;

    /**
     * @var ModuleEntity
     */
    public ActiveRow $module;
    public Presenter $presenter;
}
