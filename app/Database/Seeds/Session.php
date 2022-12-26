<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Session extends Seeder
{
    public function run()
    {
        $datetime = new \DateTime('now');
        $data = [
            'name'              => 'Sesi Coba',
            //'session_date'      => $datetime->format('Y-m-d'),
            'mode'              => 'check-in',
            'criterion_time'    => $datetime->getTimestamp()+7190
        ];
        $this->db->table('sessions')->insert($data);
    }
}
