<?php

namespace App\Component\Datagrid\Entity;

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
}