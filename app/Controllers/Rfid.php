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
            $current = $query->getRow();
            $log_date = \DateTimeImmutable::createFromFormat('U', $current->timestamp);
            $log_date = $log_date->setTimezone($this->tz);
            $current->time = $log_date->format('l, d M Y H:i:s [e]');
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
        $now = new \DateTimeImmutable('now');
        $rfid_data = [
            'rfid'          => $rfid,
            'updated_at'    => $now->getTimestamp(),
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
        $time = (new \DateTimeImmutable('now'));
        $time = $time->setTimezone($this->tz);
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

    public function showAttendance(){
        $sess = $this->getSession();
        if (is_null($sess)) {
            return view('session/att_notice', [
                'title'     => 'Sesi Tidak Ada',
                'alert'     => $this->session->alert,
            ]);
        } else {
            $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu', 'Ahad'];
            $bulan = ['Januari', 'Frebruari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $time = \DateTimeImmutable::createFromFormat('U', $sess->criterion_time);
            $time = $time->setTimezone($this->tz);
            return view('session/att_show', [
                'title'     => 'Daftar Hadir',
                'alert'     => $this->session->alert,
                'sess_id'   => $sess->id,
                'mode'      => $sess->mode,
                'date'      => $hari[$time->format('N')-1].$time->format(', d ').$bulan[$time->format('n')-1]
                                .$time->format(' Y'),
                'time'      => $time->format('H:i')
            ]);
        }
    }
}
