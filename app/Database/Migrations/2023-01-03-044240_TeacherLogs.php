<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class TeacherLogs extends Migration
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
            'student_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'logged_at' => [
                'type'          => 'TIMESTAMP',
                'default'       => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'device_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 12,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('teachers_logs');
    }

    public function down()
    {
        $this->forge->dropForeignKey('teacer_logs', 'student_id');
        $this->forge->dropTable('teachers_logs');
    }
}
