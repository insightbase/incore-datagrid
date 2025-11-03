<?php

namespace App\Component\Datagrid\Menu\Custom;

use Nette\Application\UI\Control;

/**
 * @property-read Template $template
 */
class CustomMenu extends Control
{
    public function __construct(
        private readonly string $link,
        private readonly string $caption,
        private readonly string $icon
    )
    {
    }

    public function render():void
    {
        $this->template->link = $this->link;
        $this->template->caption = $this->caption;
        $this->template->icon = $this->icon;
        $this->template->setFile(__DIR__ . '/customMenu.latte');
        $this->template->render();
    }
}