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
        $query = $this->db->query("select rfid_tmp.rfid as 'rfid', devices.name as 'device',"
            ." rfid_tmp.updated_at as 'timestamp' from rfid_tmp inner join devices"
            ." on devices.id=rfid_tmp.device_id where rfid_tmp.id='CURRENT';");
        // TODO: dijadikan select rfid_tmp.rifd as rfid, devices.name as name, dll
        $current = $query->getRow();
        $log_date = ( new \DateTimeImmutable())->setTimestamp($current->timestamp);
        $current->time = $log_date->format('l, d M Y H:i:s');
        return $this->respond($current);
    }

    public function readRfid($device_id)
    {
        $token = $this->request->getGet('token');
        $rfid = $this->request->getGet('rfid');
        $device_builder = $this->db->table('devices');
        $device_builder->where('id', $device_id);
        $query = $device_builder->get();
        $device = $query->getRow();

        if (is_null($device)) {
            return $this->failUnauthorized('device unknown');
        }
        if ($device->token != $token) {
            return $this->failValidationError('token invalid');
        }

        $data = [
            'rfid'          => $rfid,
            'updated_at'    => (new \DateTime('now'))->getTimestamp(),
            'device_id'     => $device_id
        ];
        $res = true;
        $mode = 'save'; // save or listen
        if ($mode == 'save') {
            $res = $this->saveRfid($data);
        } else {
            $res = $this->saveAttendance($data);
        }
        return ($res)?$this->respondCreated($data):$this->fail('unknow error', 400);
    }

    private function saveRfid(&$data)
    {
        try {
            $builder = $this->db->table('rfid_tmp');
            $builder->update($data, ['id' => 'CURRENT']);
            $data['mode'] = 'save_rfid';
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setStudentRfid()
    {
        $nis = $this->request->getVar('nis');
        $rfid = $this->request->getVar('rfid');
        $builder = $this->db->table('students');
        try {
            $builder->update(['rfid' => $rfid], ['nis' => $nis]);
            return $this->respondCreated(['rfid' => $rfid]);
        } catch (Exception $e) {
            return $this->fail('unknown failure', 400);
        }

    }

    private function saveAttendance(&$data)
    {
        return true;
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
}
