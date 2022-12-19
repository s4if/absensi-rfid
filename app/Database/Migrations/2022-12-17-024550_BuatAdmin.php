<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatAdmin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'          => 'VARCHAR',
                'constraint'    => 24,
                'unique'        => true,
            ],
            'name' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => false
            ],
            'password' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => false,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('admins');
    }

    public function down()
    {
        $this->forge->dropTable('admins');
    }
}
