<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeviceRfid extends Seeder
{
    public function run()
    {
        $data = [
            'id'    => 'W001',
            'name'  => 'Web Dummy',
            'token' => 'RAVEN'
        ];
        $this->db->table('devices')->insert($data);
        // last_rfid
        $this->db->table('last_rfid')->insert(['id' => 'DEFAULT']);
    }
}
