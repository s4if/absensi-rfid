<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDevice extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 12,
            ],
            'name' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
            ],
            'token' => [
                'type'          => 'VARCHAR',
                'constraint'    => 12
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('devices');
    }

    public function down()
    {
        $this->forge->dropTable('devices');
    }
}
