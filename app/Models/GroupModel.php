<?php

namespace App\Models;

use Myth\Auth\Models\GroupModel as MythModel;

class GroupModel extends MythModel
{
    protected $validationRules = [
        'id' => 'permit_empty|numeric',
        'name' => 'required|max_length[255]|is_unique[auth_groups.name,id,{id}]',
        'description' => 'max_length[255]',
    ];
}
