<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTaskSystem extends Migration {
    protected $DBGroup = 'default';
    public function up(): void {
        // The columns match the activity's required SQL schema.
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 150],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'task_date' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tasks');
    }
    public function down(): void {
        $this->forge->dropTable('tasks', true);
    }
}
