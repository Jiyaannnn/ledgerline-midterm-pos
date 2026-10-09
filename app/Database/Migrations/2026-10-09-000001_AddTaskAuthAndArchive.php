<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class AddTaskAuthAndArchive extends Migration {
    protected $DBGroup = 'default';
    public function up(): void {
        // Archived task records remain in the same database as the POS.
        $this->forge->addColumn('tasks', ['is_archived' => ['type' => 'BOOLEAN', 'default' => false]]);
    }
    public function down(): void {
        $this->forge->dropColumn('tasks', 'is_archived');
    }
}
