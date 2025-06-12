<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
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
    private bool $enableExport = false;
    /**
     * @var FilterEntity[]
     */
    private array $filters = [];
    private bool $ordering = false;
    private ?string $defaultOrder = null;
    private ?SortDirEnum $defaultOrderDir = null;
    private ?string $redrawSnippetAfterOrdering = null;

    public function __construct()
    {
        $this->getCountCallback = function (Selection $model): int {
            return $model->count('*');
        };
    }

    public function addFilter(FilterEntity $filterEntity):self
    {
        $this->filters['filter_' . count($this->filters)] = $filterEntity;
        return $this;
    }

    public function addMenu(MenuEntity $menuEntity): self
    {
        $this->menus[] = $menuEntity;

        return $this;
    }

    public function addColumn(ColumnEntity $columnEntity): self
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

    public function isEnableExport(): bool
    {
        return $this->enableExport;
    }

    public function setEnableExport(bool $enableExport = true): self
    {
        $this->enableExport = $enableExport;

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function isOrdering(): bool
    {
        return $this->ordering;
    }

    public function setOrdering(bool $ordering = true): self
    {
        $this->ordering = $ordering;
        return $this;
    }

    public function getDefaultOrder(): ?string
    {
        return $this->defaultOrder;
    }

    public function setDefaultOrder(?string $defaultOrder): self
    {
        $this->defaultOrder = $defaultOrder;
        return $this;
    }

    public function setDefaultOrderDir(?SortDirEnum $defaultOrderDir): DataGridEntity
    {
        $this->defaultOrderDir = $defaultOrderDir;
        return $this;
    }

    public function getDefaultOrderDir(): ?SortDirEnum
    {
        return $this->defaultOrderDir;
    }

    public function getRedrawSnippetAfterOrdering(): ?string
    {
        return $this->redrawSnippetAfterOrdering;
    }

    public function setRedrawSnippetAfterOrdering(?string $redrawSnippetAfterOrdering): self
    {
        $this->redrawSnippetAfterOrdering = $redrawSnippetAfterOrdering;
        return $this;
    }
}
