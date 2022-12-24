<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class RfidCurrent extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 12,
                'unsigned'       => true,
            ],
            'rfid' => [
                'type'          => 'VARCHAR',
                'constraint'    => 60,
                'null'          => true,
            ],
            'device_id' => [
                'type'          => 'VARCHAR',
                'constraint'    => 12,
                'null'          => true,
            ],
            'updated_at' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('rfid_tmp'); // RFID terakhir yang masuk
    }

    public function down()
    {
        $this->forge->dropTable('rfid_tmp');
    }
}
