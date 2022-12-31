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
        $time = \DateTimeImmutable::createFromFormat('U', $sess->criterion_time);
        $time = $time->setTimezone($this->tz);
        $sess->date = $time->format('Y-m-d');
        $sess->time = $time->format('H:i');
        return $this->respond($sess);
    }

    public function list()
    {
        $sessions = $this->model->orderBy('criterion_time', 'ASC')->findAll(); // array objek sesi
        $count = 1;
        foreach ($sessions as &$sess) {
            $time = \DateTimeImmutable::createFromFormat('U', $sess->criterion_time);
            $time = $time->setTimezone($this->tz);
            $sess->date = $time->format('l, j M Y');
            $sess->time = $time->format('H:i');
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
        $datetime = \DateTimeImmutable::createFromFormat('Y-m-d H:i', 
            $data->date.' '.$data->time, $this->tz);
        $data->criterion_time = $datetime->getTimestamp();
        $slot = $this->getSlot($data->date,$data->mode, true);
        // do simpan
        try {
            if (is_null($slot)) {
                $res = $this->model->insert($data, false);
                return $this->respondCreated($data);
            } 
            if (is_null($slot->deleted_at)) {
                return $this->failResourceExists("slot sudah dipakai, dan masih aktif");
            }
            $data->deleted_at = null;
            $res = $this->model->update($slot->id,$data);
            return $this->respondCreated($data);
        } catch (\ErrorException $e) {
            return $this->failValidationError('simpan error, cek nama!');
        }
    }

    private function getSlot($str_date, $mode, $include_deleted = false) // format: Y-m-d
    {
        // check slot
        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $str_date
            .' 00:00:00', $this->tz );
        $s_timestamp = $date->getTimestamp();
        $e_timestamp = $s_timestamp  + 86399;
        $res = false;
        $sqlc = "select * from sessions where criterion_time >= ? and criterion_time <= ? and"
                    ." mode = ?";
        $suffix = ($include_deleted)?";":" and deleted_at is null;";
        // undelete sessions rawan bikin sesi tidak valid
        $c_query = $this->db->query($sqlc, [$s_timestamp, $e_timestamp, $mode]);
        return $c_query->getRow();
    }

    public function update($id)
    {
        $data = $this->request->getVar();
        $datetime = \DateTime::createFromFormat('Y-m-d H:i', 
            $data->date.' '.$data->time, $this->tz);
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
            $time = \DateTimeImmutable::createFromFormat('U', $item->timestamp);
            $time = $time->setTimezone($this->tz);
            $item->time = $time->format('Y-m-d H:i:s');
            $item->action = "<button type='button' onclick='del(".$item->id
            .")' class='btn btn-danger btn-sm'><i class='bi-x-circle-fill'></i></button>";
            $item->time_short = $time->format('H:i');
            $item->comment = "";
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
        $time = \DateTimeImmutable::createFromFormat('U', $record->timestamp);
        $time = $time->setTimezone($this->tz);
        $record->time = $time->format('Y-m-d H:i:s');
        $record->time_short = $time->format('H:i');
        if (is_null($record)) {
            return $this->failNotFound('record not found');
        }
        return $this->respond($record);
    }

    public function getNotAttend($id){
        $sql = 'SELECT students.name as name, students.classroom as classroom from students where classroom <> "GURU" and'
        .' not EXISTS (SELECT 1 from att_records where att_records.session_id=? and att_records.student_id=students.id);';
        $aquery = $this->db->query($sql,[$id]);
        $records = $aquery->getResultObject();
        return $this->respond(['data' => $records]);
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
        $time = \DateTimeImmutable::createFromFormat('U', $sess->criterion_time);
        $time = $time->setTimezone($this->tz);
        $date = $time->format('Y-m-d');
        $new_time = \DateTime::createFromFormat('Y-m-d H:i', $date." ".$data->time, $this->tz);
        $sql = "insert into att_records(session_id, student_id, logged_at, 'device_id')"
            ." values(?, ?, ?, 'MANUAL');";
        $res = $this->db->query($sql, [$sess->id, $data->student_id, $new_time->getTimestamp()]);
        return ($res)?$this->respondCreated([]):$this->fail('unknown error', 4000);
    }
}
