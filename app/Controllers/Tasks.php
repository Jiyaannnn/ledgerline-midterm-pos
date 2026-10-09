<?php

namespace App\Controllers;

use App\Models\TaskModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Tasks extends BaseController
{
    public function index(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $search = mb_substr($search, 0, 80);
        $status = (string) $this->request->getGet('status');
        if (! in_array($status, ['pending', 'in progress', 'completed'], true)) $status = '';
        $todayOnly = $this->request->getGet('date') === 'today';
        $model = new TaskModel();
        return view('tasks/index', [
            'title' => 'Task board', 'activePage' => 'tasks',
            'tasks' => $model->findForBoard($search, $status, $todayOnly),
            'totalTasks' => count($model->allByDate()),
            'search' => $search, 'statusFilter' => $status, 'todayOnly' => $todayOnly,
        ]);
    }

    public function new(): string
    {
        // A new task starts on today's date with a pending status.
        return $this->taskForm(null, [], ['task_date' => date('Y-m-d'), 'status' => 'pending']);
    }

    public function create()
    {
        return $this->saveTask(null);
    }

    public function edit(int $id): string
    {
        $task = (new TaskModel())->find($id);
        if ($task === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->taskForm($task, [], $task);
    }

    public function update(int $id)
    {
        return $this->saveTask($id);
    }

    public function delete(int $id)
    {
        $model = new TaskModel();
        // Check the record first so an unknown ID returns a 404 instead of a success message.
        if ($model->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        // Keep the database record for history while removing it from public task lists.
        $model->update($id, ['is_archived' => 1]);
        return redirect()->to(site_url('tasks'))->with('success', 'Task archived.');
    }

    private function saveTask(?int $id)
    {
        // The same save path handles creation and editing; an ID means this is an edit.
        $model = new TaskModel();
        $task = $id === null ? null : $model->find($id);
        if ($id !== null && $task === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $values = [
            'title' => trim((string) $this->request->getPost('title')),
            'task_date' => trim((string) $this->request->getPost('task_date')),
            'status' => trim((string) $this->request->getPost('status')),
        ];
        // Check the date and allowed status before changing the task database.
        $rules = [
            'title' => 'required|max_length[150]',
            'task_date' => 'required|valid_date[Y-m-d]',
            'status' => 'required|in_list[pending,in progress,completed]',
        ];
        if (! $this->validateData($values, $rules)) {
            // Redisplay the submitted values so the user can correct only the invalid fields.
            return $this->taskForm($task, $this->validator->getErrors(), $values);
        }
        // created_at is set once; editing keeps the original creation timestamp.
        if ($id === null) {
            $values['created_at'] = date('Y-m-d H:i:s');
            $model->insert($values);
        } else {
            $model->update($id, $values);
        }
        return redirect()->to(site_url('tasks'))->with('success', $id === null ? 'Task created.' : 'Task updated.');
    }

    private function taskForm(?array $task, array $errors, array $values): string
    {
        return view('tasks/form', [
            'title' => $task === null ? 'New Task' : 'Edit Task',
            'activePage' => 'tasks',
            'task' => $task,
            'errors' => $errors,
            'values' => $values,
        ]);
    }
}
