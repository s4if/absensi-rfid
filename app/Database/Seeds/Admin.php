<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Admin extends Seeder
{
    public function run()
    {
        $data = [
            'username'  => 'admin',
            'name'      => 'Administrator',
            'password'  => password_hash("qwerty", PASSWORD_BCRYPT)
        ];
        $this->db->table('admins')->insert($data);
    }
}
