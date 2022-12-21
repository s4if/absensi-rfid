<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Student extends Migration
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
            'nis' => [
                'type'          => 'VARCHAR',
                'constraint'    => 24,
                'unique'        => true,
            ],
            'name' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => false,
            ],
            'rfid' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => true,
            ],
            'gender' => [
                'type'          => 'ENUM',
                'constraint'    => ['L', 'P'],
                'null'          => false
            ],
            'classroom' => [ // nama kelas (biar gak konflik dengan class)
                'type'          => 'ENUM',
                'constraint'    => ['TKJ1', 'TKJ2'],
                'null'          => false,
            ],
            'created_at' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'          => 'TIMESTAMP',
                'null'          => true,
            ],
            'deleted_at' => [
                'type'          => 'TIMESTAMP',
                'null'          => true,
            ],

        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('students');
    }

    public function down()
    {
        $this->forge->dropTable('students');
    }
}
