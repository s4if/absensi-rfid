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
        // tempat simpan sementara rfid, ada current dan last
        $this->db->table('rfid_tmp')->insert(['id' => 'CURRENT']);
        $this->db->table('rfid_tmp')->insert(['id' => 'OLDCURRENT']);
    }
}
