<?php

namespace App\Component\Datagrid\Entity;

use Nette\Database\Table\Selection;

class DataGridEntity
{
    /**
     * @var array<ColumnEntity>
     */
    private array $columns = [];
    private int $itemsPerPage = 20;
    /**
     * @var array<MenuEntity>
     */
    private array $menus = [];
    /**
     * @var callable
     */
    private $getCountCallback;

    public function __construct()
    {
        $this->getCountCallback = function(Selection $model): int{
            return $model->count('*');
        };
    }

    public function addMenu(MenuEntity $menuEntity):self
    {
        $this->menus[] = $menuEntity;
        return $this;
    }

    public function addColumn(ColumnEntity $columnEntity):self
    {
        $this->columns[] = $columnEntity;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getGetCountCallback(): callable|\Closure
    {
        return $this->getCountCallback;
    }

    public function setGetCountCallback(callable|\Closure $getCountCallback): self
    {
        $this->getCountCallback = $getCountCallback;
        return $this;
    }
}