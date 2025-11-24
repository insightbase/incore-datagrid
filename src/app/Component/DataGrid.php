<?php

namespace App\Component\Datagrid;

use App\Component\Datagrid\Column\Column;
use App\Component\Datagrid\Column\ColumnFactory;
use App\Component\Datagrid\Dto\ReturnInlineEditCallback;
use App\Component\Datagrid\Entity\BooleanColumnEntity;
use App\Component\Datagrid\Entity\DataGridEntity;
use App\Component\Datagrid\Exception\FilterHasNotSetChangeCallbackException;
use App\Component\Datagrid\Menu\Custom\CustomMenu;
use App\Component\Datagrid\Menu\Menu;
use App\Component\Datagrid\Menu\MenuFactory;
use App\Component\EditorJs\EditorJsFacade;
use App\Component\Image\ImageControl;
use App\Component\Image\ImageControlFactory;
use App\Core\Admin\Enum\DefaultSnippetsEnum;
use App\Model\Admin\Language;
use App\Model\Admin\Module;
use App\UI\Accessory\Admin\Form\Form;
use App\UI\Accessory\Admin\Form\FormFactory;
use App\UI\Accessory\ParameterBag;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\Attributes\Persistent;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\Database\Table\Selection;
use Nette\Localization\Translator;
use Nette\Utils\FileSystem;
use Nette\Utils\Paginator;

/**
 * @property DataGridTemplate $template
 */
class DataGrid extends Control
{
    #[Persistent]
    public string $sort = '';
    #[Persistent]
    public string $sortDir = '';
    #[Persistent]
    public int $page = 1;
    #[Persistent]
    public string $globalSearch = '';

    /**
     * @var array<Column>
     */
    private array $columns = [];
    private bool $enableGlobalSearch = false;
    /**
     * @var Menu[]
     */
    private array $menus = [];
    private bool $isInit = false;
    #[Persistent]
    public array $filter = [];
    private string $columnId = 'default';

    public function __construct(
        private readonly Selection           $selection,
        private readonly Translator          $translator,
        private readonly DataGridEntity      $dataGridEntity,
        private readonly ColumnFactory       $columnFactory,
        private readonly MenuFactory         $menuFactory,
        private readonly ParameterBag        $parameterBag,
        private readonly Module              $moduleModel,
        private readonly ImageControlFactory $imageControlFactory,
        private readonly FormFactory         $formFactory,
        private readonly Language            $languageModel,
    ) {}

    public function handleSortValue(string $values):void{
        $iterator = 1;
        foreach(explode(',', $values) as $id){
            $this->selection->get($id)->update(['position' => $iterator]);
            $iterator++;
        }
        $this->redrawControl('dataGrid');
        if($this->dataGridEntity->getRedrawSnippetAfterOrdering() !== null){
            $this->getPresenter()->redrawControl($this->dataGridEntity->getRedrawSnippetAfterOrdering());
        }
    }

    public function handleRefreshModal(string $columnId, int $id):void{
        $this->init();
        $column = $this->getColumnById($columnId);
        $this->getComponent('formInlineEdit-' . $columnId)->setDefaults($column->getColumnEntity()->inlineEdit->getDefaults($id) + ['id' => $id, 'columnId' => $columnId]);
        $this->columnId = $columnId;
        $this->template->inlineModalHeader = $column->getColumnEntity()->inlineEdit->getHeader($id);
        $this->redrawControl('inlineModalBody');
        $this->redrawControl('inlineModalHeader');
    }

    protected function createComponentFormInlineEdit():Multiplier{
        return new Multiplier(function(string $columnId):Form{
            $this->init();

            $column = $this->getColumnById($columnId);
            if($columnId !== 'default' && $column->getColumnEntity()->inlineEdit->getForm() !== null){
                $form = $column->getColumnEntity()->inlineEdit->getForm();
            }else {
                $form = $this->formFactory->create();
                $form->addTextArea('value');
            }

            $form->sendByAjax();
            $form->addHidden('columnId');
            $form->addSubmit('send', $this->translator->translate('input_update'))
                ->getControlPrototype()->addAttributes(['data-modal-dismiss' => true]);
            $form->addHidden('id');

            $form->onSuccess[] = function(Form $form, array $values):void{
                $this->init();
                $column = $this->getColumnById($values['columnId']);
                $column->getColumnEntity()->inlineEdit->getOnSuccessCallback()($values);
                $this->redrawControl('dataGrid');
                $this->redrawControl('inlineModalBody');
            };
            return $form;
        });
    }

