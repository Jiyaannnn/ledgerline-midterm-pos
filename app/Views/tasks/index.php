<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="inner-hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> Print shop / complete schedule</p><h1>A clear view of <em>every task.</em></h1><p>Print jobs, stock checks, and counter work in one dated list.</p></div><?= view('components/hero_art', ['kind' => 'tasks']) ?></section>
<section class="listing-section" id="task-board-section"><div class="section-top"><div><p class="eyebrow">The task board</p><h2><?= $totalTasks ?> scheduled tasks<span class="heading-period">.</span></h2></div><?php if (session()->get('staff_id')): ?><a class="button button-dark" href="<?= site_url('tasks/new') ?>">+ New task</a><?php else: ?><a class="button button-outline" href="<?= site_url('login') ?>">Sign in to manage tasks</a><?php endif ?></div>
<div class="task-tools" aria-label="Filter tasks">
    <form class="task-search" method="get" action="<?= site_url('tasks') ?>">
        <label for="task-query">Search tasks</label>
        <div class="task-search-row"><input id="task-query" name="q" type="search" maxlength="80" placeholder="Try paper or pickup…" value="<?= esc($search, 'attr') ?>" autocomplete="off"><button type="submit">Search <span aria-hidden="true">↗</span></button></div>
        <?php if ($statusFilter !== ''): ?><input type="hidden" name="status" value="<?= esc($statusFilter, 'attr') ?>"><?php endif ?>
        <?php if ($todayOnly): ?><input type="hidden" name="date" value="today"><?php endif ?>
    </form>
    <div class="task-filters"><div class="filter-label">Show by status</div><div class="filter-chips" role="group" aria-label="Task status">
        <?php foreach (['' => 'All tasks', 'pending' => 'Pending', 'in progress' => 'In progress', 'completed' => 'Completed'] as $value => $label): ?>
        <?php $params = array_filter(['q' => $search, 'status' => $value, 'date' => $todayOnly ? 'today' : '']); ?>
        <a class="filter-chip <?= $statusFilter === $value ? 'is-active' : '' ?>" href="<?= esc(site_url('tasks') . ($params ? '?' . http_build_query($params) : ''), 'attr') ?>" <?= $statusFilter === $value ? 'aria-current="true"' : '' ?>><?= esc($label) ?></a>
        <?php endforeach ?>
    </div></div>
    <?php $dateParams = array_filter(['q' => $search, 'status' => $statusFilter, 'date' => $todayOnly ? '' : 'today']); ?>
    <a class="date-filter <?= $todayOnly ? 'is-active' : '' ?>" href="<?= esc(site_url('tasks') . ($dateParams ? '?' . http_build_query($dateParams) : ''), 'attr') ?>" <?= $todayOnly ? 'aria-current="true"' : '' ?>><?= $todayOnly ? '✓ Today only' : 'Today only' ?></a>
</div>
<div class="task-results" role="status"><span><?= count($tasks) ?> <?= count($tasks) === 1 ? 'result' : 'results' ?><?= ($search !== '' || $statusFilter !== '' || $todayOnly) ? ' for this view' : '' ?></span><?php if ($search !== '' || $statusFilter !== '' || $todayOnly): ?><a href="<?= site_url('tasks') ?>">Clear filters</a><?php endif ?></div>
<?php // Escape database values before displaying task titles, dates, and statuses. ?>
<?php if ($tasks === []): ?><div class="empty-state"><strong><?= $totalTasks === 0 ? 'No tasks yet.' : 'No tasks match these filters.' ?></strong><p><?php if ($totalTasks === 0): ?>The shop task board is ready for its first entry.<?php else: ?>Try a different search or <a href="<?= site_url('tasks') ?>">clear the filters</a>.<?php endif ?></p></div><?php else: ?><div class="task-list"><?php foreach ($tasks as $index => $task): ?><article class="task-item"><span class="task-number"><?= esc(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span><div class="task-copy"><h3><?= esc($task['title']) ?></h3><span><?= esc(date('l, F j, Y', strtotime($task['task_date']))) ?></span></div><span class="status status-<?= esc(str_replace(' ', '-', $task['status'])) ?>"><?= esc(ucwords($task['status'])) ?></span><?php if (session()->get('staff_id')): ?><a class="inline-link task-edit" href="<?= site_url('tasks/' . $task['id'] . '/edit') ?>" aria-label="Edit <?= esc($task['title'], 'attr') ?>">Edit ↗</a><?php endif ?></article><?php endforeach ?></div><?php endif ?></section>
<script src="<?= base_url('assets/js/task-board.js') ?>" defer></script>
<?= $this->endSection() ?>
