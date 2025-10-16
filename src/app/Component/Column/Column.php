<?php

namespace App\Component\Datagrid\Column;

use App\Component\Datagrid\Entity\ColumnEntity;
use App\Component\EditorJs\EditorJsFacade;
use App\Component\Image\ImageControl;
use App\Component\Image\ImageControlFactory;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;

/**
 * @property-read Template $template
 */
class Column extends Control
{
    private bool $enabledSort = true;
    protected array $beforeRender = [];

    public function __construct(
        private readonly string              $column,
        private readonly string              $label,
        private readonly ColumnEntity        $columnEntity,
        private readonly string              $id,
        private readonly EditorJsFacade      $editorJsFacade,
        private readonly ImageControlFactory $imageControlFactory,
    ) {}

    protected function createComponentImage():ImageControl{
        return $this->imageControlFactory->create();
    }

    public function render(ActiveRow $activeRow):void
    {
        $this->template->column = $this;
        $this->template->activeRow = ($this->columnEntity->getGetRowCallback())($activeRow);
        $this->template->activeRowOrig = $activeRow;
        $this->template->render(dirname(__FILE__) . '/' . $this->columnEntity->templateFile);
    }

    public function getInlineEditId(ActiveRow $row): string
    {
        return ($this->getColumnEntity()->getGetInlineEditIdCallback())($row);
    }

    public function getRowExport(ActiveRow $activeRow): string
    {
        return ($this->columnEntity->getGetColumnExportCallback())($activeRow);
    }

    public function getRow(ActiveRow $activeRow, bool $original = false, bool $toEditor = false): string
    {
        $row = ($this->columnEntity->getGetRowCallback())($activeRow);
        $value = ($this->columnEntity->getColumnCallback())($row, $original);
        if($toEditor){
            try{
                $json = Json::decode($value, true);
                if(is_array($json) && array_key_exists('time', $json)){
                    return $value;
                }
            }catch(JsonException $e){

            }
            return '{"time":' . time() . ',"blocks":[{"id":"","type":"paragraph","data":{"text":"' . $value . '"}}],"version":""}';
        }
        if($original) {
            return ($this->columnEntity->getColumnCallback())($activeRow, $original);
        }else{
            return $value;
        }
    }

    public function showOpenModalInInlineEdit(ActiveRow $row):bool{
        if($this->getColumnEntity()->getInlineEditCallback() === null){
            return false;
        }
        return true;
        $value = $this->getRow($row, true);
        try {
            $json = Json::decode($value, true);
            if(array_key_exists('time', $json)) {
                return true;
            }else{
                return false;
            }
        } catch (JsonException $e) {
            if($value === '' || $value === null || Strings::length($value) > 255 ){
                return true;
            }else{
                return false;
            }
        }
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isEnabledSort(): bool
    {
        return $this->enabledSort;
    }

    public function setEnabledSort(bool $enabledSort): self
    {
        $this->enabledSort = $enabledSort;

        return $this;
    }

    public function getColumnEntity(): ColumnEntity
    {
        return $this->columnEntity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setTemplateFile(string $templateFile): self
    {
        $this->templateFile = $templateFile;
        return $this;
    }
}
