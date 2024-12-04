<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Student extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('students', [
            'classroom' => [ // nama kelas (biar gak konflik dengan class)
                'type'          => 'ENUM',
                'constraint'    => ['TJKT1', 'TJKT2','GURU'], // guru tetep dikasih, whynot? wkwk
                'null'          => false,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('students', [
            'classroom' => [ // nama kelas (biar gak konflik dengan class)
                'type'          => 'ENUM',
                'constraint'    => ['TKJ1', 'TKJ2','GURU'], // rolbek
                'null'          => false,
            ],
        ]);
    }
}
