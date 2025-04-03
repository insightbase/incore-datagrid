<?php

namespace App\Component\Datagrid;

use App\UI\Accessory\Admin\Form\Form;
use Nette\Database\Table\ActiveRow;

interface InlineEdit
{
    public function isEnabled(ActiveRow $row):bool;
    public function getDefaults(int $id):array;
    public function getOnSuccessCallback():callable;
    public function getForm():?Form;
}