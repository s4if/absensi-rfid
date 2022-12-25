<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Session extends Migration
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
            'name' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => false,
            ],
            'session_date' => [
                'type'          => 'DATE',
                'null'          => false
            ],
            'mode' => [
                'type'          => 'ENUM',
                'constraint'    => ['check-in', 'check-out'], // kedatangan dan kepulangan
                'null'          => false,
            ],
            'criterion_time' => [ // patokan waktu, misal, sesi kedatangan: jam 8.00 kepulangan jam 16.30
                'type'          => 'TIMESTAMP',
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
        $this->forge->createTable('sessions');
    }

    public function down()
    {
        $this->forge->dropTable('sessions');
    }
}
