<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['company_name', 'country_id', 'state_id', 'city_id', 'phone_number', 'email', 'address', 'unique_code', 'alias'];

    // Dates
    protected $useTimestamps = true;
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
    protected $afterFind = ['getGeolocation'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * .
     */
    public function generateRandom($length = 8)
    {
        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            $code .= mt_rand(0, 9);
        }
        $c = $this->where('unique_code', $code)->find();
        // if ($c) {
        //     $this->generateRandom($length);
        // }

        return $code;
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

    /**
     * .
     */
    public function getCompanies($term = null)
    {
        $companies = [];
        if ($term) {
            $companies = $this->like('company_name', $term)->findAll();
        } else {
            $companies = $this->findAll();
        }

        foreach ($companies as $company) {
            $companies['results'][] = [
                'id' => $company['id'],
                'text' => $company['company_name'],
            ];
        }

        return $companies;
    }

    /**
     * .
     */
    public function getGeolocation($data)
    {
        $countryIds = [];
        if ($data['method'] == 'findAll') {
            if ($this->returnType == 'array') {
                foreach ($data['data'] as $company) {
                    $countryIds[] = $company['country_id'];
                }
            } else {
                foreach ($data['data'] as $company) {
                    $countryIds[] = $company->country_id;
                }
            }
        } else {
            if ($this->returnType == 'array') {
                $countryIds[] = $data['data']['country_id'];
            } else {
                foreach ($data['data'] as $company) {
                    $countryIds[] = $data['data']->country_id;
                }
            }
        }

        if (empty($countryIds)) {
            return $data;
        }
        $countryIds = array_unique($countryIds);

        $query = $this->db->table('countries c')
            ->select('c.id as country_id, c.name as country_name, s.id as state_id, s.name as state_name,
            ct.id as city_id, ct.name as city_name')
            ->join('states s', 'c.id=s.country_id')
            ->join('cities ct', 'c.id=ct.country_id')
            ->whereIn('c.id', $countryIds)->get()->getResultArray();
        $countries = [];
        $states = [];
        $cities = [];
        foreach ($query as $q) {
            $countries[$q['country_id']] = $q['country_name'];
            $states[$q['state_id']] = $q['state_name'];
            $cities[$q['city_id']] = $q['city_name'];
        }

        ////////////////////////////////////

        if ($data['method'] == 'findAll') {
            if ($this->returnType == 'array') {
                for ($i = 0; $i < count($data['data']); ++$i) {
                    $data['data'][$i]['country_name'] = $countries[$data['data'][$i]['country_id']];
                    $data['data'][$i]['state_name'] = $states[$data['data'][$i]['state_id']];
                    $data['data'][$i]['city_name'] = $cities[$data['data'][$i]['city_id']];
                }
            } else {
                for ($i = 0; $i < count($data['data']); ++$i) {
                    $data['data'][$i]->country_name = $countries[$data['data'][$i]->country_id];
                    $data['data'][$i]->state_name = $states[$data['data'][$i]->state_id];
                    $data['data'][$i]->city_name = $cities[$data['data'][$i]->city_id];
                }
            }
        } else {
            if ($this->returnType == 'array') {
                $data['data']['country_name'] = $countries[$data['data']['country_id']];
                $data['data']['state_name'] = $states[$data['data']['state_id']];
                $data['data']['city_name'] = $cities[$data['data']['city_id']];
            } else {
                $data['data']->country_name = $countries[$data['data']->country_id];
                $data['data']->state_name = $states[$data['data']->state_id];
                $data['data']->city_name = $cities[$data['data']->city_id];
            }
        }

        return $data;
    }

    /*
     * .
     */
    public function getVessels($limit = 0, $offset = 0, $companyId = null, $search = null)
    {
        $response = [
            'totalVessel' => 0,
            'filteredVessel' => 0,
            'data' => [],
        ];

        $columns = [
            'v.name as vessel_name', 'v.imo', 'v.mmsi',
            'c.company_name',
            'vc.created_at as contract_start', 'vc.deleted_at as contract_end',
        ];
        $builder = $this->db->table('vessels_contract vc')
            ->select(implode(',', $columns))
            ->join('vessels v', 'v.id=vc.vessel_id')
            ->join('companies c', 'c.id=vc.company_id')
            ->where('vc.company_id', $companyId)
            ->where('v.deleted_at', null)
            ->where('c.deleted_at', null)
            ->where('vc.deleted_at', null);

        $response['totalVessel'] = $builder->countAllResults(false);

        if ($search) {
            $builder = $builder->like('v.name', $search);
            $response['filteredVessel'] = $builder->countAllResults(false);
        }

        $builder = $builder->orderBy('v.name', 'asc');

        foreach ($builder->get($limit, $offset)->getResultArray() as $vessel) {
            $response['data'][] = $vessel;
        }

        return $response;
    }

    /**
     * .
     */
    function getUsersCompany($limit=0, $offset = 0, $companyId = null, $term=null)
    {
        $result = [
            'countAll' => 0,
            'countFiltered' => 0,
            'data' => []
        ];
        $builder = $this->db->table('users u')
            ->select("p.first_name, p.last_name, u.email, uc.id")
            ->join('profile p', 'p.user_id=u.id', 'left')
            ->join('users_companies uc', 'uc.user_id=u.id')
            ->where('uc.company_id', $companyId)
            ->where('uc.deleted_at', null)
            ->where('u.active', 1);
        $result['countAll'] = $builder->countAllResults(false);
        if($term){
            $builder = $builder->like('CONCAT(p.first_name, " ", p.last_name)', $term);
            $result['countFiltered'] = $builder->countAllResults(false);
        }else{
            $result['countFiltered'] = $builder->countAllResults(false);
        }       
        $result['data'] = $builder->orderBy('p.first_name', 'ASC')
            ->get($limit, $offset)->getResultArray();

        return $result;
    }

    /**
     * .
     */
    function removeUserCompany($id = null)
    {
        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        return $this->db->table('users_companies uc')
            ->set('deleted_at', $today->format('Y-m-d H:i:s'))
            ->where('uc.id', $id)->update();
    }

    /**
     * .
     */
    function addUSerCompany($data = []){
        //check if user company exist
        $builder = $this->db->table('users_companies uc')
            ->select('uc.id')
            ->where('uc.company_id', $data['company_id'])
            ->where('uc.user_id', $data['user_id'])
            ->where('uc.deleted_at', null)
            ->get()->getNumRows();
        if($builder){
            return false;
        }

        return $this->db->table('users_companies uc')
            ->insert($data);
    }

    /**
     * .
     */
    function getUserList($companyId = null, $term=null){
        //user of company
        $userOfCompany = $this->db->table('users_companies uc')
            ->select('uc.user_id')
            ->where('uc.company_id', $companyId)
            ->where('uc.deleted_at', null)
            ->get()->getResultArray();

        $builder = $this->db->table('users u')
            ->select('u.id as user_id, p.first_name, p.last_name')
            ->join('profile p', 'p.user_id=u.id', 'left');
        if($userOfCompany){
            $builder = $builder->whereNotIn('u.id', array_column($userOfCompany, 'user_id'));
        }
        if($term){
            $builder = $builder->like('CONCAT(p.first_name, " ", p.last_name)', $term);
        }
        $builder = $builder->orderBy('p.first_name', 'ASC')
            ->orderBy('p.last_name', 'ASC')
            ->get()->getResultArray();
        
        return $builder;            
    }
}
