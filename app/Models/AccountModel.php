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
            'b.id as profile_id', 'b.first_name', 'b.last_name', 'b.city_id', 'b.state_id', 'b.country_id',
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
        $result = null;
        $countryIds = [];
        $stateIds = [];
        $cityIds = [];
        if ($userId) {
            $result = $query->getRowArray();
            if ($result) {
                $countryIds[] = $result['country_id'];
                $stateIds[] = $result['state_id'];
                $cityIds[] = $result['city_id'];
            }
        } else {
            $result = $query->getResultArray();
            if ($result) {
                foreach ($result as $row) {
                    $countryIds[] = $row['country_id'];
                    $stateIds[] = $row['state_id'];
                    $cityIds[] = $row['city_id'];
                }
                $countryIds = array_unique($countryIds);
                $stateIds = array_unique($stateIds);
                $cityIds = array_unique($cityIds);
            }
        }

        //get country, state and city name
        $builder = $this->db->table('countries co')
            ->select('co.id as country_id, co.name as country_name, st.id as state_id, st.name as state_name, ci.id as city_id, ci.name as city_name,')
            ->join('states st', 'co.id=st.country_id')
            ->join('cities ci', 'st.id=ci.state_id')
            ->whereIn('co.id', $countryIds)
            ->whereIn('st.id', $stateIds)
            ->whereIn('ci.id', $cityIds)
            ->groupBy('ci.name')
            ->get()->getResultArray();

        $geo = [];
        foreach ($builder as $row) {
            $geo[$row['city_id']] = $row;
        }

        $i = 0;
        foreach ($result as $row) {
            if (array_key_exists($row['city_id'], $geo)) {
                $row['country'] = $geo[$row['city_id']]['country_name'];
                $row['state'] = $geo[$row['city_id']]['state_name'];
                $row['city'] = $geo[$row['city_id']]['city_name'];
            }
            $result[$i] = $row;
            $i++;
        }

        return $result;
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
            'c.id as company_id', 'c.company_name', 'c.email', 'c.address',
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
    public function getCities($term = null, $stateId = null)
    {
        $builder = $this->db->table('cities');
        if ($term) {
            $builder = $builder->like('name', $term);
        }
        $builder = $builder->where('state_id', $stateId);
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

    /**
     * .
     */
    public function getAllVessels($limit = 0, $offset = 0, $companyId = null, $userId = null, $search = null)
    {
        $result = [
            'countAllRows' => 0,
            'countFilteredRows' => 0,
            'data' => []
        ];
        $hiddenVessels = $this->db->table('users_vessels_hidden uvh')
            ->where('uvh.company_id', $companyId)
            ->where('uvh.user_id', $userId)
            ->where('deleted_at IS NOT', null)
            ->get()->getResultArray();

        $vesselIds = array_column($hiddenVessels, 'vessel_id');

        $vesselContracts = $this->db->table('vessels v')
            ->select('v.id as vessel_id, v.name as vessel_name, c.id as company_id, c.company_name')
            ->join('vessels_contract vc', 'vc.vessel_id=v.id')
            ->join('companies c', 'c.id=vc.company_id')
            ->where('c.id', $companyId)
            ->where('v.deleted_at', null)
            ->where('vc.deleted_at', null)
            ->where('c.deleted_at', null);
        $result['countAllRows'] = $vesselContracts->countAllResults(false);
        if ($search) {
            $vesselContracts = $vesselContracts->like('v.name', $search);
            $result['countFilteredRows'] = $vesselContracts->countAllResults(false);
        } else {
            $result['countFilteredRows'] = $vesselContracts->countAllResults(false);
        }
        $vesselContracts = $vesselContracts->get($limit, $offset)->getResultArray();

        foreach ($vesselContracts as &$vc) {
            $vc['hidden'] = false;
            if (in_array($vc['vessel_id'], $vesselIds)) {
                $vc['hidden'] = true;
            }
            $result['data'][] = $vc;
        }

        return $result;
    }
}
