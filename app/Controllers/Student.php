<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\API\ResponseTrait;

class Student extends BaseController
{
    use ResponseTrait;
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->model = new \App\Models\Student();
    }

    public function index()
    {
        return view('student/index', [
            'title'     => 'List Siswa',
            'alert'     => $this->session->alert,
        ]);
    }

    public function show($id)
    {
        $student = $this->model->find($id);
        return (is_null($student))?$this->failNotFound('data tidak ditemukan'):$this->respond($student);
    }

    public function list()
    {
        $students = $this->model->findAll();
        foreach ($students as &$student) {
            $student->action = "<a href='".base_url()."/admin/edit_siswa/".$student->id
            ."' class='btn btn-warning btn-sm'><i class='bi-pencil-fill'></i></a>"
            ."<button type='button' onclick='del(".$student->id.",".$student->nis
            .")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></a>";
        }
        unset($student);// wajib
        $data = ['data' => $students];
        return $this->respond($data);
    }

    public function new() //get
    {
        $student = new \stdClass();
        $student->id = null;
        $student->nis = null;
        $student->name = null;
        $student->gender = "L";
        $student->classroom = "TKJ1";
        $student->rfid = null;
        return view('student/update', [
            'title'     => 'Tambah Siswa',
            'alert'     => $this->session->alert,
            'student'   => $student,
            'action'    => base_url().'/admin/tambah_siswa',
        ]);
    }

    public function create() //post
    {
        $data = $this->request->getPost();
        $res = true;
        try {
            // undelete kalau nis-nya sama
            $student = $this->model->onlyDeleted()->where('nis', $data['nis'])->first();
            if (is_null($student)) {
                $res = $this->model->insert($data, false);
            } else {
                $data['deleted_at'] = null;
                $res = $this->model->update($student->id,$data);
            }
        } catch (\ErrorException $e) {
            $res = false;
        }
        if ($res) {
            $this->session->setFlashdata('alert', ['type' => 'success', 'msg' => 'Data Berhasil Disimpan.']);
            return redirect()->to('/admin/siswa');
        } else {
            $this->session->setFlashdata('alert', [
                'type' => 'danger', 
                'msg' => 'Penyimpanan Data Gagal!<br>Silahkan cek data lagi!'
            ]);
            return redirect()->to('/admin/siswa');
        }
    }

    public function edit($id) //get
    {
        $student = $this->model->find($id);
        if (is_null($student)) {
            $this->session->setFlashdata('alert', [
                'type' => 'danger', 
                'msg' => 'Error: Data siswa dengan id:'.$id.' tidak ditemukan!'
            ]);
            return redirect()->to('admin/siswa');
        }
        return view('student/update', [
            'title'     => 'Tambah Siswa',
            'alert'     => $this->session->alert,
            'student'   => $student,
            'action'    => base_url().'/admin/edit_siswa/'.$id,
        ]);
    }

    public function update($id) //post
    {
        $data = $this->request->getPost();
        $res = true;
        try {
            $res = $this->model->update($id,$data);
        } catch (\ErrorException $e) {
            $res = false;
        }
        if ($res) {
            $this->session->setFlashdata('alert', ['type' => 'success', 'msg' => 'Data Berhasil Disimpan.']);
            return redirect()->to('/admin/siswa');
        } else {
            $this->session->setFlashdata('alert', [
                'type' => 'danger', 
                'msg' => 'Penyimpanan Data Gagal!<br>Silahkan cek data lagi!'
            ]);
            return redirect()->to('/admin/siswa');
        }
    }

    public function delete($id)
    {
        $res = true;
        try {
            $res = $this->model->delete($id);
        } catch (\ErrorException $e) {
            $res = false;
        }
        if ($res) {
            $this->session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Data Berhasil Dihapus.']);
            return redirect()->to('/admin/siswa');
        } else {
            $this->session->setFlashdata('alert', [
                'type' => 'danger', 
                'msg' => 'Data gagal dihapus!<br>Silahkan cek data lagi!'
            ]);
            return redirect()->to('/admin/siswa');
        }
    }
}
