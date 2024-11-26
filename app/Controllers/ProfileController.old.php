<?php

namespace App\Controllers;

class ProfileController extends BaseController
{
    protected $profileModel;
    protected $validation;
    protected $userModel;
    protected $css;
    protected $js;
    protected $session;

    public function __construct()
    {
        $this->profileModel = new \App\Models\ProfileModel();
        $this->validation = \Config\Services::validation();
        $this->userModel = new \Myth\Auth\Models\UserModel();
        $this->css = [];
        $this->js = [];
        helper('auth');
        helper('form');
        $this->session = \Config\Services::session();
    }

    /**
     * .
     */
    public function index($userId = null)
    {
        if (!$this->authorize->hasPermission('management.profile.view', user()->id)) {
            return redirect('Dashboard::index');
        }

        array_push(
            $this->js,
            'plugins/bootstrap-switch/js/bootstrap-switch.min.js',
            'plugins/select2/js/select2.full.min.js',
            'view_onload/js/profile_index.js'
        );

        array_push(
            $this->css,
            'plugins/select2/css/select2.min.css',
            'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        );
        $id = !is_null($userId) ? $userId : user()->id;
        $user = null;
        if ($userId != null) {
            $user = $this->userModel->find($userId);
        }
        $data = [
            'title' => 'Profile',
            'css' => $this->css,
            'js' => $this->js,
            'userId' => $id,
            'user' => $user,
            'targetUrl' => base_url('profile/getprofile/' . $id),
            'authorize' => $this->authorize
        ];

        if ($this->popup) {
            if (!$this->session->has('notifications_acknowledged')) {
                $data['popup'] = $this->popup;
            }
        }

        return view('profile/index', $data);
    }

    /**
     * .
     */
    public function getProfile($userId = null)
    {
        $response = ['status' => false];
        if (!$this->authorize->hasPermission('management.profile.view', user()->id)) {
            return $this->response->setJSON($response);
        }

        $id = !is_null($userId) ? $userId : user()->id;

        $data = $this->profileModel->where('user_id', $id)->limit(1)->findAll();
        // $data = $this->profileModel->findAll();

        if (count($data) > 0) {
            $str = $data[0]['citizen_number'];
            if ($str != '') {
                $data[0]['qr_code'] = $this->profileModel->getQRCode($str);
            } else {
                $data[0]['qr_code'] = '';
            }

            return $this->response->setJSON($data[0]);
        }
    }

    /**
     * .
     */
    public function updateProfile()
    {
        $response = ['status' => false];
        if (!$this->authorize->hasPermission('management.profile.edit', user()->id)) {
            return redirect('Dashboard::index');
        }

        $inputValidation = [
            'first_name' => 'required',
            'last_name' => 'required',
            // 'city' => 'required',
            // 'country' => 'required',
            'phone_number' => 'required',
        ];

        if (!$this->validate($inputValidation)) {
            $data = [
                'title' => 'Update profile',
                'css' => $this->css,
                'js' => $this->js,
            ];

            return redirect()->to('profile/' . $this->request->getPost('user_id'));
        }

        $data = $this->request->getPost();

        //ommit group permission change if no permission to update
        if (!$this->authorize->hasPermission('management.account.edit', user()->id)) {
            if (isset($data['input_role'])) {
                unset($data['input_role']);
            }
        }

        if (isset($data['active']) && $data['active'] == 'on') {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        $tmp = [
            'active' => $data['active']
        ];

        $userModel = new \Myth\Auth\Models\UserModel();
        if (isset($data['password1']) || isset($data['password2'])) {
            if ($data['password1'] === $data['password2']) {
                $tmp['password_hash'] = \Myth\Auth\Password::hash($data['password1']);
            }
        }

        $userModel->update($data['user_id'], $tmp);

        $img = $this->request->getFile('profile_image');
        if ($img->getError() == 0) {
            $randomName = $img->getRandomName();
            $filePath = 'uploads/users/' . $data['user_id'] . '/' . $randomName;
            $img->move('uploads/users/' . $data['user_id'] . '/', $randomName);
            $data['profile_image'] = $filePath;
        }

        $data['id'] = $data['profile_id'];
        unset($data['profile_id']);

        $this->profileModel->update($data['id'], $data);

        if (isset($data['sm_telegram'])) {
            $smTelegram = $this->db->table('users_social_media usm')
                ->select('*')
                ->where('usm.user_id', $data['user_id'])
                ->where('usm.social_media', 'telegram')
                ->get()->getRowArray();
            if (!$smTelegram) {
                $this->db->table('users_social_media')
                   ->insert([
                       'user_id' => $data['user_id'],
                       'social_media' => 'telegram',
                       'username' => $data['sm_telegram']
                   ]);
            } else {
                $up = $this->db->table('users_social_media usm')
                    ->set('usm.username', $data['sm_telegram']);
                if ($data['sm_telegram'] !== $smTelegram['username']) {
                    $up = $up->set('usm.sm_user_id', null);
                }
                $up = $up->where('usm.user_id', $data['user_id'])
                    ->where('usm.social_media', 'telegram')
                    ->update();
            }
        }

        //update timezone in $_SESSION
        $session = \Config\Services::session();
        $session->set(['timezone' => $data['timezone']]);

        if ($this->authorize->hasPermission('management.account.edit', user()->id)) {
            //assign to spesified group
            if ($this->db->table('auth_groups_users')->where('user_id', $this->request->getPost('user_id'))->delete()) {
                $this->authorize->addUserToGroup($this->request->getPost('user_id'), $this->request->getPost('input_role'));
            }
        }

        //clear cache
        cache()->clean();

        array_push(
            $this->js,
            'plugins/bootstrap-switch/js/bootstrap-switch.min.js',
            'plugins/select2/js/select2.full.min.js',
            'view_onload/js/profile_index.js'
        );

        array_push(
            $this->css,
            'plugins/select2/css/select2.min.css',
            'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        );
        $data = [
            'title' => 'Profile',
            'css' => $this->css,
            'js' => $this->js,
            // 'userId' => $id,
            // 'user' => $user,
            'targetUrl' => base_url('profile/getprofile'),
            'authorize' => $this->authorize
        ];

        return view('profile/index', $data);
    }

    /**
     * .
     */
    public function getTimezones()
    {
        $term = $this->request->getPost('term');

        $timezones = [];
        $i = 0;
        foreach (\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $tz) {
            if (!$term) {
                $timezones[] = [
                    'id' => $i,
                    'text' => $tz,
                ];
            } else {
                if (stripos($tz, $term) !== false) {
                    $timezones[] = [
                        'id' => $i,
                        'text' => $tz,
                        'type' => 2,
                    ];
                }
            }
            ++$i;
        }

        return $this->response->setJSON($timezones);
    }

    /**
    * .
    */
    public function getCountries()
    {
        $term = $this->request->getPost('term');

        $countries = $this->profileModel->getCountries($term);

        return $this->response->setJSON($countries);
    }

    /**
     * .
     */
    public function getStates()
    {
        $term = $this->request->getPost('term');
        $countryId = $this->request->getPost('countryId');

        $states = $this->profileModel->getStates($term, $countryId);

        return $this->response->setJSON($states);
    }

    /**
     * .
     */
    public function getCities()
    {
        $term = $this->request->getPost('term');
        $countryId = $this->request->getPost('countryId');
        $stateId = $this->request->getPost('stateId');

        $cities = $this->profileModel->getCities($term, $countryId, $stateId);

        return $this->response->setJSON($cities);
    }
}
