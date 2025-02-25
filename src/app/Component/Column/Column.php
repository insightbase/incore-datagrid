<?php

namespace App\Component\Datagrid\Column;

use App\Component\Datagrid\Entity\ColumnEntity;
use App\Component\EditorJs\EditorJsFacade;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;

class Column extends Control
{
    private bool $enabledSort = true;

    public function __construct(
        private readonly string         $column,
        private readonly string         $label,
        private readonly ColumnEntity   $columnEntity,
        private readonly string         $id,
        private readonly EditorJsFacade $editorJsFacade,
    ) {}

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
        $value = ($this->columnEntity->getColumnCallback())(($this->columnEntity->getGetRowCallback())($activeRow));
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
            return $value;
        }else{
            try {
                $json = Json::decode($value, true);
                if(is_array($json) && array_key_exists('time', $json)) {
                    return strip_tags($this->editorJsFacade->renderJson($value));
                }else{
                    return $value;
                }
            } catch (JsonException $e) {
                return $value;
            }
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
}
