<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function importSiswa()
    {
        $template = $this->request->getFile('template');
        if(is_null($template)){
            return $this->failNotFound('file kosong');
        }
        if (!$template->isValid()) {
            return $this->failValidationError('file tidak valid');
        }
        $file_ext = $template->guessExtension();
        if ($file_ext != 'xls') {
            return $this->failValidationError('file bukan excel valid');
        }
        $reader = IOFactory::CreateReader(ucfirst($file_ext));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($template->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $data_count = $worksheet->getCell('D2')->getValue();
        if (!is_numeric($data_count)) { // cek jika gagal!
            return $this->failValidationError('file template tidak valid');
        }
        $h_row = $data_count+4;
        $sql_insert = "insert into students (nis, name, gender, classroom)"
            ." VALUES(:nis:, :name:, :gender:, :classroom:);";
        $sql_update = "update students set name = :name:, gender = :gender:,"
            ." classroom = :classroom:, deleted_at = null where nis = :nis: ;";
        $this->db->transStart();
        $debug_data = [];
        for ($row = 5; $row <= $h_row; $row++) {
            $nis = $worksheet->getCell('B'.$row)->getValue();
            if (!is_numeric($nis)) { // cek jika gagal!
                return $this->failValidationError('file template tidak valid');
            }
            $data = [
                'nis'       => $nis,
                'name'      => $worksheet->getCell('C'.$row)->getValue(),
                'gender'    => $worksheet->getCell('D'.$row)->getValue(),
                'classroom' => $worksheet->getCell('E'.$row)->getValue(),
            ];
            $sql_check = "select id, nis from students where nis = ? ;";
            $c_query = $this->db->query($sql_check, [$nis]);
            $c_res = $c_query->getRow();
            $sql = (is_null($c_res))?$sql_insert:$sql_update;
            $debug_data[] = $data;
            $this->db->query($sql, $data);
        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->failValidationError('data ada yang salah');
        }
        //return $this->respondCreated(['msg' => 'file valid, no error']);
        return $this->respond(['debug_data' => $debug_data], 200);
    }

    public function importSesi() {
        $template = $this->request->getFile('template');
        if(is_null($template)){
            return $this->failNotFound('file kosong');
        }
        if (!$template->isValid()) {
            return $this->failValidationError('file tidak valid');
        }
        $file_ext = $template->guessExtension();
        if ($file_ext != 'xls') {
            return $this->failValidationError('file bukan excel valid');
        }
        $reader = IOFactory::CreateReader(ucfirst($file_ext));
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($template->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $data_count = $worksheet->getCell('C2')->getValue();
        if (!is_numeric($data_count)) { // cek jika gagal!
            return $this->failValidationError('file template tidak valid');
        }
        $h_row = $data_count+4;
        $worksheet->getStyle("B5:B".$h_row)->getNumberFormat()->setFormatCode("YYYY-MM-DD");
        $worksheet->getStyle("C5:D".$h_row)->getNumberFormat()->setFormatCode("hh:mm");
        $sql_insert = "insert into sessions (name, mode, criterion_time)"
            ." VALUES(:name:, :mode:, :criterion_time:);";
        $sql_update = "update sessions set name = :name:, mode = :mode:,"
            ." criterion_time = :criterion_time:, deleted_at = null where id = :id: ;";
        $this->db->transStart();
        $debug_data = [];
        for ($row = 5; $row <= $h_row; $row++) {
            $date = $worksheet->getCell('B'.$row)->getFormattedValue();
            $ci_time = $worksheet->getCell('C'.$row)->getFormattedValue();
            $co_time = $worksheet->getCell('D'.$row)->getFormattedValue();
            // simpan slot check-in
            $slot_ci = $this->getSlot($date, 'check-in', true);
            $sql_ci = (is_null($slot_ci))?$sql_insert:$sql_update;
            $ci_date = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $date.' '.$ci_time, $this->tz);
            $data_ci = [
                'criterion_time'    => $ci_date->getTimestamp(),
                'mode'              => 'check-in',
                'name'              => $ci_date->format('y_m_d').'_check-in'
            ];
            if (!is_null($slot_ci)) {
                $data_ci['id'] = $slot_ci->id;
            }
            $this->db->query($sql_ci, $data_ci);

            // simpan slot check-out
            $slot_co = $this->getSlot($date, 'check-out', true);
            $sql_co = (is_null($slot_co))?$sql_insert:$sql_update;
            $co_date = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $date.' '.$co_time, $this->tz);
            $data_co = [
                'criterion_time'    => $co_date->getTimestamp(),
                'mode'              => 'check-out',
                'name'              => $co_date->format('y_m_d').'_check-out'
            ];
            if (!is_null($slot_co)) {
                $data_co['id'] = $slot_co->id;
            }
            $this->db->query($sql_co, $data_co);
            /*$debug_data[] = [
                'date' => $date,
                'ci_time' => $ci_time,
                'co_time' => $co_time,
                'exist' => (!is_null($this->getSlot($date, 'check-in', false)))?'ada':'tidak',
            ];*/

        }
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->failValidationError('data ada yang salah');
        }
        //return $this->respondCreated(['msg' => 'file valid, no error']);
        // beluum jadiii..
        return $this->respond(['debug_data' => $debug_data], 200);
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
}
