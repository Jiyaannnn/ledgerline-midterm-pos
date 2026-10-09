<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserPassword extends Migration
{
    protected $DBGroup = 'default';

    public function up(): void
    {
        // Nullable permits migration of existing rows before hashes are assigned.
        $this->forge->addColumn('users', [
            'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'avatar'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'password');
    }
}
