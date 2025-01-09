<?php

namespace App\Component\Datagrid;

use App\Component\Datagrid\Column\Column;
use App\Component\Datagrid\Column\ColumnFactory;
use App\Component\Datagrid\Entity\BooleanColumnEntity;
use App\Component\Datagrid\Entity\DataGridEntity;
use App\Component\Datagrid\Menu\MenuFactory;
use App\UI\Accessory\ParameterBag;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\Attributes\Persistent;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Control;
use Nette\Database\Table\Selection;
use Nette\Localization\Translator;
use Nette\Utils\FileSystem;
use Nette\Utils\Paginator;
use PhpParser\Node\Param;

/**
 * @property-read DataGridTemplate $template
 */
class DataGrid extends Control
{
    /**
     * @var array<Column>
     */
    private array $columns = [];

    #[Persistent]
    public string $sort = '';
    #[Persistent]
    public string $sortDir = '';
    #[Persistent]
    public int $page = 1;
    #[Persistent]
    public string $globalSearch = '';
    private bool $enableGlobalSearch = false;
    private array $menus = [];

    public function __construct(
        private readonly Selection      $selection,
        private readonly Translator     $translator,
        private readonly DataGridEntity $dataGridEntity,
        private readonly ColumnFactory  $columnFactory,
        private readonly MenuFactory    $menuFactory,
        private readonly ParameterBag   $parameterBag,
    )
    {
    }

    private function iconv(array $data):array{
        foreach($data as $key => $value){
            $data[$key] = iconv('UTF-8', 'WINDOWS-1250', $value);
        }
        return $data;
    }

    #[NoReturn] public function handleExport():void{
        $this->init();

        $fileName = 'export-' . time() . '.csv';
        FileSystem::createDir($this->parameterBag->tempDir . '/export');
        $handle = fopen($this->parameterBag->tempDir . '/export/' . $fileName, 'w');
        $columns = [];
        foreach($this->columns as $column){
            $columns[] = $column->getLabel();
        }
        fputcsv($handle, $this->iconv($columns));
        foreach($this->selection as $row){
            $data = [];
            foreach($this->columns as $column){
                $data[] = $column->getRowExport($row);
            }
            fputcsv($handle, $data);
        }
        fclose($handle);

        $response = new FileResponse($this->parameterBag->tempDir . '/export/' . $fileName);
        $this->getPresenter()->sendResponse($response);
    }

    public function handleGlobalSearch(string $search):void
    {
        $this->globalSearch = $search;
        $this->page = 1;
        $this->redrawControl();
    }

    public function handleSetPage(int $page):void
    {
        $this->page = $page;
        $this->redrawControl();
    }

    public function handleSetSort(string $column, string $sortDir):void
    {
        $this->sort = $column;
        $this->sortDir = $sortDir;
        $this->page = 1;
        $this->redrawControl();
    }

    public function init():void
    {
        foreach($this->dataGridEntity->getColumns() as $column){
            $this->columns[] = $columnGrid = $this->columnFactory->create($column->column, $column->label)
                ->setEnabledSort($column->isEnabledSort())
            ;
            if($column->sort && $this->sort === ''){
                $this->sort = $column->column;
                $this->sortDir = $column->sortDir->value;
            }
            if($column->isEnableSearchGlobal()){
                $this->enableGlobalSearch = true;
            }
            if($column->getRowCallback() !== null){
                $columnGrid->setGetRowCallback($column->getRowCallback());
            }
            if($column->getGetRowExportCallback() !== null){
                $columnGrid->setGetRowExportCallback($column->getGetRowExportCallback());
            }
            if($column instanceof BooleanColumnEntity){
                $columnGrid->setNoEscape(true);
            }
        }

        foreach($this->dataGridEntity->getMenus() as $menu){
            $this->menus[] = $this->menuFactory->create($menu->label, $menu->plink)
                ->setIcon($menu->getIcon())
            ;
        }

        $this->initModel();
    }

    private function initModel():void{
        if($this->globalSearch !== ''){
            $query = [];
            $params = [];
            foreach($this->dataGridEntity->getColumns() as $column){
                if($column->isEnableSearchGlobal()){
                    $query[] = $column->column . ' REGEXP ?';
                    $params[] = $this->globalSearch;
                }
            }
            if(!empty($query)){
                if(count($query) === 1){
                    $this->selection->where($query[0], $params[0]);
                }else {
                    $this->selection->where(implode(' OR ', $query), $params);
                }
            }
        }
    }

    public function render():void
    {
        $this->init();

        $itemsCountCallback = $this->dataGridEntity->getGetCountCallback();
        $itemsCount = $itemsCountCallback(clone $this->selection);

        if($this->sort !== ''){
            $this->selection->order($this->sort . ' ' . $this->sortDir);
        }

        $paginator = new Paginator();
        $paginator->setItemCount($itemsCount);
        $paginator->setItemsPerPage($this->dataGridEntity->getItemsPerPage());
        $paginator->setPage($this->page);
        $this->selection->limit($paginator->getLength(), $paginator->getOffset());

        $this->template->selection = $this->selection;
        $this->template->columns = $this->columns;
        $this->template->sort = $this->sort;
        $this->template->sortDir = $this->sortDir;
        $this->template->paginator = $paginator;
        $this->template->isEnabledGlobalSearch = $this->enableGlobalSearch;
        $this->template->menus = $this->menus;
        $this->template->globalSearchText = $this->globalSearch;

        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . '/dataGrid.latte');
        $this->template->render();
    }
}