<?php

namespace App\Component\Datagrid\Entity;

use App\Component\Datagrid\SortDirEnum;
use App\Model\Admin\User;
use Nette\Database\Table\ActiveRow;

class UserColumnEntity extends ColumnEntity
{
    public function __construct(
        string                $column,
        string                $label,
        private readonly User $userModel,
        bool                  $sort = false,
        SortDirEnum           $sortDir = SortDirEnum::ASC,
    )
    {
        parent::__construct($column, $label, $sort, $sortDir);
        $this->setGetRowCallback(function(ActiveRow $row) use ($column):?ActiveRow{
            return $row->ref('user', $column);
        });
        $this->getColumnCallback = function (ActiveRow $activeRow, bool $original = false): string {
            if(array_key_exists('user_id', $activeRow->toArray())) {
                $user = $this->userModel->get($activeRow['user_id']);
                return (string)$user->firstname . ' ' . $user->lastname;
            }else{
                return (string)$activeRow['firstname'] . ' ' . $activeRow['lastname'];
            }
        };
        $this->templateFile = 'user.latte';
    }


}