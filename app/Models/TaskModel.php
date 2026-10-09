<?php
namespace App\Models;
use CodeIgniter\Model;
class TaskModel extends Model {
    // The optional daily task board lives alongside the POS tables.
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['title', 'status', 'task_date', 'created_at', 'is_archived'];
    public function forDate(string $date): array {
        // Filter in SQL so the Today page receives only tasks scheduled for this date.
        return $this->where('is_archived', 0)->where('task_date', $date)->orderBy('id', 'ASC')->findAll();
    }
    public function allByDate(): array {
        return $this->where('is_archived', 0)->orderBy('task_date', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
    public function findForBoard(string $search, string $status, bool $todayOnly): array {
        // Build the same query for direct links and form submissions.
        $query = $this->where('is_archived', 0);
        if ($search !== '') $query->like('title', $search);
        if ($status !== '') $query->where('status', $status);
        if ($todayOnly) $query->where('task_date', date('Y-m-d'));
        return $query->orderBy('task_date', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}
