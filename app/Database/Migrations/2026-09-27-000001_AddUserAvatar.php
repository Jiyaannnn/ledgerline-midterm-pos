<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserAvatar extends Migration
{
    protected $DBGroup = 'default';

    public function up(): void
    {
        // The database keeps the public filename; image bytes stay on disk.
        $this->forge->addColumn('users', [
            'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'full_name'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'avatar');
    }
}
