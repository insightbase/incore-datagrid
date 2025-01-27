<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\Exception\RefNotSetException;
use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class HasManyColumnEntity extends ColumnEntity
{
    private ?string $relation = null;
    private $getRelationCallback;

    public function __construct(string $column, string $label, bool $sort = false, SortDirEnum $sortDir = SortDirEnum::ASC)
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->getRelationCallback = function(ActiveRow $activeRow):Selection{
            return $activeRow->related($this->relation);
        };
        $this->getColumnCallback = (function(ActiveRow $activeRow):string {
            if($this->relation === null){
                throw new RefNotSetException('If you use HasManyColumnEntity you must set ref (->setRef())');
            }
            $ret = [];
            foreach(($this->getRelationCallback)($activeRow) as $row){
                foreach($this->ref as $ref){
                    $row = $row->ref($ref);
                }

                $ret[] = $row[$this->column];
            }
            return implode(",", $ret);
        });
        $this->getRowCallback = function(ActiveRow $activeRow):ActiveRow{
            return $activeRow;
        };
    }

    public function setRelation(?string $relation): self
    {
        $this->relation = $relation;
        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getGetRelationCallback(): \Closure
    {
        return $this->getRelationCallback;
    }

    public function setGetRelationCallback(\Closure $getRelationCallback): self
    {
        $this->getRelationCallback = $getRelationCallback;
        return $this;
    }
}