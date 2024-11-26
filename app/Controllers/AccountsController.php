<?php

namespace App\Controllers;

class AccountsController extends BaseController
{
    protected $validation;
    protected $css;
    protected $js;
    protected $config;
    protected $session;
    protected $accountModel;

    public function __construct()
    {
        $this->validation = \Config\Services::validation();
        $this->css = [];
        $this->js = [];
        helper('auth');
        helper('form');
        $this->config = config('Auth');
        $this->session = \Config\Services::session();
        $this->accountModel = new \App\Models\AccountModel();
    }

    /**
     * .
     */
    public function index()
    {
        if (!$this->authorize->hasPermission('management.account.view', intval(user()->id))) {
            return redirect('Dashboard::index');
        }

        $userModel = new \Myth\Auth\Models\UserModel();
        $accountModel = new \App\Models\AccountModel();
        if (!$this->request->getGet('offset')) {
            $offset = 0;
        } else {
            $offset = $this->request->getGet('offset');
        }
        $data = [
            'title' => 'Accounts',
            'css' => $this->css,
            'js' => $this->js,
            'users' => $accountModel->getAccountWithCredential(),
            'timezone' => $this->session->get('timezone')
        ];

        return view('accounts/index', $data);
    }

    /**
     * .
     */
    public function create()
    {
        if (!$this->authorize->hasPermission('management.account.add', intval(user()->id))) {
            return redirect('Dashboard::index');
        }

        $this->js[] = 'plugins/bootstrap-switch/js/bootstrap-switch.min.js';
        array_push(
            $this->js,
            'plugins/select2/js/select2.full.min.js',
        );
        array_push(
            $this->css,
            'plugins/select2/css/select2.min.css',
            'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        );

        $validation = \Config\Services::validation();
        $users = new \Myth\Auth\Models\UserModel();

        // Validate basics first since some password rules rely on these fields
        $rules = [
            'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'citizen_number' => 'required|numeric',
            'password' => 'required',
            'pass_confirm' => 'required|matches[password]',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
        ];
        $fields = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'pass_confirm' => $this->request->getPost('pass_confirm'),
            'citizen_number' => $this->request->getPost('citizen_number'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'active' => $this->request->getPost('active'),
            'group_id' => $this->request->getPost('input_role'),
            'timezone' => $this->request->getPost('timezone'),
            'country_id' => $this->request->getPost('country_id'),
            'state_id' => $this->request->getPost('state_id'),
            'city_id' => $this->request->getPost('city_id'),
        ];

        
        if (!$this->validate($rules)) {
            $data = [
                'title' => 'Create New Account',
                'css' => $this->css,
                'js' => $this->js,
                'old' => $fields,
                'error' => $validation->getErrors(),
            ];

            return view('accounts/create', $data);
        }

        // Save the user
        $allowedPostFields = array_merge(['password', 'citizen_number'], $this->config->validFields, $this->config->personalFields);
        $user = new \Myth\Auth\Entities\User($this->request->getPost($allowedPostFields));

        if (isset($fields['active']) && $fields['active'] == 'on') {
            $user->setActive(true);
        }
        $user->setPassword($fields['password']);

        if (!$users->save($user)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        $fields['user_id'] = $users->getInsertID();

        //assign to spesified group
        if ($fields['group_id']) {
            $this->authorize->addUserToGroup($users->getInsertID(), $fields['group_id']);
        } else {
            if (!empty($this->config->defaultUserGroup)) {
                $this->authorize->addUserToGroup($users->getInsertID(), $fields['group_id']);
            }
        }

        $profileModel = new \App\Models\ProfileModel();
        if ($profileModel->save($fields)) {
            $session = \Config\Services::session();
            if ($fields['timezone']) {
                $session->set(['timezone' => $fields['timezone']]);
            }
        }

        // Success!
        return redirect()->route('accounts')->with('message', 'New account added!');
    }

    /**
     * .
     */
    public function getRoles()
    {
        $permissions = $this->authorize->groups();
        $groups = [];
        foreach ($this->authorize->groups() as $group) {
            $groups[] = [
                'id' => $group->id,
                'text' => $group->name,
            ];
        }

        return $this->response->setJSON($groups);
    }

    /**
     * . for select2.
     */
    public function getCompanies()
    {
        $term = $this->request->getPost('term');
        $userId = $this->request->getPost('user_id');

        $builder = $this->db->table('users u')
            ->select('c.id as company_id')
            ->join('users_companies uc', 'uc.user_id=u.id')
            ->join('companies c', 'c.id=uc.company_id')
            ->where('u.id', $userId)
            ->where('uc.deleted_at', null)
            ->where('c.deleted_at', null)
            ->get();
        $usedCompanyId = [-1];
        foreach ($builder->getResultArray() as $row) {
            $usedCompanyId[] = $row['company_id'];
        }

        /////////////////////////////////////////////
        /////////////////////////////////////////////
        $companyModel = new \App\Models\CompanyModel();
        $companies = [];
        if ($term) {
            $companies = $companyModel->like('company_name', $term)->whereNotIn('id', $usedCompanyId)->findAll();
        } else {
            $companies = $companyModel->whereNotIn('id', $usedCompanyId)->findAll();
        }

        foreach ($companies as $company) {
            $companies['results'][] = [
                'id' => $company['id'],
                'text' => $company['company_name'],
            ];
        }

        return $this->response->setJSON($companies);
    }

    /*
     * .
     */
    public function companyList($userId)
    {
        array_push(
            $this->css,
            'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
            'plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
            'plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
            'plugins/select2/css/select2.min.css'
        );

        array_push(
            $this->js,
            'plugins/datatables/jquery.dataTables.min.js',
            'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js',
            'plugins/datatables-responsive/js/dataTables.responsive.min.js',
            'plugins/datatables-responsive/js/responsive.bootstrap4.min.js',
            'plugins/datatables-buttons/js/dataTables.buttons.min.js',
            'plugins/datatables-buttons/js/buttons.bootstrap4.min.js',
            'plugins/jszip/jszip.min.js',
            'plugins/pdfmake/pdfmake.min.js',
            'plugins/pdfmake/vfs_fonts.js',
            'plugins/datatables-buttons/js/buttons.html5.min.js',
            'plugins/datatables-buttons/js/buttons.print.min.js',
            'plugins/datatables-buttons/js/buttons.colVis.min.js',
            'plugins/select2/js/select2.full.min.js',
            'asset/js/sweetalert2.all.min.js',
        );

        $accountModel = new \App\Models\AccountModel();

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Bluemega::Company List',
            'subTitle' => 'Company List',
            'user' => $accountModel->getAccountWithCredential($userId),
        ];

        return view('accounts/companies', $data);
    }


    /**
     * .
     */
    public function getAllCompanies()
    {
        $userId = $this->request->getPost('user_id');

        $start = $this->request->getPost('start');
        $rowsPerPage = $this->request->getPost('length');

        $orderCol = $this->request->getPost('order')[0]['column'];
        $orderDir = $this->request->getPost('order')[0]['dir'];

        $search = $this->request->getPost('search')['value'];

        $accountModel = new \App\Models\AccountModel();
        $companies = $accountModel->getCompanies($rowsPerPage, $start, $userId, $search);

        $response = [
            'draw' => $this->request->getPost('draw'),
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'data' => [],
        ];

        $inc = 1;
        foreach ($companies['data'] as $company) {
            $ops = '<div class="btn-group">';
            $ops .= '<button type="button" class=" btn btn-sm dropdown-toggle btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $ops .= '<i class="fas fa-pen"></i>  </button>';
            $ops .= '<div class="dropdown-menu">';
            $ops .= '<a class="dropdown-item text-danger" onClick="remove(' . $company['uc_id'] . ')"><i class="fas fa-trash"></i> ' . lang('App.delete') . '</a>';
            $ops .= '<a class="dropdown-item text-default" href="' . base_url("accounts/companies/{$userId}/{$company['company_id']}") . '"> <i class="fas fa-ship"></i> ' . 'Vessels' . '</a>';
            $ops .= '</div></div>';

            $response['data'][] = [
                $inc,
                $company['company_name'],
                $company['email'],
                $company['address'],
                $ops,
            ];
            ++$inc;
        }
        $response['recordsFiltered'] = $search ? $companies['filteredCompanies'] : $companies['totalCompanies'];
        $response['recordsTotal'] = $companies['totalCompanies'];

        return $this->response->setJSON($response);
    }

    /*
     * .
     */
    public function vesselList($userId, $companyId)
    {
        array_push(
            $this->css,
            'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
            'plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
            'plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
            'plugins/select2/css/select2.min.css'
        );

        array_push(
            $this->js,
            'plugins/datatables/jquery.dataTables.min.js',
            'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js',
            'plugins/datatables-responsive/js/dataTables.responsive.min.js',
            'plugins/datatables-responsive/js/responsive.bootstrap4.min.js',
            'plugins/datatables-buttons/js/dataTables.buttons.min.js',
            'plugins/datatables-buttons/js/buttons.bootstrap4.min.js',
            'plugins/jszip/jszip.min.js',
            'plugins/pdfmake/pdfmake.min.js',
            'plugins/pdfmake/vfs_fonts.js',
            'plugins/datatables-buttons/js/buttons.html5.min.js',
            'plugins/datatables-buttons/js/buttons.print.min.js',
            'plugins/datatables-buttons/js/buttons.colVis.min.js',
            'plugins/select2/js/select2.full.min.js',
            'asset/js/sweetalert2.all.min.js',
        );

        $accountModel = new \App\Models\AccountModel();

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Bluemega::Vessel List',
            'subTitle' => 'Vessel List',
            'user' => $accountModel->getAccountWithCredential($userId),
            'companyId' => $companyId
        ];

        return view('accounts/vessels', $data);
    }

