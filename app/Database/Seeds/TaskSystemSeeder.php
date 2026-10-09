<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use DateTimeImmutable;
class TaskSystemSeeder extends Seeder {
    public function run(): void {
        $today = new DateTimeImmutable('today');
        $createdAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $entries = [
            ['Check A4 and letter paper stock', 'completed', 0],
            ['Prepare afternoon print pickups', 'in progress', 0],
            ['Test the color printer and trim samples', 'pending', 0],
            ['Reconcile counter sales', 'pending', 0],
            ['Restock pens and notebooks', 'completed', -1],
            ['Confirm tomorrow’s binding orders', 'pending', 1],
            ['Receive new ink cartridges', 'pending', 1],
        ];
        foreach ($entries as [$title, $status, $offset]) {
            $this->db->table('tasks')->insert(['title' => $title, 'status' => $status,
                'task_date' => $today->modify(sprintf('%+d day', $offset))->format('Y-m-d'),
                'created_at' => $createdAt, 'is_archived' => 0]);
        }
    }
}
