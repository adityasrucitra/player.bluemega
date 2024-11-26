<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAccessModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'auth_permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id', 'name', 'description'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * .
     */
    public function __construct()
    {
        $this->authorize = service('authorization');
    }

    /**
     * .
     */
    public function insertPermission($data = [])
    {
        //check if permission existed
        $permission = $this->authorize->permission($data['permission_name']);
        if ($permission) {
            return [
                'message' => 'Permission exist, nothing add',
            ];
        }

        $id = $authorize->createPermission($data['permission_name'], $data['permission_description']);

        return ['message' => 'New permission added', 'id' => $id];
    }
}
