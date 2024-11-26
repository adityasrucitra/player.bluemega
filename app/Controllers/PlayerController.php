<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PlayerController extends BaseController
{
    protected $validation;
    protected $authorize;
    protected $css;
    protected $js;
    protected $playerModel;

    /**
    * .
    */
    public function __construct()
    {
        $this->authorize = service('authorization');
        $this->validation = \Config\Services::validation();
        $this->css = [];
        $this->js = [];
        $this->playerModel = new \App\Models\PlayerModel();
    }

    /**
     *
     */
    public function index()
    {
        if (!$this->authorize->hasPermission('tool.player.view', intval(user()->id))) {
            return redirect('ProfileController::index');
        }

        array_push(
            $this->css,
            'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
            'plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
            'plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
            'plugins/select2/css/select2.min.css',
            'plugins/daterangepicker/daterangepicker.css'
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
            'plugins/moment/moment.min.js',
            'plugins/daterangepicker/daterangepicker.js'
        );

        $data = [
            'css' => $this->css,
            'js' => $this->js,
            'title' => 'Recorded Files',
        ];

        return view('player/index', $data);
    }

    /**
     *
     */
    public function getOne()
    {
    }

    /**
     * .
     */
    public function getAll()
    {
        $offset = $this->request->getPost('start') ?? $this->request->getPost('start');
        $limit = $this->request->getPost('length') ?? $this->request->getPost('length');

        $filters = [];
        if ($this->request->getPost('channel') == 0) {
            $filters['channel_name'] = $this->request->getPost('channel');
        } elseif ($this->request->getPost('channel')) {
            $filters['channel_name'] = $this->request->getPost('channel');
        }

        if ($this->request->getPost('file_name')) {
            $filters['file_name'] = $this->request->getPost('file_name');
        }

        if ($this->request->getPost('time_start')) {
            $filters['time_start'] = $this->request->getPost('time_start');
        }

        if ($this->request->getPost('time_end')) {
            $filters['time_end'] = $this->request->getPost('time_end');
        }

        $targetDirectory = WRITEPATH . 'recorded';

        $response = [
            'draw' => $this->request->getPost('draw'),
            'data' => []
        ];
        $files = $this->playerModel->getAll($limit, $offset, $filters, $targetDirectory);
        $response['recordsFiltered'] = $files['recordsFiltered'];
        $response['recordsTotal'] = $files['recordsTotal'];
        $i = $offset + 1;
        foreach ($files['data'] as $d) {
            $str = " 
            <audio controls>
                <source src='{$d['path']}' type='audio/mp3'>
                <source src='{$d['path']}' type='audio/wav'>
                Your browser does not support the audio element.
            </audio>
            ";
            $strDownload = "
            <a href='{$d['path']}' download='{$d['path']}'>
                <button>
                   <i class='fas fa-download'></i> Download Audio
                </button>
            </a>
            ";
            $strCheckBox = "
            <div class='input-group mb-3'>
                <input type='checkbox'>
            </div>
            ";
            $response['data'][] = [
                $i,
                $d['file_name'],
                $str,
                $strDownload,
                // $strCheckBox
            ];
            $i++;
        }

        return $this->response->setJSON($response);
    }
}
