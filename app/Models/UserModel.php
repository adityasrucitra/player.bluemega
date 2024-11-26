<?php

namespace App\Models;

use App\Entities\User;
use Myth\Auth\Models\UserModel as MythModel;

class UserModel extends MythModel
{
    protected $allowedFields = [
        'email', 'username', 'password_hash', 'reset_hash', 'reset_at', 'reset_expires', 'activate_hash',
        'status', 'status_message', 'active', 'force_pass_reset', 'permissions', 'deleted_at', 'citizen_number',
        // 'first_name', 'last_name', 'phone_number',
    ];

    protected $returnType = User::class;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = ['addProfile'];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected $profileFields = [];

    protected $validationRules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone_number' => 'required',
        'citizen_number' => 'required',
    ];

    /**.
     *
     */
    public function setProfileFields($data)
    {
        $this->profileFields['first_name'] = $data['first_name'];
        $this->profileFields['last_name'] = $data['last_name'];
        $this->profileFields['phone_number'] = $data['phone_number'];

        return $this;
    }

    /**
     * .
     */
    public function addProfile($data)
    {
        // dd($data);
        if ($data['id'] == 0) {
            return;
        }
        $this->profileFields['user_id'] = $data['id'];
        $this->db->table('profile')->insert($this->profileFields);
    }
}
