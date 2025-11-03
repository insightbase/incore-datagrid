<?php

namespace App\Component\Datagrid\Menu\Custom;

interface CustomMenuFactory
{
    public function create(string $link, string $caption, string $icon):CustomMenu;
}