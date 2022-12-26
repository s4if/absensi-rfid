<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Rfid extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function getCurrent()
    {
        try {
            $query = $this->db->query("select rfid_tmp.rfid as 'rfid', devices.name as 'device',"
                ." rfid_tmp.updated_at as 'timestamp' from rfid_tmp inner join devices"
                ." on devices.id=rfid_tmp.device_id where rfid_tmp.id='CURRENT';");
            // TODO: dijadikan select rfid_tmp.rifd as rfid, devices.name as name, dll
            $current = $query->getRow();
            $log_date = ( new \DateTimeImmutable())->setTimestamp($current->timestamp);
            $current->time = $log_date->format('l, d M Y H:i:s');
            return $this->respond($current);
        } catch (\ErrorException $e) {
            return $this->failNotFound('rfid belum ada yang masuk');
        }
            
    }

    public function readRfid($device_id)
    {
        // karena perangkatnya sederhana, api-nya yang harus menebak!
        $token = $this->request->getGet('token');
        $rfid = $this->request->getGet('rfid');
        $device_builder = $this->db->table('devices');
        $device_builder->where('id', $device_id);
        $dquery = $device_builder->get();
        $device = $dquery->getRow();

        if (is_null($device)) {
            return $this->failUnauthorized('device unknown');
        }
        if ($device->token != $token) {
            return $this->failUnauthorized('token invalid');
        }
        if (is_null($rfid)) {
            return $this->failValidationError('rfid is null');
        }

        // save rfid
        $rfid_data = [
            'rfid'          => $rfid,
            'updated_at'    => (new \DateTime('now'))->getTimestamp(),
            'device_id'     => $device_id
        ];
        $rfid_builder = $this->db->table('rfid_tmp');
        $rfid_builder->update($rfid_data, ['id' => 'CURRENT']); // Current jangan diubah

        // check student
        $student_builder = $this->db->table('students');
        $student_builder->select('id, nis'); // nis untuk dimasukkan ke log
        $student_builder->where('rfid', $rfid);
        $squery = $student_builder->get();
        $student = $squery->getRow();
        if (is_null($student)) {
            return $this->respondCreated(['msg' => 'rfid saved, not associated with any student']);
        }

        // check session
        $sess = $this->getSession();
        if (is_null($sess)) {
            return $this->respondCreated(['msg' => 'rfid saved, no session detected']);
        }

        // check prev attendance
        $sql = "select * from att_records where session_id=? and student_id=?;";
        $query = $this->db->query($sql, [$sess->id, $student->id]);
        $existing_record = $query->getRow();
        if (!is_null($existing_record)) {
            return $this->respondCreated(['msg' => 'rfid saved, already used in this session']);
        }
        $time = (new \DateTime('now'));
        $time->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $record_data = [
            'student_id'    => $student->id,
            'device_id'     => $device_id,
            'session_id'    => $sess->id,
            'logged_at'     => $time->getTimestamp(),
        ];
        $att_builder = $this->db->table('att_records');
        try {
            $att_builder->insert($record_data);
            return $this->respondCreated(['msg' => 'attendance saved']);
        } catch (\ErrorException $e) {
            return $this->fail('unknown error',400);
        }

        // TODO: append to log (not urgent)
    }

    public function setStudentRfid($mode = 'edit')
    {
        $nis = $this->request->getVar('nis');
        $rfid = null;
        if ($mode == 'edit') {
            $rfid = $this->request->getVar('rfid');
        }
        $builder = $this->db->table('students');
        try {
            $builder->update(['rfid' => $rfid], ['nis' => $nis]);
            return $this->respondCreated(['rfid' => $rfid]);
        } catch (\Exception $e) {
            return $this->fail('unknown failure', 400);
        } catch (\ErrorException $e) {
            return false;
        }
    }

    public function showStudents()
    {
        return view('student/rfid', [
            'title'     => 'List RFID Siswa',
            'alert'     => $this->session->alert,
        ]);
        // todo: copy view-nya...
    }

    public function getStudents()
    {
        $student_builder = $this->db->table('students');
        $student_builder->select('id, name, nis, rfid');
        $student_builder->where('deleted_at', null);
        $query = $student_builder->get();
        $students = $query->getResult();
        foreach ($students as &$student) {
            $student->action = "<button type='button' onclick='set_rfid(".$student->id
            .")' class='btn btn-primary btn-sm'><i class='bi-person-fill-gear'></i></button>";
        }
        unset($student);// wajib
        return $this->respond(['data' => $students]);
    }

    private function getSession()
    {
        $range = 7200; // dibuat di setting env?
        $current_timestamp = time();
        $upper_limit = $current_timestamp + $range;
        $lower_limit = $current_timestamp - $range;
        $sql = "select * from sessions where criterion_time >= ? and criterion_time <= ?"
            ." and deleted_at is null order by criterion_time asc";
        $query = $this->db->query($sql, [$lower_limit, $upper_limit]);
        return $query->getRow();
    }
}
