<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'profile';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'first_name', 'last_name', 'city', 'country', 'phone_number'];

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
    public function getAccountWithCredential($userId = null)
    {
        $builder = $this->db->table('users a');
        $selectField = [
            'a.id as user_id, a.username', 'a.email', 'a.citizen_number', 'a.active',
            'b.id as profile_id', 'b.first_name', 'b.last_name', 'b.city', 'b.country',
            'b.phone_number', 'b.profile_image', 'b.timezone', 'ag.name as group_name', ];
        $query = $builder->select(implode(',', $selectField))
            ->join('profile b', 'a.id = b.user_id')
            ->join('auth_groups_users agu', 'agu.user_id=a.id')
            ->join('auth_groups ag', 'ag.id=agu.group_id');

        if ($userId) {
            $query = $query->where('a.id', $userId);
        }

        $query = $query->orderBy('b.first_name', 'ASC')
            ->orderBy('b.last_name', 'ASC')
            ->get();

        if ($userId) {
            return $query->getRowArray();
        }

        return $query->getResultArray();
    }

    /**
     * .
     */
    public function getCompanies($limit = 0, $offset = 0, $userId = null, $search = null)
    {
        $response = [
            'totalCompanies' => 0,
            'filteredCompanies' => 0,
            'data' => [],
        ];
        $columns = [
            'c.company_name', 'c.email', 'c.address',
            'uc.id as uc_id',
        ];
        $builder = $this->db->table('users u')
            ->select(implode(',', $columns))
            ->join('users_companies uc', 'uc.user_id=u.id')
            ->join('companies c', 'c.id=uc.company_id')
            ->where('u.id', $userId)
            ->where('uc.deleted_at', null)
            ->where('c.deleted_at', null);

        $response['totalCompanies'] = $builder->countAllResults(false);

        if ($search) {
            $builder = $builder->like('c.company_name', $search);
            $response['filteredCompanies'] = $builder->countAllResults(false);
        }

        $builder = $builder->orderBy('c.company_name', 'asc')->get();

        foreach ($builder->getResultArray() as $company) {
            $response['data'][] = $company;
        }

        return $response;
    }

    /**
     * .
     */
    public function addCompany($userId = null, $companyId = null)
    {
        $builder = $this->db->table('users u')
            ->select('c.id as company_id')
            ->join('users_companies uc', 'uc.user_id=u.id')
            ->join('companies c', 'c.id=uc.company_id')
            ->where('u.id', $userId)
            ->where('uc.deleted_at', null)
            ->where('c.deleted_at', null)
            ->get();
        $usedCompanyId = [];
        foreach ($builder->getResultArray() as $row) {
            $usedCompanyId[] = $row['company_id'];
        }

        if (in_array($companyId, $usedCompanyId)) {
            return [
                'status' => false,
                'message' => 'User already in selected company!',
            ];
        }

        $result = [
            'status' => false,
        ];
        $data = [
            'user_id' => $userId,
            'company_id' => $companyId,
        ];
        if (!$this->db->table('users_companies')->insert($data)) {
            $result['message'] = $this->db->error();

            return $result;
        }

        $result['status'] = true;

        return $result;
    }

    /**
    * .
    */
    public function getCountries($term = null)
    {
        $builder = $this->db->table('countries');
        if ($term) {
            $builder = $builder->like('name', $term);
        }
        $builder = $builder->get();
        $countries['results'] = [];
        foreach ($builder->getResultArray() as $country) {
            $countries['results'][] = [
                'id' => $country['id'],
                'text' => $country['name'],
            ];
        }

        return $countries;
    }

    /**
     * .
     */
    public function getStates($term = null, $countryId = null)
    {
        $builder = $this->db->table('states');
        if ($term) {
            $builder = $builder->like('name', $term);
        }
        $builder = $builder->where('country_id', $countryId);
        $builder = $builder->get();
        $states['results'] = [];
        foreach ($builder->getResultArray() as $state) {
            $states['results'][] = [
                'id' => $state['id'],
                'text' => $state['name'],
            ];
        }

        return $states;
    }

    /**
     * .
     */
    public function getCities($term = null, $countryId = null, $stateId = null)
    {
        $builder = $this->db->table('cities');
        if ($term) {
            $builder = $builder->like('name', $term);
        }
        $builder = $builder->where('country_id', $countryId);
        if ($stateId) {
            $builder = $builder->where('state_id', $stateId);
        }
        $builder = $builder->get();
        $cities['results'] = [];
        foreach ($builder->getResultArray() as $city) {
            $cities['results'][] = [
                'id' => $city['id'],
                'text' => $city['name'],
            ];
        }

        return $cities;
    }
}
