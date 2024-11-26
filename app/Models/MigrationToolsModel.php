<?php

namespace App\Models;

use CodeIgniter\Model;

class MigrationToolsModel extends Model
{
    protected $table            = 'migrationtools';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * .
     */
    public function getCompanies($companyId = null, $term = null, $userId=null)
    {
        $builder = $this->db->table('companies c')
            ->select('c.id as company_id, c.company_name, c.alias')
            ->join('users_companies uc', 'uc.company_id=c.id')
            ->where('uc.deleted_at', null)
            ->where('c.deleted_at', null);
        if ($companyId) {
            $builder = $builder->where('c.id', $companyId);
        }
        if ($term) {
            $builder = $builder->like('c.company_name', $term);
        }
        if($userId){
            $builder = $builder->where('uc.user_id', $userId);
        }
        $builder = $builder->get();
        $companies = [];
        foreach ($builder->getResultArray() as $row) {
            $cId = $row['company_id'];
            unset($row['company_id']);
            $row['vessels'] = [];
            $companies[$cId] = $row;
        }

        return $companies;
    }

    /**
     * .
     */
    public function getVessels($companyIds = [])
    {
        $builder = $this->db->table('companies c')
            ->select('c.id as company_id, v.id as vessel_id, v.name as vessel_name')
            ->join('vessels_contract vc', 'vc.company_id=c.id')
            ->join('vessels v', 'v.id=vc.vessel_id')
            ->where('c.deleted_at', null)
            ->where('vc.deleted_at', null)
            ->where('v.deleted_at', null)
            ->whereIn('c.id', $companyIds)
            ->get();
        $vessels = [];
        foreach ($companyIds as $cId) {
            $vessels[$cId] = [];
        }

        foreach ($builder->getResultArray() as $row) {
            $cId = $row['company_id'];
            $row['devices'] = [];
            unset($row['company_id']);
            $vessels[$cId][] = $row;
        }

        return $vessels;
    }

    /**
     * .
     */
    public function getDevices($vesselIds = [], $deviceIds)
    {
        $builder = $this->db->table('devices d')
            ->select('v.id as vessel_id, d.id as device_id, d.name as device_name, d.part_sequence, d.engine_sections')
            ->join('devices_in_vessel div', 'div.device_id=d.id')
            ->join('vessels v', 'v.id = div.vessel_id')
            ->where('d.deleted_at', null)
            ->where('v.deleted_at', null)
            ->where('div.deleted_at', null)
            ->whereIn('v.id', $vesselIds)
            ->whereIn('d.id', $deviceIds)
            ->get();
        $devices = [];
        foreach ($vesselIds as $vId) {
            $devices[$vId] = [];
        }
        foreach ($builder->getResultArray() as $row) {
            $vId = $row['vessel_id'];
            $row['connection'] = null;
            $row['subscriptions'] = null;
            unset($row['vessel_id']);
            $devices[$vId][] = $row;
        }

        return $devices;
    }

    /**
     * .
     */
    public function getConnections($deviceIds = [])
    {
        $builder = $this->db->table('databases db')
            ->select('d.id as device_id, db.host, db.username, db.password, db.database_name, db.port')
            ->join('devices_connection dc', 'dc.database_id = db.id')
            ->join('devices d', 'd.id=dc.device_id')
            ->where('d.deleted_at', null)
            ->where('db.deleted_at', null)
            ->where('dc.deleted_at', null)
            ->get();
        $connections = [];
        foreach ($deviceIds as $dId) {
            $connections[$dId] = null;
        }
        foreach ($builder->getResultArray() as $row) {
            $dId = $row['device_id'];
            unset($row['device_id']);
            $connections[$dId] = $row;
        }

        return $connections;
    }

     /**
     * .
     */
    public function getSubscriptions()
    {
        $today = new \DateTime('now', new \DateTimeZone('UTC'));

        $builder = $this->db->table('subscriptions s')
            ->select('s.id as subscription_id, s.device_id, sd.plan, sd.user_id, sd.created_at')
            ->join('subscriptions_details sd', 'sd.subscription_id=s.id')
            ->where('s.deleted_at', null)
            ->where('sd.deleted_at', null)
            ->orderBy('s.device_id', 'asc')
            ->get();

        $subscriptions = [];
        foreach ($builder->getResultArray() as $row) {
            $dateInterval = new \DateInterval("P{$row['plan']}M");
            $subDT = new \DateTime($row['created_at'], new \DateTimeZone('UTC'));
            $subDT->add($dateInterval);
            if ($today > $subDT) { //Expired, ommit this subscription
                continue;
            }
            $row['due_date'] = $subDT->format('Y-m-d H:i:s');
            $dId = $row['device_id'];
            unset($row['device_id']);
            $subscriptions[$dId] = $row;
        }

        return $subscriptions;
    }

    /**
     * .
     */
    public function moveUsers($device, $companyId)
    {
        $custom = [
            'DSN' => '',
            'hostname' => $device['connection']['host'],
            'username' => $device['connection']['username'],
            'password' => $device['connection']['password'],
            'database' => $device['connection']['database_name'],
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug' => true,
            'charset' => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre' => '',
            'encrypt' => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port' => $device['connection']['port'],
        ];
        $db = \Config\Database::connect($custom);

        //Get current user list
        $currentUsers = $this->db->table('users u')
            ->select('u.id as user_id, u.username, u.email')
            ->get()->getResultArray();
        $currentUserEmails = array_column($currentUsers, 'email');
        $currentUsernames = array_column($currentUsers, 'username');

        //New users
        $newUsers = $db->table('bf_users u')
            ->select('u.username, u.email, u.display_name, u.password_hash')
            ->whereNotIn('u.email', $currentUserEmails)
            ->whereNotIn('u.username', $currentUsernames)
            ->where('deleted', 0)
            ->where('active', 1)
            ->get()->getResultArray();
        
        $authorize = service('authorization');
        $ret = ['moved' =>[]]; 
        foreach($newUsers as $nu){
            // $this->db->transStart();
            $i = $this->db->table('users')->insert([                
                'username' => $nu['username'],
                'email' => $nu['email'],
                'active' => 1,
                'password_hash' => $nu['password_hash']                
            ]);
            if($i){
                $id = $this->db->insertID();
                $displayName = explode(' ', $nu['display_name']);
                $firstName = count($displayName) > 1 ? $displayName[0] : '-';
                $lastName = count($displayName) > 1 ? $displayName[1] : '-'; 
                $i2= $this->db->table('profile p')->insert([
                    'user_id' => $id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'timezone' => 'Asia/Jakarta'
                ]);
                if($i2){
                    $authorize->addUserToGroup($id, 'User');
                    $this->db->table('users_companies')->insert([
                        'user_id' => $id,
                        'company_id' => $companyId
                    ]);

                    $ret['moved'][] = [
                        'username' => $nu['username'],
                        'email' => $nu['email'],
                    ]; 
                }
            }
               
        }

        return $ret;
    }
}