    /**
     * .
     */
    public function getAllVessels()
    {
        $userId = $this->request->getPost('user_id');
        $companyId = $this->request->getPost('company_id');

        $start = $this->request->getPost('start');
        $rowsPerPage = $this->request->getPost('length');

        $orderCol = $this->request->getPost('order')[0]['column'];
        $orderDir = $this->request->getPost('order')[0]['dir'];

        $search = $this->request->getPost('search')['value'];

        $vessels = $this->accountModel->getAllVessels($start, $rowsPerPage, $companyId, $userId, $search);

        $response = [
            'draw' => $this->request->getPost('draw'),
            'recordsFiltered' => $vessels['countFilteredRows'],
            'recordsTotal' => $vessels['countAllRows'],
            'data' => [],
        ];

        $inc = $start+1;;
        foreach ($vessels['data'] as $vessel) {
            $extraStr = !$vessel['hidden'] ? 'checked' : '';
            $opt = "<input class='checkbox-vessel' type='checkbox' 
                data-vessel-id='{$vessel['vessel_id']}'
                data-company-id='{$companyId}' 
                data-user-id='{$userId}' {$extraStr}>";
            $response['data'][] = [
                $inc,
                $vessel['vessel_name'],
                $opt,
            ];
            ++$inc;
        }

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    // function hideVessel()
    // {
    //     $fields = [
    //         'vessel_id' => $this->request->getPost('vessel_id'),
    //         'user_id' => $this->request->getPost('user_id'),
    //         'company_id' => $this->request->getPost('company_id')
    //     ];

    //     $response = [
    //         'status' => false
    //     ];

    //     $builder = $this->db->table('users_vessels_hidden')
    //         ->insert($fields);
    //     if($builder){
    //         $response['status'] = true;
    //     }

    //     return $this->response->setJSON($response);
    // }

     /**
     * .
     */
    function toggleVessel()
    {
        $fields = [
            'user_id' => $this->request->getPost('user_id'),
            'vessel_id' => $this->request->getPost('vessel_id'),           
            'company_id' => $this->request->getPost('company_id'),
            'visible' => filter_var($this->request->getPost('visible'), FILTER_VALIDATE_BOOLEAN)
        ];        

        $response = [
            'status' => false
        ];

        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        $today = $today->format('Y-m-d H:i:s');
        $doFind = $this->db->table('users_vessels_hidden')
            ->where('user_id', $fields['user_id'])
            ->where('company_id', $fields['company_id'])
            ->where('vessel_id', $fields['vessel_id'])
            ->get()->getRowArray();
        if($doFind){
            $doUpdate = $this->db->table('users_vessels_hidden');
            if($fields['visible'] === true){
                $doUpdate = $doUpdate->set('deleted_at', null);
            }else{
                $doUpdate = $doUpdate->set('deleted_at', $today);
            }
            unset($fields['visible']);
            $doUpdate = $doUpdate->where('user_id', $fields['user_id'])
                ->where('company_id', $fields['company_id'])
                ->where('vessel_id', $fields['vessel_id'])->update(); 
            if($doUpdate){
                $response['status'] = true;
            }
        }else{           
            $doInsert = $this->db->table('users_vessels_hidden');
            if(!$fields['visible']){
                $fields['deleted_at'] = $today;
            }
            unset($fields['visible']);
            $doInsert = $doInsert->insert($fields);
            if($doInsert){
                $response['status'] = true;
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function deleteCompany()
    {
        $response = [
            'status' => false,
        ];

        $ucId = $this->request->getPost('uc_id');

        $today = new \DateTime('now', new \DateTimeZone('UTC'));

        $builder = $this->db->table('users_companies uc')
            ->set('updated_at', $today->format('Y-m-d H:i:s'))
            ->set('deleted_at', $today->format('Y-m-d H:i:s'))
            ->where('id', $ucId);
        if ($builder->update()) {
            $response['status'] = true;
        }

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function addCompany()
    {
        $response = [
            'status' => false,
        ];

        $fields = [
            'companyId' => $this->request->getPost('company_id'),
            'userId' => $this->request->getPost('user_id'),
        ];

        $rules = [
            'companyId' => 'required|numeric',
            'userId' => 'required|numeric',
        ];

        $this->validation->setRules($rules);

        if (!$this->validation->run($fields)) {
            $response['errors'] = $this->validation->getErrors();

            return $this->response->setJSON($response);
        }

        $accountModel = new \App\Models\AccountModel();
        $addNew = $accountModel->addCompany($fields['userId'], $fields['companyId']);
        $response['status'] = $addNew['status'];

        return $this->response->setJSON($response);
    }

    /**
    * .
    */
    public function getCountries()
    {
        $term = $this->request->getPost('term');

        $countries = $this->accountModel->getCountries($term);

        return $this->response->setJSON($countries);
    }

    /**
     * .
     */
    public function getStates()
    {
        $term = $this->request->getPost('term');
        $countryId = $this->request->getPost('countryId');

        $states = $this->accountModel->getStates($term, $countryId);

        return $this->response->setJSON($states);
    }

    /**
     * .
     */
    public function getCities()
    {
        $term = $this->request->getPost('term');
        $stateId = $this->request->getPost('stateId');

        $cities = $this->accountModel->getCities($term, $stateId);

        return $this->response->setJSON($cities);
    }
}
