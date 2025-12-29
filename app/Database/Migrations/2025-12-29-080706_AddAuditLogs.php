<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddAuditLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ip_address' => [
                'type'          => 'VARCHAR',
                'constraint'    => 45,
            ],
            'device_id' => [
                'type'          => 'VARCHAR',
                'constraint'    => 50,
                'null'          => true,
            ],
            'student_id' => [
                'type'          => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'null'          => true,
            ],
            'session_id' => [
                'type'          => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'null'          => true,
            ],
            'action' => [
                'type'          => 'VARCHAR',
                'constraint'    => 50,
            ],
            'status' => [
                'type'          => 'ENUM',
                'constraint'    => ['success', 'failure', 'warning'],
                'default'       => 'success',
            ],
            'message' => [
                'type'          => 'TEXT',
                'null'          => true,
            ],
            'created_at' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['ip_address', 'created_at']);
        $this->forge->addKey(['device_id', 'created_at']);
        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs');
    }
}
