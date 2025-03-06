<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\Enum\FilterTypeEnum;
use Nette\Utils\Html;

class FilterEntity
{
    /**
     * @var null|callable
     */
    private $onChangeCallback = null;

    public function __construct(
        private readonly string         $label,
        private readonly FilterTypeEnum $type,
        private array                   $values = [],
    )
    {
    }

    public function getInputLabel(string $name):?Html
    {
        if($this->type === FilterTypeEnum::Input){
            return null;
        }
        $html = Html::el('label')->for($name)->setText($this->label);
        return $html;
    }

    public function getInput(string $name, ?string $default):Html
    {
        $html = match($this->type){
            FilterTypeEnum::Input => Html::el('input')->type('text')->placeholder($this->label)->class('filterInput')->setText($default),
            FilterTypeEnum::Select => Html::el('select')->class('filterInput'),
            FilterTypeEnum::Checkbox => Html::el('input')->type('checkbox')->class('checkbox filterInput')->checked($default === 'true'),
        };

        $html->name($name);
        $html->id($name);

        return $html;
    }

    public function getOnChangeCallback(): ?callable
    {
        return $this->onChangeCallback;
    }

    public function setOnChangeCallback(callable $onChangeCallback): self
    {
        $this->onChangeCallback = $onChangeCallback;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}