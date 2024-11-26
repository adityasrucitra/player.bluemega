<?php

namespace App\Controllers;

class PermissionsController extends BaseController
{
    /**
     * .
     */
    public function __construct()
    {
        $this->authorize = service('authorization');
        $this->validation = \Config\Services::validation();
        $this->permissionModel = new \App\Models\PermissionModel();
        $this->css = [];
        $this->js = [];
    }

    /**
     * .
     */
    public function index()
    {
        if (!$this->authorize->hasPermission('management.permission.view', intval(user()->id))) {
            return redirect('ProfileController::index');
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
            'asset/js/bootstrap4-toggle.min.js',
            'asset/js/sweetalert2.all.min.js',
            'asset/js/jquery.validate.min.js',
            'plugins/select2/js/select2.full.min.js',
            'plugins/qrCode/js/html5-qrcode.min.js'
        );

        array_push(
            $this->css,
            'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
            'plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
            'plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
            'plugins/select2/css/select2.min.css',
            'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        );
        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'controller' => 'permissioncontroller',
            'title' => 'Permissions',
        ];

        return view('permission/index', $data);
    }

    /**
     * .
     */
    public function getAll()
    {
        $draw = $this->request->getPost('draw');
        $row = $this->request->getPost('start');
        $rowperpage = $this->request->getPost('length'); // Rows display per page
        $columnIndex = $this->request->getPost('order')[0]['column']; // Column index
        $columnName = $this->request->getPost('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $this->request->getPost('order')[0]['dir']; // asc or desc

        $data = $this->permissionModel
            ->orderBy($columnName, $columnSortOrder)
            ->findAll($rowperpage, $row);

        for ($i = 0; $i < count($data); ++$i) {
            $ops = '<div class="btn-group">';
            $ops .= '<button type="button" class=" btn btn-sm dropdown-toggle btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $ops .= '<i class="fas fa-pen"></i>  </button>';
            $ops .= '<div class="dropdown-menu">';
            $ops .= '<a class="dropdown-item text-info" onClick="update(' . $data[$i]->id . ')"><i class="fas fa-edit"></i> ' . lang('App.edit') . '</a>';
            $ops .= '<div class="dropdown-divider"></div>';
            $ops .= '<a class="dropdown-item text-danger" onClick="remove(' . $data[$i]->id . ')"><i class="fas fa-trash"></i>' . lang('App.delete') . '</a>';
            $ops .= '</div></div>';
            $data[$i]->action = $ops;
        }

        $totalRows = $this->db->table('auth_permissions')->select('id')
            ->get()->getNumRows();
        $displayRecord = is_null($data) ? 0 : count($data);
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => !is_null($data) ? $data : [],
        ];

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function getOne()
    {
        $permission = null;
        $id = $this->request->getPost('permission_id');
        if (!$this->validation->check($id, 'required')) {
            return $this->response->setJSON($permission);
        }

        $permission = $this->permissionModel->find($id);

        return $this->response->setJSON($permission);
    }

    /**
     * .
     */
    public function add()
    {
        $response = [
            'status' => false,
            'action' => 'add',
        ];

        if (!$this->authorize->hasPermission('management.permission.add', user()->id)) {
            $response['status'] = false;
            $response['messages'] = 'You dont\'t have sufficient right to add new assignment';

            return $this->response->setJSON($response);
        }

        $fields = [
            'name' => $this->request->getPost('permission_name'),
            'description' => $this->request->getPost('permission_description'),
        ];
        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];
        $this->validation->setRules($rules);
        if (!$this->validation->run($fields)) {
            $response['messages'] = 'Error input';

            return $this->response->setJSON($response);
        }

        if ($this->permissionModel->save($fields)) {
            $response['status'] = true;
            $response['messages'] = 'Successfully add new assignment';
        }

        $response['messages'] = $this->permissionModel->errors();

        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function update()
    {
        $response = [
            'status' => false,
            'action' => 'update',
        ];

        if (!$this->authorize->hasPermission('management.permission.edit', user()->id)) {
            $response['status'] = false;
            $response['messages'] = 'You dont\'t have sufficient right to modify existing assignment';

            return $this->response->setJSON($response);
        }

        $fields = [
            'id' => $this->request->getPost('permission_id'),
            'name' => $this->request->getPost('permission_name'),
            'description' => $this->request->getPost('permission_description'),
        ];
        $rules = [
            'id' => 'required',
            'name' => 'required',
            'description' => 'required',
        ];
        $this->validation->setRules($rules);
        if (!$this->validation->run($fields)) {
            $response['messages'] = 'Error input';

            return $this->response->setJSON($response);
        }

        if ($this->permissionModel->save($fields)) {
            $response['status'] = true;
            $response['messages'] = 'Successfully update assignment';
        }

        $response['messages'] = $this->permissionModel->errors();
        return $this->response->setJSON($response);
    }

    /**
     * .
     */
    public function delete()
    {
        $resp = [
            'status' => false,
            'action' => 'delete',
        ];

        if (!$this->authorize->hasPermission('management.permission.delete', user()->id)) {
            $response['status'] = false;
            $response['messages'] = 'You dont\'t have sufficient right to delete existing assignment';

            return $this->response->setJSON($response);
        }

        $id = $this->request->getPost('permission_id');
        if (!$this->validation->check($id, 'required')) {
            $response['messages'] = 'Error input';

            return $this->response->setJSON($resp);
        }

        if ($this->permissionModel->delete($id)) {
            $resp['status'] = true;
            $response['messages'] = 'Successfully delete assignment';
        }

        return $this->response->setJSON($resp);
    }
}
