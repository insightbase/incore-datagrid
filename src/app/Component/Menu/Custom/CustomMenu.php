<?php

namespace App\Component\Datagrid\Menu\Custom;

use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

/**
 * @property-read Template $template
 */
class CustomMenu extends Control
{
    public function __construct(
        private $linkCallback,
        private readonly string $caption,
        private readonly string $icon
    )
    {
    }

    public function render(ActiveRow $activeRow):void
    {
        $this->template->linkCallback = $this->linkCallback;
        $this->template->caption = $this->caption;
        $this->template->icon = $this->icon;
        $this->template->activeRow = $activeRow;

        $this->template->setFile(__DIR__ . '/customMenu.latte');
        $this->template->render();
    }
}