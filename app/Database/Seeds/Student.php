<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Student extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nis'       => '1001',
                'name'      => 'Adi Bagaskara',
                'gender'    => 'L',
                'classroom' => 'TKJ1',
            ],
            [
                'nis'       => '1002',
                'name'      => 'Bagus Cahyono',
                'gender'    => 'L',
                'classroom' => 'TKJ2',
            ],
            [
                'nis'       => '1003',
                'name'      => 'Cecep Darmono',
                'gender'    => 'L',
                'classroom' => 'TKJ1',
            ],
            [
                'nis'       => '1004',
                'name'      => 'Dimas Ekowiyono',
                'gender'    => 'L',
                'classroom' => 'TKJ2',
            ],
            [
                'nis'       => '9001',
                'name'      => 'SUKARI',
                'gender'    => 'L',
                'classroom' => 'GURU',
            ],
            [
                'nis'       => '9002',
                'name'      => 'Surtini',
                'gender'    => 'P',
                'classroom' => 'GURU',
            ],
        ];
        foreach ($data as $row) {
            $this->db->table('students')->insert($row);
        }
    }
}
