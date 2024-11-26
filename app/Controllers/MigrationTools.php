<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MigrationTools extends BaseController
{
    protected $migrationModel;

    public function __construct()
    {
        $this->migrationModel = new \App\Models\MigrationToolsModel();
    }

    /**
     * .
     */
    public function index()
    {
    }

    /**
     * .
     */
    public function moveUsers($companyId = null)
    {
        //Get companies
        $companies = $this->migrationModel->getCompanies($companyId);
        if (!$companies) {
            return $this->response->setJSON($companies);
        }

        //Get vessels
        $companyIds = array_keys($companies);
        $vessels = $this->migrationModel->getVessels($companyIds);

        foreach ($vessels as $cId => $val) {
            if (empty($val)) {
                return $this->response->setJSON($companies);
            }
        }

        //get subscriptions
        $subscriptions = $this->migrationModel->getSubscriptions();

        //Get device
        $vesselIds = [];
        foreach ($vessels as $cId => $vess) {
            foreach ($vess as $v) {
                $vesselIds[] = $v['vessel_id'];
            }
        }
        $devSub = array_keys($subscriptions);

        $devices = $this->migrationModel->getDevices($vesselIds, $devSub);

        //Get connections
        $deviceIds = [];
        foreach ($devices as $vId => $dev) {
            foreach ($dev as $d) {
                $deviceIds[] = $d['device_id'];
            }
        }
        $connections = $this->migrationModel->getConnections($deviceIds);

        //Put it one
        foreach ($devices as $vId => $d) {
            $i = 0;
            foreach ($d as $dd) {
                if (array_key_exists($dd['device_id'], $connections)) {
                    $dd['connection'] = $connections[$dd['device_id']];
                    $devices[$vId][$i] = $dd;
                }
                if (array_key_exists($dd['device_id'], $subscriptions)) {
                    $dd['subscriptions'] = $subscriptions[$dd['device_id']];
                    $devices[$vId][$i] = $dd;
                }
                $i++;
            }
        }

        unset($connections); //Optimize memory, delete unused

        foreach ($vessels as $cId => $v) {
            $i = 0;
            foreach ($v as $vv) {
                if (array_key_exists($vv['vessel_id'], $devices)) {
                    $vv['devices'] = $devices[$vv['vessel_id']];
                    $vessels[$cId][$i] = $vv;
                }
                $i++;
            }
        }

        unset($devices); //Optimize memory, delete unused

        foreach ($companies as $cId => $cc) {
            if (array_key_exists($cId, $vessels)) {
                $companies[$cId]['vessels'] = $vessels[$cId];
            }
        }
        unset($vessels); //Optimize memory, delete unused

        $processed = false;
        foreach ($companies as $cId => $company) {
            foreach ($company['vessels'] as $vessel) {
                foreach ($vessel['devices'] as $device) {
                    $companies[$cId]['users'] = $this->migrationModel->moveUsers($device, $cId);
                    $processed = true;
                    break;
                }
                if($processed){
                    break;
                }
            }
            if($processed){
                break;
            }
        }

        return $this->response->setJSON($companies);
    }
}