    public function handleChangeFilter(string $name, string $value):void
    {
        $this->init();
        $this->filter[$name] = $value;
        $callback = $this->dataGridEntity->getFilters()[$name]->getOnChangeCallback();
        $callback($this->selection, $value);
        $this->redrawControl();
    }

    protected function createComponentImage():ImageControl{
        return $this->imageControlFactory->create();
    }

    #[NoReturn]
    public function handleExport(): void
    {
        $this->init();

        $fileName = 'export-'.time().'.csv';
        FileSystem::createDir($this->parameterBag->tempDir.'/export');
        $handle = fopen($this->parameterBag->tempDir.'/export/'.$fileName, 'w');
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column->getLabel();
        }
        fputcsv($handle, $this->iconv($columns), ",", '"', "\\");
        foreach ($this->selection as $row) {
            $data = [];
            foreach ($this->columns as $column) {
                $data[] = $column->getRowExport($row);
            }
            fputcsv($handle, $data, ",", '"', "\\");
        }
        fclose($handle);

        $response = new FileResponse($this->parameterBag->tempDir.'/export/'.$fileName);
        $this->getPresenter()->sendResponse($response);
    }

    public function handleGlobalSearch(string $search): void
    {
        $this->globalSearch = $search;
        $this->page = 1;
        $this->redrawControl('dataGrid');
    }

    public function handleSetPage(int $page): void
    {
        $this->page = $page;
        $this->redrawControl('dataGrid');
    }

    public function handleSetSort(string $column, string $sortDir): void
    {
        $this->sort = $column;
        $this->sortDir = $sortDir;
        $this->page = 1;
        $this->redrawControl('dataGrid');
    }

    public function handleInlineEdit(string $columnId, string $id, string $value): void
    {
        $this->init();
        $column = $this->getColumnById($columnId);
        if (null !== $column && null !== $column->getColumnEntity()->getInlineEditCallback()) {
            try {
                $dto = ($column->getColumnEntity()->getInlineEditCallback())($id, $value);
                if (!$dto instanceof ReturnInlineEditCallback) {
                    $dto = new ReturnInlineEditCallback();
                }
                if ($dto->redraw) {
                    $this->redrawControl('dataGrid');
                }
                if ($dto->redrawOneColumn) {
                    $this->template->selection = $this->selection->where('id', $id);
                    $this->template->columns = [$column];

                    $this->redrawControl('bodyArea');
                    $this->redrawControl(sprintf('column-%s-%s', $id, $columnId));
                }
            } catch (\Exception $e) {
                $this->getPresenter()->flashMessage($e->getMessage(), 'error');
            }
        } else {
            $this->getPresenter()->flashMessage($this->translator->translate('flash_badLink'), 'error');
        }
        $this->getPresenter()->redrawControl(DefaultSnippetsEnum::Flashes->value);
    }

    public function handleColumnClick(string $columnId, int $id): void
    {
        $this->init();

        $column = $this->getColumnById($columnId);
        if (null !== $column && $column->getColumnEntity() instanceof BooleanColumnEntity) {
            try {
                ($column->getColumnEntity()->getOnClickCallback())($id);
                $this->redrawControl('dataGrid');
            } catch (\Exception $e) {
                $this->getPresenter()->flashMessage($e->getMessage(), 'error');
            }
        } else {
            $this->getPresenter()->flashMessage($this->translator->translate('flash_badLink'), 'error');
        }
        $this->getPresenter()->redrawControl('flashes');
    }

    public function init(): void
    {
        if (!$this->isInit) {
            $iterator = 1;
            foreach($this->dataGridEntity->getCustomMenu() as $customMenu){
                $this->addComponent($customMenu, 'customMenu' . $iterator);
                $iterator++;
            }

            foreach ($this->dataGridEntity->getColumns() as $column) {
                $this->columns[] = $columnGrid = $this->columnFactory->create($column->column, $column->label, $column, 'column_'.count($this->columns))
                    ->setEnabledSort($column->isEnabledSort())->setParent($this)
                ;
                if ($column->sort && '' === $this->sort) {
                    $this->sort = $column->column;
                    $this->sortDir = $column->sortDir->value;
                }
                if ($column->isEnableSearchGlobal()) {
                    $this->enableGlobalSearch = true;
                }
            }

            foreach ($this->dataGridEntity->getMenus() as $menu) {
                $this->menus[] = $this->menuFactory->create($menu->label, $menu->plink, $menu)
                    ->setIcon($menu->getIcon())
                ;
            }

            foreach($this->dataGridEntity->getFilters() as $filter){
                if($filter->getOnChangeCallback() === null){
                    throw new FilterHasNotSetChangeCallbackException($filter->getLabel());
                }
            }

            $this->initModel();

            $this->isInit = true;
        }
    }

    public function render(): void
    {
        $this->init();

        $itemsCountCallback = $this->dataGridEntity->getGetCountCallback();
        $itemsCount = $itemsCountCallback(clone $this->selection);

        if ('' !== $this->sort) {
            $this->selection->order($this->sort.' '.$this->sortDir);
        }elseif($this->dataGridEntity->getDefaultOrder() !== null){
            $orderString = $this->dataGridEntity->getDefaultOrder();
            if($this->dataGridEntity->getDefaultOrderDir() !== null){
                $orderString .= ' '.$this->dataGridEntity->getDefaultOrderDir()->value;
            }
            $this->selection->order($orderString);
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
        $this->template->isEnabledExport = $this->dataGridEntity->isEnableExport();
        $this->template->module = $this->moduleModel->getByPresenter($this->getPresenter()->getName());
        $this->template->presenter = $this->getPresenter();
        $this->template->dataGridEntity = $this->dataGridEntity;
        $this->template->filter = $this->filter;
        $this->template->basicFormFile = $this->parameterBag->rootDir . '/vendor/incore/core/src/app/UI/Accessory/Admin/Form/basic-form.latte';
        foreach ($this->languageModel->getToTranslate() as $language) {
            if ($language->is_default) {
                $this->template->defaultLanguage = $language;
                break;
            }
        }
        $this->template->control = $this;
        $this->template->columnId = $this->columnId;

        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__.'/dataGrid.latte');
        $this->template->render();
    }

    private function iconv(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = iconv('UTF-8', 'WINDOWS-1250', $value);
        }

        return $data;
    }

    private function getColumnById(string $id): ?Column
    {
        foreach ($this->columns as $column) {
            if ($column->getId() === $id) {
                return $column;
            }
        }

        return null;
    }

    private function initModel(): void
    {
        if ('' !== $this->globalSearch) {
            if($this->dataGridEntity->getCustomSearchGlobalCallback() !== null){
                ($this->dataGridEntity->getCustomSearchGlobalCallback())($this->selection, $this->globalSearch);
            }else {
                $query = [];
                $params = [];
                foreach ($this->dataGridEntity->getColumns() as $column) {
                    if ($column->isEnableSearchGlobal()) {
                        $query[] = $column->column . ' REGEXP ?';
                        $params[] = $this->globalSearch;
                    }
                }
                if (!empty($query)) {
                    if (1 === count($query)) {
                        $this->selection->where($query[0], $params[0]);
                    } else {
                        $this->selection->where(implode(' OR ', $query), $params);
                    }
                }
            }
        }

        foreach($this->filter as $key => $value){
            $callback = $this->dataGridEntity->getFilters()[$key]->getOnChangeCallback();
            $callback($this->selection, $value);
        }
    }
}
