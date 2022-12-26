<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\API\ResponseTrait;

class Session extends BaseController
{
    use ResponseTrait;
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->model = new \App\Models\Session();
    }

    public function index()
    {
        return view('session/index', [
            'title'     => 'Daftar Sesi',
            'alert'     => $this->session->alert,
        ]);
    }

    public function show($id)
    {
        $sess = $this->model->find($id);
        if (is_null($sess)) {
            return $this->failNotFound('data tidak ditemukan');
        }
        $time = new \DateTime();
        $time->setTimestamp($sess->criterion_time);
        $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $sess->date = $time->format('Y-m-d');
        $sess->time = $time->format('H:i');
        return $this->respond($sess);
    }

    public function list()
    {
        $sessions = $this->model->orderBy('criterion_time', 'ASC')->findAll(); // array objek sesi
        $count = 1;
        foreach ($sessions as &$sess) {
            $time = (new \DateTime());
            $time->setTimestamp($sess->criterion_time);
            $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            $sess->date = $time->format('Y-m-d');
            $sess->time = $time->format('H:i:s');
            $sess->counter = $count++;
            $sess->action = "<button type='button' onclick='edit(".$sess->id
            .")' class='btn btn-warning btn-sm'><i class='bi-pencil-fill'></i></a>"
            ."<button type='button' onclick='del(".$sess->id
            .")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></a>";
        }
        unset($sess);// wajib
        $data = ['data' => $sessions];
        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getVar();
        $datetime = \DateTime::createFromFormat('Y-m-d H:i', 
            $data->date.' '.$data->time, (new \DateTimeZone('Asia/Jakarta')));
        $data->criterion_time = $datetime->getTimestamp();
        $res = false;
        try {
            $sess = $this->model->onlyDeleted()->where('name', $data->name)->first();
            if (is_null($sess)) {
                $res = $this->model->insert($data, false);
            } else {
                $data['deleted_at'] = null;
                $res = $this->model->update($sess->id,$data);
            }
        } catch (\ErrorException $e) {
            return $this->failResourceExists('simpan error, cek nama!');
        }
        if ($res) {
            return $this->respondCreated($data);
        } else {
            return $this->fail('unknown error', 400);
        }
    }

    public function update($id)
    {
        $data = $this->request->getVar();
        $datetime = \DateTime::createFromFormat('Y-m-d H:i', 
            $data->date.' '.$data->time, (new \DateTimeZone('Asia/Jakarta')));
        $data->criterion_time = $datetime->getTimestamp();
        try {
            $this->model->update($id,$data);
            return $this->respondCreated([]);
        } catch (\ErrorException $e) {
            return $this->failNotFound('resource tidak ditemukan');
        }
    }

    public function delete($id)
    {
        try {
            $this->model->delete($id);
            return $this->respondDeleted([]);
        } catch (\ErrorException $e) {
            return $this->failNotFound('resource tidak ditemukan');
        }
    }
}
