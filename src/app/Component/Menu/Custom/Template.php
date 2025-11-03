<?php

namespace App\Component\Datagrid\Menu\Custom;

class Template extends \Nette\Bridges\ApplicationLatte\Template
{
    public string $caption;
    public string $icon;
    /**
     * @var callable
     */
    public $linkCallback;
    public \Nette\Database\Table\ActiveRow $activeRow;
    public string $target;
}