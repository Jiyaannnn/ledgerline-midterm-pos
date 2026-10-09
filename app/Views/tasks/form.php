<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $task !== null; ?>
<section class="inner-hero"><div class="inner-hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> Print shop / schedule / <?= $editing ? 'edit' : 'new' ?></p><h1><?= $editing ? 'Keep the work' : 'Plan' ?> <em><?= $editing ? 'on track.' : 'the next task.' ?></em></h1><p><?= $editing ? 'Update the work, date, or progress for this shop task.' : 'Add work to the print shop task board.' ?></p></div><?= view('components/hero_art', ['kind' => 'tasks']) ?></section>
<section class="form-section"><div class="section-top"><div><p class="eyebrow">The task board</p><h2><?= $editing ? 'Edit task' : 'New task' ?><span class="heading-period">.</span></h2></div><a class="inline-link" href="<?= site_url('tasks') ?>">Back to all tasks ↗</a></div>
<form class="account-form" method="post" action="<?= $editing ? site_url('tasks/' . $task['id']) : site_url('tasks') ?>" novalidate>
<?php // The token protects both create and update requests from cross-site form submissions. ?>
<?= csrf_field() ?>
<div class="form-grid">
<?php // Submitted values take priority over saved values when a validation error is shown. ?>
<div class="field field-wide"><label for="title">Task title <span aria-hidden="true">*</span></label><input id="title" name="title" type="text" maxlength="150" required value="<?= esc($values['title'] ?? '') ?>" aria-invalid="<?= isset($errors['title']) ? 'true' : 'false' ?>" aria-describedby="title-error"><small class="field-error" id="title-error"><?= esc($errors['title'] ?? '') ?></small></div>
<div class="field"><label for="task_date">Scheduled date <span aria-hidden="true">*</span></label><input id="task_date" name="task_date" type="date" required value="<?= esc($values['task_date'] ?? '') ?>" aria-invalid="<?= isset($errors['task_date']) ? 'true' : 'false' ?>" aria-describedby="task-date-error"><small class="field-error" id="task-date-error"><?= esc($errors['task_date'] ?? '') ?></small></div>
<div class="field"><label for="status">Status <span aria-hidden="true">*</span></label><select id="status" name="status" required aria-invalid="<?= isset($errors['status']) ? 'true' : 'false' ?>" aria-describedby="status-error"><?php foreach (['pending' => 'Pending', 'in progress' => 'In progress', 'completed' => 'Completed'] as $value => $label): ?><option value="<?= esc($value) ?>" <?= ($values['status'] ?? 'pending') === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach ?></select><small class="field-error" id="status-error"><?= esc($errors['status'] ?? '') ?></small></div>
</div>
<div class="form-actions"><button class="button button-dark" type="submit"><?= $editing ? 'Save changes' : 'Create task' ?></button><a class="button button-outline" href="<?= site_url('tasks') ?>">Cancel</a></div>
</form>
<?php // Deletion has its own protected POST form and asks for confirmation first. ?>
<?php if ($editing): ?><form class="delete-form" method="post" action="<?= site_url('tasks/' . $task['id'] . '/delete') ?>" onsubmit="return confirm('Archive this task? It will be hidden from public lists.');"><?= csrf_field() ?><button class="delete-button" type="submit">Archive task</button></form><?php endif ?>
</section>
<?= $this->endSection() ?>
