<?php

namespace App\Component\Datagrid\Menu\Custom;

interface CustomMenuFactory
{
    public function create(callable $linkCallback, string $caption, string $icon):CustomMenu;
}