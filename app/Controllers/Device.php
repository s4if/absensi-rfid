<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\API\ResponseTrait;

class Device extends BaseController
{
    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->model = new \App\Models\Device();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        return view('device/index', [
            'title'     => 'Daftar Perangkat',
            'alert'     => $this->session->alert,
        ]);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id)
    {
        $device = $this->model->find($id);
        return (is_null($device))?$this->failNotFound('data tidak ditemukan'):$this->respond($device);
    }

    public function list()
    {
        $devices = $this->model->findAll();
        foreach ($devices as &$device) {
            $device->action = "<button type='button' onclick='edit(\"".$device->id
            ."\")' class='btn btn-warning btn-sm'><i class='bi-pencil-fill'></i></button>"
            ."<button type='button' onclick='del(\"".$device->id
            ."\")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></button>";
        }
        unset($device);// wajib
        $data = ['data' => $devices];
        return $this->respond($data);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $res = true;
        try {
            $res = $this->model->delete($id);
        } catch (\ErrorException) {
            $res = false;
        }
        if ($res) {
            $this->session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Data Berhasil Dihapus.']);
            return redirect()->to('/admin/device');
        } else {
            $this->session->setFlashdata('alert', [
                'type' => 'danger', 
                'msg' => 'Data gagal dihapus!<br>Silahkan cek data lagi!'
            ]);
            return redirect()->to('/admin/device');
        }
    }
}
