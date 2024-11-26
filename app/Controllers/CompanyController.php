<?php

namespace App\Controllers;

class CompanyController extends BaseController
{
    protected $authorize;
    protected $validation;
    protected $companyModel;
    protected $css;
    protected $js;
    protected $session;

    /**
     * .
     */
    public function __construct()
    {
        $this->authorize = service('authorization');
        $this->validation = \Config\Services::validation();
        $this->companyModel = new \App\Models\CompanyModel();
        $this->css = [];
        $this->js = [];
        $this->session = \Config\Services::session();
    }

    /**
     * .
     */
    public function index()
    {
        if (!$this->authorize->hasPermission('management.company.view', user()->id)) {
            return redirect('Dashboard::index');
        }

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

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Bluemega::Companies',
            'timezone' => $this->session->get('timezone')
        ];

        return view('company/index', $data);
    }

    /**
     * .
     */
    public function getOne()
    {
        $companyId = $this->request->getPost('company_id');
        $response = ['status' => false];
        if (!$this->validation->check($companyId, 'required|numeric')) {
            $response['errors'] = $this->validation->getErrors();

            return $this->response->setJSON($response);
        }
        $company = $this->companyModel->find($companyId);
        $response['status'] = true;
        $response['company'] = $company;

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function getAll()
    {
        $start = $this->request->getPost('start') ?? $this->request->getPost('start');
        $rowsPerPage = $this->request->getPost('length') ?? $this->request->getPost('length');

        $orderCol = $this->request->getPost('order')[0]['column'];
        $orderDir = $this->request->getPost('order')[0]['dir'];

        $cols = ['company_name', 'city', 'country', 'phone_number', 'email', 'address'];

        $companies = $this->companyModel;
        if ($this->request->getPost('search')['value']) {
            $companies = $companies->like('company_name', $this->request->getPost('search')['value']);
        }

        // if ($orderCol) {
        $companies = $companies->orderBy('company_name', 'asc');
        // }

        $companies = $companies->findAll($rowsPerPage, $start);

        $response = [
            'draw' => $this->request->getPost('draw'),
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'data' => [],
            'orderCol' => $cols[$orderCol],
            'orderDir' => $orderDir,
            'search' => $this->request->getPost('search')['value'],
        ];

        foreach ($companies as $company) {
            $ops = '<div class="btn-group">';
            $ops .= '<button type="button" class=" btn btn-sm dropdown-toggle btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $ops .= '<i class="fas fa-pen"></i>  </button>';
            $ops .= '<div class="dropdown-menu">';
            $ops .= '<a class="dropdown-item text-info" href="companies/update/' . $company['id'] . '"><i class="fas fa-edit"></i> ' . lang('App.edit') . '</a>';
            $ops .= '<a class="dropdown-item text-info" href="companies/vessellist/' . $company['id'] . '"><i class="fas fa-edit"></i> ' . 'Vessels' . '</a>';
            $ops .= '<a class="dropdown-item text-info" href="companies/getuserscompany/' . $company['id'] . '"><i class="fas fa-users"></i> ' . 'Users' . '</a>';
            $ops .= '<div class="dropdown-divider"></div>';
            $ops .= '<a class="dropdown-item text-danger" onClick="remove(' . $company['id'] . ')"><i class="fas fa-trash"></i>' . lang('App.delete') . '</a>';
            $ops .= '</div></div>';

            $response['data'][] = [
                $company['company_name'],
                $company['city_name'],
                $company['state_name'],
                $company['country_name'],
                $company['phone_number'],
                $company['email'],
                $company['address'],
                $company['action'] = $ops,
            ];
        }
        $keyword = $this->request->getPost('search.value');
        $response['recordsFiltered'] = $this->request->getPost('search')['value'] ? count($companies) : $this->db->table('companies')->where('deleted_at IS NULL')->countAllResults();
        $response['recordsTotal'] = $this->db->table('companies')->where('deleted_at IS NULL')->countAllResults();

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function add()
    {
        if (!$this->authorize->hasPermission('management.company.add', user()->id)) {
            return redirect('Dashboard::index');
        }

        $fields = [
            'id' => $this->request->getPost('company_id'),
            'company_name' => $this->request->getPost('company_name'),
            'country_id' => $this->request->getPost('country_id'),
            'country_name' => $this->request->getPost('country_name'),
            'state_id' => $this->request->getPost('state_id'),
            'state_name' => $this->request->getPost('state_name'),
            'city_id' => $this->request->getPost('city_id'),
            'city_name' => $this->request->getPost('city_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ];
        $rules = [
            'company_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Company name required!',
                ],
            ],
            'country_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Country name required!',
                ],
            ],
            'state_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'State name required!',
                ],
            ],
            'city_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'City name required!',
                ],
            ],
            'phone_number' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Phone number required!',
                ],
            ],
            'email' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Email required!',
                ],
            ],
            'address' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Address required!',
                ],
            ],
        ];

        $this->validation->setRules($rules);

        $response = ['status' => false];

        if (!$this->validation->run($fields)) {
            $response['errors'] = $this->validation->getErrors();
            $errors = [];
            foreach ($this->validation->getErrors() as $field => $errorMessage) {
                $errors[$field] = '<div class="invalid-feedback">' . $errorMessage . '</div>';
            }

            array_push(
                $this->css,
                'plugins/select2/css/select2.min.css'
            );

            array_push(
                $this->js,
                'plugins/select2/js/select2.full.min.js'
            );

            $data = [
                'css' => $this->css,
                'js' => $this->js,
                'title' => 'Bluemega::Companies',
                'validation_errors' => $errors,
                'company' => $fields,
            ];

            return view('company/create', $data);
        }

        if (!$fields['id']) {
            $code = '';
            while (true) {
                for ($i = 0; $i < 8; ++$i) {
                    $code .= mt_rand(0, 9);
                }
                $c = $this->companyModel->builder()->where('unique_code', $code)->get()->getResult();
                if (!$c) {
                    break;
                }
            }

            $fields['unique_code'] = $code;
        }

        if ($this->companyModel->save($fields)) {
            return redirect()->to('companies');
        }
    }

    /**
     * .
     */
    public function update($companyId)
    {
        if (!$this->authorize->hasPermission('management.company.edit', user()->id)) {
            return redirect('Dashboard::index');
        }
        $response = ['status' => false];
        if (!$this->validation->check($companyId, 'required|numeric')) {
            $response['errors'] = $this->validation->getErrors();

            return $this->response->setJSON($response);
        }
        $company = $this->companyModel->find($companyId);

        array_push(
            $this->css,
            'plugins/select2/css/select2.min.css'
        );

        array_push(
            $this->js,
            'plugins/select2/js/select2.full.min.js'
        );

        $rules = [
            'company_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Company name required!',
                ],
            ],
            'country_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Country name required!',
                ],
            ],
            'state_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'State name required!',
                ],
            ],
            'city_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'City name required!',
                ],
            ],
            'phone_number' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Phone number required!',
                ],
            ],
            'email' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Email required!',
                ],
            ],
            'address' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Address required!',
                ],
            ],
        ];

        $this->validation->setRules($rules);

        $errors = [];
        if (!$this->validation->run($company)) {
            foreach ($this->validation->getErrors() as $field => $errorMessage) {
                $errors[$field] = '<div class="invalid-feedback">' . $errorMessage . '</div>';
            }
        }

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'company' => $company,
            'action' => 'update',
            'title' => 'Bluemega::Update Company',
            'company_id' => $companyId,
            'validation_errors' => $errors,
        ];

        return view('company/create', $data);
    }

    /**
     * .
     */
    public function delete()
    {
        if (!$this->authorize->hasPermission('management.company.delete', user()->id)) {
            return redirect('Dashboard::index');
        }

        $companyId = $this->request->getPost('company_id');
        $response = ['status' => false];
        if (!$this->validation->check($companyId, 'required|numeric')) {
            $response['errors'] = $this->validation->getErrors();

            return $this->response->setJSON($response);
        }

        if ($this->companyModel->delete($companyId)) {
            $response['status'] = true;
        }

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function getCountries()
    {
        $term = $this->request->getPost('term');

        $countries = $this->companyModel->getCountries($term);

        return $this->response->setJSON($countries);
    }

    /**
     * .
     */
    public function getStates()
    {
        $term = $this->request->getPost('term');
        $countryId = $this->request->getPost('countryId');

        $states = $this->companyModel->getStates($term, $countryId);

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

        $cities = $this->companyModel->getCities($term, $countryId, $stateId);

        return $this->response->setJSON($cities);
    }

    /**
     * .
     */
    public function getCompanies()
    {
        $term = $this->request->getPost('term');

        $companies = $this->companyModel->getCompanies($term);

        return $this->response->setJSON($companies);
    }

    /**
     * .
     */
    public function vesselList($companyId)
    {
        if (!$companyId) {
            return redirect('companies::index');
        }

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

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Bluemega::Vessel List',
            'subTitle' => 'Vessel list',
            'company' => $this->companyModel->find($companyId),
            'timezone' => $this->session->get('timezone')
        ];

        return view('company/vessels', $data);
    }

    /**
     * .
     */
    public function getVessels()
    {
        $companyId = $this->request->getPost('company_id');

        if (!$this->validation->check($companyId, 'required|numeric')) {
            $error['message'] = 'Company ID not supplied!';

            return $this->response->setJSON($error);
        }

        $start = $this->request->getPost('start');
        $rowsPerPage = $this->request->getPost('length');

        $orderCol = $this->request->getPost('order')[0]['column'];
        $orderDir = $this->request->getPost('order')[0]['dir'];

        $searchTerm = $this->request->getPost('search')['value'];

        $vessels = $this->companyModel->getVessels($rowsPerPage, $start, $companyId, $searchTerm);

        $response = [
            'draw' => $this->request->getPost('draw'),
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'data' => [],
            'vessels' => $vessels,
        ];

        foreach ($vessels['data'] as $vessel) {
            $response['data'][] = [
                $vessel['vessel_name'],
                $vessel['imo'],
                $vessel['mmsi'],
                $vessel['contract_start'],
                $vessel['contract_end'] ? '<span class="badge badge-danger">' . $vessel['contract_end'] . ' UTC </span>' : '<span class="badge badge-success">On Hire</span>',
            ];
        }
        $response['recordsFiltered'] = $searchTerm ? $vessels['filteredVessel'] : $vessels['totalVessel'];
        $response['recordsTotal'] = $vessels['totalVessel'];

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function getUsersCompany($companyId = null)
    {
        if ($this->request->is('GET')) {
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

            $companyModel = new \App\Models\CompanyModel();
            $company = $companyModel->find($companyId);

            $data = [
                'css' => $this->css,
                'js' => $this->js,
                'title' => 'Bluemega::User Of The Company',
                'timezone' => $this->session->get('timezone'),
                'companyId' => $companyId,
                'companyName' => $company['company_name']
            ];

            return view('company/user_company', $data);
        }

        $start = $this->request->getPost('start') ?? $this->request->getPost('start');
        $rowsPerPage = $this->request->getPost('length') ?? $this->request->getPost('length');
        $companyId = $this->request->getPost('company_id') ?? $this->request->getPost('company_id');
        $term = $this->request->getPost('search')['value'] ?? $this->request->getPost('search')['value'];

        $userCompany = $this->companyModel->getUsersCompany($start, $rowsPerPage, $companyId, $term);
        $response = [
            'draw' => $this->request->getPost('draw'),
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'data' => [],
        ];

        $i = $start + 1;
        foreach ($userCompany['data'] as $uc) {
            $ops = '<div class="btn-group">';
            $ops .= '<button type="button" class=" btn btn-sm dropdown-toggle btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $ops .= '<i class="fas fa-pen"></i>  </button>';
            $ops .= '<div class="dropdown-menu">';
            $ops .= '<a class="dropdown-item text-danger" onClick="remove(' . $uc['id'] . ')"><i class="fas fa-trash"></i>' . lang('App.delete') . '</a>';
            $ops .= '</div></div>';
            $response['data'][] = [
                $i,
                $uc['first_name'],
                $uc['last_name'],
                $uc['email'],
                $ops
            ];
            $i++;
        }
        $response['recordsFiltered'] = $userCompany['countFiltered'];
        $response['recordsTotal'] = $userCompany['countAll'];

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function removeUserCompany()
    {
        $id = $this->request->getPost('id');

        $response = [
            'status' => $this->companyModel->removeUserCompany($id)
        ];

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function getUserList()
    {
        $companyId = $this->request->getPost('company_id');
        $term = $this->request->getPost('term');

        $userList = $this->companyModel->getUserList($companyId, $term);
        $response = ['results' => []];
        foreach ($userList as $ul) {
            $response['results'][] = [
                'id' => $ul['user_id'],
                'text' => $ul['first_name'] . ' ' . $ul['last_name']
            ];
        }

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function addUserCompany()
    {
        $fields = [
            'user_id' => $this->request->getPost('user_id'),
            'company_id' => $this->request->getPost('company_id')
        ];
        $rules = [
            'user_id' => [
                'label' => 'User ID',
                'rules' => 'required',
                'errors' => [
                    'required' => 'User ID required!'
                ]
            ],
            'company_id' => [
                'label' => 'User ID',
                'rules' => 'required',
                'errors' => [
                    'required' => 'User ID required!'
                ]
            ]
        ];
        $this->validation->setRules($rules);
        $response = ['status' => false];
        if (!$this->validation->run($fields)) {
            $mess = [];
            foreach ($this->validation->getErrors() as $err) {
            }
            $response['errors'] = $this->validation->getErrors();
        }

        if (!$this->companyModel->addUserCompany($fields)) {
            $response['error'] = $this->db->error();

            return $this->response->setJSON($response);
        }

        $response['status'] = true;
        $response['message'] = 'User successfullly added';

        return $this->response->setJSON($response);
    }
}
