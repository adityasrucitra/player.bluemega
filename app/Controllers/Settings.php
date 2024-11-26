<?php

namespace App\Controllers;

class Settings extends BaseController
{
    /**
     * .
     */
    public function __construct()
    {
        $this->settingModel = new \App\Models\SettingModel();
        $this->validation = \Config\Services::validation();

        $this->css = [];
        $this->js = [];
        date_default_timezone_set('UTC');
    }

    /**
     * .
     */
    public function index()
    {
        if (!$this->authorize->hasPermission('management.setting.view', intval(user()->id))) {
            return redirect('Dashboard::index');
        }
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
            'plugins/toastr/toastr.min.js',
            'asset/js/sweetalert2.all.min.js',
        );

        array_push(
            $this->css,
            'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
            'plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
            'plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
            'plugins/toastr/toastr.css',
        );

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Settings',
        ];

        return view('settings/index', $data);
    }

    /**
     * .
     */
    public function findOne()
    {
        $id = $this->request->getPost('setting_id');
        if ($this->validation->check($id, 'required')) {
            $setting = $this->settingModel->find($id);
            $res = [
                'status' => true,
                'setting' => $setting,
            ];

            return $this->response->setJSON($res);
        }

        return $this->response->setJOSN([
            'status' => false,
            'setting' => null,
        ]);
    }

    /**
     * .
     */
    public function findAll()
    {
        $draw = $this->request->getPost('draw');

        $searchTerm = $this->request->getPost('search');
        $searchTerm = $searchTerm['value'];

        if ($searchTerm) {
            $allData = $this->settingModel->like('setting_name', $searchTerm)->findAll();
        } else {
            $allData = $this->settingModel->findAll();
        }

        $recordsTotal = $this->db->table('settings')->countAll();

        $recordsFiltered = count($allData);

        $data = [];
        foreach ($allData as $d) {
            $data[] = [
                $d['id'],
                $d['setting_name'],
                $d['value'],
                '<div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-tools"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <button class="dropdown-item" onclick="edit('.$d['id'].')">Edit</button>
                  <button class="dropdown-item" onclick="remove('.$d['id'].')">Delete</button>
                </div>
              </div>',
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * .
     */
    public function add()
    {
        if (!$this->authorize->hasPermission('management.setting.add', intval(user()->id))) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'You don\'t have sufficient right to add new setting',
            ]);
        }

        $fields['setting_name'] = $this->request->getPost('setting_name');
        $fields['value'] = $this->request->getPost('value');
        $fields['id'] = $this->request->getPost('setting_id');

        $rules = [
            'setting_name' => 'required',
            'value' => 'required',
        ];
        $this->validation->setRules($rules);
        if ($this->validation->run($fields)) {
            if ($this->settingModel->save($fields)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => $fields['id'] ? 'Successfully update setting' : 'Successfully add new setting',
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Fail add new setting',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $this->validation->getErrors(),
            ]);
        }
    }

    /**
     * .
     */
    public function update()
    {
        if (!$this->authorize->hasPermission('management.setting.edit', intval(user()->id))) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'You don\'t have sufficient right to modify existing setting',
            ]);
        }
        $fields = [
            'setting_id' => $this->request->getPost('setting_id'),
            'setting_name' => $this->request->getPost('setting_name'),
        ];
        $rules = [
            'setting_id' => 'required|numeric',
            'setting_name' => 'required',
        ];
        if ($this->validation->run($fields, $rules)) {
            if ($this->settingModel->update($fields['setting_id'], $fields['setting_name'])) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Setting successfully updated',
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Setting fail update',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Setting fail update',
            ]);
        }
    }

    /**
     * .
     */
    public function delete()
    {
        if (!$this->authorize->hasPermission('management.setting.delete', intval(user()->id))) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'You don\'t have sufficient right to delete setting',
            ]);
        }
        $resp = [
            'status' => false,
            'message' => 'Delete fail!',
        ];
        $id = $this->request->getPost('setting_id');
        if ($this->validation->check($id, 'required')) {
            if ($this->settingModel->delete($id)) {
                $resp['status'] = true;
                $resp['message'] = 'Successfully delete setting';
            }
        }

        return $this->response->setJSON($resp);
    }
}
