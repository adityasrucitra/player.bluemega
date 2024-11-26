<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'profile';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'first_name', 'last_name', 'phone_number', 'timezone', 'country_id', 'state_id', 'city_id', 'profile_image'];

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
    protected $afterFind = ['loadUser'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * .
     */
    public function getQRCode($string)
    {
        return '<img class="img-fluid" src="' . (new QRCode())->render($string) . '" alt="QR Code" />';
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
        $builder = $builder->where('state_id', $stateId)->get();
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
    public function loadUser($data)
    {
        $users = [];
        $query = $this->db->table('users u')->select('u.id, u.email, u.username, u.citizen_number, u.active, g.id AS role_id, g.name AS role_name')
            ->join('auth_groups_users gu', 'gu.user_id = u.id')
            ->join('auth_groups g', 'g.id = gu.group_id')
            ->get();

        $uIds = [];
        foreach ($query->getResultArray() as $row) {
            $users['u_' . $row['id']] = $row;
            $uIds[] = $row['id'];
        }

        $socialMedia = [];
        $builder = $this->db->table('users_social_media usm')
            ->select('usm.username, usm.user_id, usm.social_media')
            ->whereIn('usm.user_id', $uIds)
            ->get()->getResultArray();
        foreach ($builder as $sm) {
            $socialMedia[$sm['user_id']] = [];
        }
        foreach ($builder as $sm) {
            $socialMedia[$sm['user_id']][] = $sm;
        }

        //get country name and city name
        $bld = $this->db->table('profile p')
            ->select('p.user_id, c.id as country_id, c.name as country_name, s.id as state_id, s.name as state_name, cc.id as city_id, cc.name as city_name')
            ->join('countries c', 'c.id=p.country_id', 'left')
            ->join('states s', 's.id=p.state_id', 'left')
            ->join('cities cc', 'cc.id=p.city_id', 'left')
            ->whereIn('p.user_id', $uIds)
            ->get()->getResultArray();
        $locMap = [];
        foreach ($bld as $r) {
            $locMap[$r['user_id']] = $r;
        }

        if (\is_array($data['data'])) {
            if ($this->returnType == 'array') {
                for ($i = 0; $i < count($data['data']); ++$i) {
                    $data['data'][$i]['username'] = $users['u_' . $data['data'][$i]['user_id']]['username'];
                    $data['data'][$i]['email'] = $users['u_' . $data['data'][$i]['user_id']]['email'];
                    $data['data'][$i]['citizen_number'] = $users['u_' . $data['data'][$i]['user_id']]['citizen_number'];
                    $data['data'][$i]['active'] = $users['u_' . $data['data'][$i]['user_id']]['active'];
                    $data['data'][$i]['role_id'] = $users['u_' . $data['data'][$i]['user_id']]['role_id'];
                    $data['data'][$i]['role_name'] = $users['u_' . $data['data'][$i]['user_id']]['role_name'];
                    $data['data'][$i]['country_id'] = $locMap[$data['data'][$i]['user_id']]['country_id'];
                    $data['data'][$i]['country_name'] = $locMap[$data['data'][$i]['user_id']]['country_name'];
                    $data['data'][$i]['state_id'] = $locMap[$data['data'][$i]['user_id']]['state_id'];
                    $data['data'][$i]['state_name'] = $locMap[$data['data'][$i]['user_id']]['state_name'];
                    $data['data'][$i]['city_id'] = $locMap[$data['data'][$i]['user_id']]['city_id'];
                    $data['data'][$i]['city_name'] = $locMap[$data['data'][$i]['user_id']]['city_name'];
                    $data['data'][$i]['social_media'] = array_key_exists($data['data'][$i]['user_id'], $socialMedia) ? $socialMedia[$data['data'][$i]['user_id']] : [];
                }
            } else {
                for ($i = 0; $i < count($data['data']); ++$i) {
                    $data['data'][$i]->username = $users['u_' . $data['data'][$i]->user_id]['username'];
                    $data['data'][$i]->email = $users['u_' . $data['data'][$i]->user_id]['email'];
                    $data['data'][$i]->citizen_number = $users['u_' . $data['data'][$i]->user_id]['citizen_number'];
                    $data['data'][$i]->active = $users['u_' . $data['data'][$i]->user_id]['active'];
                    $data['data'][$i]->role_id = $users['u_' . $data['data'][$i]->user_id]['role_id'];
                    $data['data'][$i]->role_name = $users['u_' . $data['data'][$i]->user_id]['role_name'];
                    $data['data'][$i]->country_id = $locMap[$data['data'][$i]->user_id]['country_id'];
                    $data['data'][$i]->country_name = $locMap[$data['data'][$i]->user_id]['country_name'];
                    $data['data'][$i]->state_id = $locMap[$data['data'][$i]->user_id]['state_id'];
                    $data['data'][$i]->state_name = $locMap[$data['data'][$i]->user_id]['state_name'];
                    $data['data'][$i]->city_id = $locMap[$data['data'][$i]->user_id]['city_id'];
                    $data['data'][$i]->city_name = $locMap[$data['data'][$i]->user_id]['city_name'];
                    $data['data'][$i]->social_media = array_key_exists($data['data'][$i]->user_id, $socialMedia) ? $socialMedia[$data['data'][$i]->user_id] : [];
                }
            }

            return $data;
        }

        if ($this->returnType == 'array') {
            $data['data']['username'] = $users['u_' . $data['data']['user_id']]['username'];
            $data['data']['email'] = $users['u_' . $data['data']['user_id']]['email'];
            $data['data']['citizen_number'] = $users['u_' . $data['data']['user_id']]['citizen_number'];
            $data['data']['active'] = $users['u_' . $data['data']['user_id']]['active'];
            $data['data']['role_id'] = $users['u_' . $data['data']['user_id']]['role_id'];
            $data['data']['role_name'] = $users['u_' . $data['data']['user_id']]['role_name'];
            $data['data']['country_id'] = $locMap[$data['data']['user_id']]['country_id'];
            $data['data']['country_name'] = $locMap[$data['data']['user_id']]['country_name'];
            $data['data']['state_id'] = $locMap[$data['data']['user_id']]['state_id'];
            $data['data']['state_name'] = $locMap[$data['data']['user_id']]['state_name'];
            $data['data']['city_id'] = $locMap[$data['data']['user_id']]['city_id'];
            $data['data']['city_name'] = $locMap[$data['data']['user_id']]['city_name'];
            $data['data']['social_media'] = array_key_exists($data['data']['user_id'], $socialMedia) ? $socialMedia[$data['data']['user_id']] : [];
        } else {
            $data['data']->username = $users['u_' . $data['data']->user_id]['username'];
            $data['data']->email = $users['u_' . $data['data']->user_id]['email'];
            $data['data']->citizen_number = $users['u_' . $data['data']->user_id]['citizen_number'];
            $data['data']->active = $users['u_' . $data['data']->user_id]['active'];
            $data['data']->role_id = $users['u_' . $data['data']->user_id]['role_id'];
            $data['data']->role_name = $users['u_' . $data['data']->user_id]['role_name'];
            $data['data']->country_id = $locMap[$data['data']->user_id]['country_id'];
            $data['data']->country_name = $locMap[$data['data']->user_id]['country_name'];
            $data['data']->state_id = $locMap[$data['data']->user_id]['state_id'];
            $data['data']->state_name = $locMap[$data['data']->user_id]['state_name'];
            $data['data']->city_id = $locMap[$data['data']->user_id]['city_id'];
            $data['data']->city_name = $locMap[$data['data']->user_id]['city_name'];
            $data['data']->social_media = array_key_exists($data['data']->user_id, $socialMedia) ? $users[$data['data']->user_id] : [];
        }

        return $data;
    }
}
