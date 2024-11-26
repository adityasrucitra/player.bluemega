<?php

namespace App\Models;

use CodeIgniter\Model;
use Myth\Auth\Models\PermissionModel as MythPermissionModel;

class PermissionModel extends MythPermissionModel
{
    protected $validationRules = [
        'id'          => 'max_length[19]|permit_empty',
        'name'        => 'required|max_length[255]|is_unique[auth_permissions.name,id,{id}]',
        'description' => 'max_length[255]',
    ];
}
