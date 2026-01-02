<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\InlineEdit;
use App\Component\Datagrid\SortDirEnum;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Html;

class ColumnEntity
{
    protected bool $enableSearchGlobal = false;

    /**
     * @var null|callable
     */
    protected $getColumnCallback;

    /**
     * @var null|callable
     */
    protected $getColumnExportCallback;
    protected bool $enabledSort = true;

    /**
     * @var ?callable
     */
    protected $inlineEditCallback;

    /**
     * @var callable
     */
    protected $getInlineEditIdCallback;
    protected ?int $truncate = null;
    protected array $ref = [];
    protected bool $noEscape = false;
    public string $templateFile = 'default.latte' {
        get {
            return $this->templateFile;
        }
        set {
            $this->templateFile = $value;
        }
    }
    public array $beforeRender = [];

    /**
     * @var callable
     */
    protected $getRowCallback;
    public string $modalId = 'datagrid-inline-edit';
    public ?InlineEdit $inlineEdit = null;
    /**
     * @var callable
     */
    protected $inlineEditInputCallback;
    public ?string $templateDir = null;
    public ?string $class = null;
    public ?string $classHeader = 'min-w-[200px]';
    private ?string $link = null;
    /**
     * @var ?callable
     */
    protected $sortString = null;

    public function __construct(
        public string $column,
        public string $label,
        public bool $sort = false,
        public SortDirEnum $sortDir = SortDirEnum::ASC,
    ) {
        $this->inlineEditInputCallback = function(ActiveRow $row):Html{
            return Html::el('input')->type('text')->class('input')->value('xxxx');
        };
        $this->getInlineEditIdCallback = function (ActiveRow $row): int {
            return $row['id'];
        };
        $this->getRowCallback = function (ActiveRow $activeRow): ?ActiveRow {
            foreach ($this->ref as $ref) {
                $activeRow = $activeRow->ref($ref);
            }

            return $activeRow;
        };
        $this->getColumnCallback = function (ActiveRow $activeRow, bool $original = false): string {
            $value = $activeRow[$this->column];
            if($value instanceof \DateInterval){
                $value = (new \DateTimeImmutable('1970-01-01 00:00:00'))->add($value)->format('H:i');
            }
            return (string) $value;
        };
        $this->getColumnExportCallback = function (ActiveRow $activeRow): string {
            $value = $activeRow[$this->column];
            if($value instanceof \DateInterval){
                $value = (new \DateTimeImmutable('1970-01-01 00:00:00'))->add($value)->format('H:i');
            }
            return (string) $value;
        };
        $this->sortString = function():string{
            return $this->column;
        };
    }

    public function getSortString(): ?callable
    {
        return $this->sortString;
    }

    public function setSortString(callable $sortString): self
    {
        $this->sortString = $sortString;
        return $this;
    }

    public function disableSort(): self
    {
        $this->enabledSort = false;

        return $this;
    }

    public function enableSort(): self
    {
        $this->enabledSort = true;

        return $this;
    }

    public function getColumnCallback(): ?callable
    {
        return $this->getColumnCallback;
    }

    public function setGetColumnCallback(callable $getColumnCallback): self
    {
        $this->getColumnCallback = $getColumnCallback;

        return $this;
    }

    public function isEnableSearchGlobal(): bool
    {
        return $this->enableSearchGlobal;
    }

    public function setEnableSearchGlobal(bool $enableSearchGlobal = true): self
    {
        $this->enableSearchGlobal = $enableSearchGlobal;

        return $this;
    }

    public function isEnabledSort(): bool
    {
        return $this->enabledSort;
    }

    public function getGetColumnExportCallback(): ?callable
    {
        return $this->getColumnExportCallback;
    }

    public function setGetColumnExportCallback(?callable $getColumnExportCallback): self
    {
        $this->getColumnExportCallback = $getColumnExportCallback;

        return $this;
    }

    public function getInlineEditCallback(): ?callable
    {
        return $this->inlineEditCallback;
    }

    public function setInlineEditCallback(?callable $inlineEditCallback): self
    {
        $this->inlineEditCallback = $inlineEditCallback;

        return $this;
    }

    public function getGetInlineEditIdCallback(): callable
    {
        return $this->getInlineEditIdCallback;
    }

    public function setGetInlineEditIdCallback(callable $getInlineEditIdCallback): self
    {
        $this->getInlineEditIdCallback = $getInlineEditIdCallback;

        return $this;
    }

    public function getTruncate(): ?int
    {
        return $this->truncate;
    }

    public function setTruncate(?int $truncate): self
    {
        $this->truncate = $truncate;

        return $this;
    }

    public function getRef(): array
    {
        return $this->ref;
    }

    public function setRef(array $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getGetRowCallback(): callable
    {
        return $this->getRowCallback;
    }

    public function setGetRowCallback(callable $getRowCallback): self
    {
        $this->getRowCallback = $getRowCallback;

        return $this;
    }

    public function isNoEscape(): bool
    {
        return $this->noEscape;
    }

    public function setNoEscape(bool $noEscape = true): self
    {
        $this->noEscape = $noEscape;
        return $this;
    }

    public function setInlineEdit(?InlineEdit $inlineEdit): self
    {
        $this->inlineEdit = $inlineEdit;
        return $this;
    }

    public function getInlineEditInputCallback(): callable
    {
        return $this->inlineEditInputCallback;
    }

    public function setInlineEditInputCallback(callable $inlineEditInputCallback): self
    {
        $this->inlineEditInputCallback = $inlineEditInputCallback;
        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function getClassHeader(): ?string
    {
        return $this->classHeader;
    }

    public function setClassHeader(?string $classHeader): self
    {
        $this->classHeader = $classHeader;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }
}
