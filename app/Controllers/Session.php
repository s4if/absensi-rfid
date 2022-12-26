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
            $sess->action = "<a type='button' href='".base_url()."/admin/presensi/".$sess->id
            ."' class='btn btn-primary btn-sm'><i class='bi-list-check'></i></a>"
            ."<button type='button' onclick='edit(".$sess->id
            .")' class='btn btn-warning btn-sm'><i class='bi-pencil-fill'></i></button>"
            ."<button type='button' onclick='del(".$sess->id
            .")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></button>";
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

    // presensi
    public function showAttendance($id)
    {
        return view('session/attendance', [
            'title'     => 'Daftar Hadir',
            'alert'     => $this->session->alert,
            'sess_id'   => $id,
        ]);
    }

    public function getAttendaces($id)
    {
        $sess = $this->model->find($id);
        if (is_null($sess)) {
            return $this->failNotFound('data tidak ditemukan');
        }
        $sql = "select students.nis as nis, students.name as name, students.classroom as classroom,"
            ." att_records.logged_at as timestamp, att_records.id as id from att_records inner join"
            ." students on att_records.student_id=students.id where att_records.session_id=?"
            ." order by att_records.logged_at asc;";
        $aquery = $this->db->query($sql, [$sess->id]);
        $data = $aquery->getResultObject();
        foreach ($data as &$item) {
            $time = (new \DateTime());
            $time->setTimestamp($item->timestamp);
            $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            $item->time = $time->format('Y-m-d H:i:s');
            $item->action = "<button type='button' onclick='del(".$item->id
            .")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></button>";
        }
        return $this->respond(['data' => $data]);
    }

    public function getAttRecord($id){
        $sql = "select students.nis as nis, students.name as name, students.classroom as classroom,"
            ." att_records.logged_at as timestamp, att_records.id as id from att_records inner join"
            ." students on att_records.student_id=students.id where att_records.id=?"
            ." order by att_records.logged_at asc;";
        $query = $this->db->query($sql,[$id]);
        $record = $query->getRow();
        $time = (new \DateTime());
        $time->setTimestamp($record->timestamp);
        $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $record->time = $time->format('Y-m-d H:i:s');
        if (is_null($record)) {
            return $this->failNotFound('record not found');
        }
        return $this->respond($record);
    }

    public function deleteAttRecord($id)
    {
        $sql = "delete from att_records where id=?;";
        try {
            $res = $this->db->query($sql,[$id]);
            return ($res)?$this->respondDeleted(['id' => $id]):$this->failNotFound('item tidak ditemukan');
        } catch (\ErrorException $e) {
            return $this->fail('unknown error', 400);
        }
    }

    public function getStudentsOption($id){
        $sql1 = "select student_id as id from att_records where session_id=?;";
        $q1 = $this->db->query($sql1, [$id]);
        $res1 = $q1->getResultObject();
        $exclusion_list = [];
        foreach ($res1 as $ex_id) {
            $exclusion_list[] = $ex_id->id;
        }
        $sql2 = "select * from students;";
        $q2 = $this->db->query($sql2);
        $res2 = $q2->getResultObject();
        $data = [];
        foreach ($res2 as $item) {
            if (!in_array($item->id, $exclusion_list)) {
                $data[] = $item;
            }
        }
        return $this->respond($data);
    }

    public function manualRecord($id)
    {
        $sess = $this->model->find($id);
        if (is_null($sess)) {
            return $this->failNotFound('data tidak ditemukan');
        }
        $data = $this->request->getVar();
        $time = (new \DateTime());
        $time->setTimestamp($sess->criterion_time);
        $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $date = $time->format('Y-m-d');
        $new_time = \DateTime::createFromFormat('Y-m-d H:i', $date." ".$data->time, 
            (new \DateTimeZone('Asia/Jakarta'))); // TODO: default timezone biar lebih enak, gimana ya?
        $sql = "insert into att_records(session_id, student_id, logged_at, 'device_id')"
            ." values(?, ?, ?, 'MANUAL');";
        $res = $this->db->query($sql, [$sess->id, $data->student_id, $new_time->getTimestamp()]);
        return ($res)?$this->respondCreated([]):$this->fail('unknown error', 4000);
    }
}
