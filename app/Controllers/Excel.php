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
            ." classroom = :classroom: where nis = :nis: ;";
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
}
