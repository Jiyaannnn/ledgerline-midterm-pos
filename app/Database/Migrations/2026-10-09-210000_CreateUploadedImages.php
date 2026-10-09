<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUploadedImages extends Migration
{
    protected $DBGroup = 'default';

    public function up(): void
    {
        // Prepared JPEGs live in MySQL/TiDB so Render restarts cannot erase them.
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'content' => ['type' => 'MEDIUMBLOB'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('uploaded_images');
    }

    public function down(): void
    {
        $this->forge->dropTable('uploaded_images', true);
    }
}
