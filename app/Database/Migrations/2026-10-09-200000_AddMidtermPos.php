<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMidtermPos extends Migration
{
    protected $DBGroup = 'default';

    public function up(): void
    {
        // Archive flags keep old sale references intact when a record leaves the active list.
        $this->forge->addColumn('customers', ['is_archived' => ['type' => 'BOOLEAN', 'default' => false]]);
        $this->forge->addColumn('users', ['is_archived' => ['type' => 'BOOLEAN', 'default' => false]]);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'stock_quantity' => ['type' => 'INT', 'default' => 0],
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_archived' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'customer_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'sold_by' => ['type' => 'INT', 'unsigned' => true],
            'quantity' => ['type' => 'INT'],
            'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('sales');
    }

    public function down(): void
    {
        $this->forge->dropTable('sales', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropColumn('users', 'is_archived');
        $this->forge->dropColumn('customers', 'is_archived');
    }
}
