<?php

namespace App\Component\Datagrid\Dto;

class ReturnInlineEditCallback
{
    public function __construct(
        public bool $redraw = true,
    )
    {
    }
}