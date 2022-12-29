<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeviceRfid extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'    => 'W001',
                'name'  => 'Web Dummy',
                'token' => 'RAVEN'
            ],
            [
                'id'    => 'D001',
                'name'  => 'NodeMCU dev device',
                'token' => 'RABBIT'
            ],
            [
                'id'    => 'D002',
                'name'  => 'Lolin dari adjie',
                'token' => 'RACOON'
            ],
            [
                'id'    => 'D003',
                'name'  => 'ESP32 perangkat baru',
                'token' => 'LYCAN'
            ],
        ];
        foreach ($data as $item) {
            $this->db->table('devices')->insert($item);
        }
        // tempat simpan sementara rfid, hanya current
        $this->db->table('rfid_tmp')->insert(['id' => 'CURRENT']);
    }
}
