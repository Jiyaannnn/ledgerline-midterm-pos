<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $customer !== null; ?>
<section class="inner-hero"><div class="inner-hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> Print shop / community / <?= $editing ? 'edit' : 'new' ?></p><h1><?= $editing ? 'Keep the details' : 'A new face' ?> <em><?= $editing ? 'current.' : 'at the counter.' ?></em></h1><p><?= $editing ? 'Update this customer’s contact details.' : 'Add a new customer to the print shop directory.' ?></p></div><?= view('components/hero_art', ['kind' => 'people']) ?></section>
<section class="form-section"><div class="section-top"><div><p class="eyebrow">Customer directory</p><h2><?= $editing ? 'Edit customer' : 'New customer' ?><span class="heading-period">.</span></h2></div><a class="inline-link" href="<?= site_url('customers') ?>">Back to customers ↗</a></div>
<form class="account-form" method="post" action="<?= $editing ? site_url('customers/' . $customer['id']) : site_url('customers') ?>" novalidate>
<?= csrf_field() ?>
<div class="form-grid">
<div class="field"><label for="full_name">Full name <span aria-hidden="true">*</span></label><input id="full_name" name="full_name" type="text" maxlength="100" required value="<?= esc($values['full_name'] ?? '') ?>" aria-invalid="<?= isset($errors['full_name']) ? 'true' : 'false' ?>"><small class="field-error"><?= esc($errors['full_name'] ?? '') ?></small></div>
<div class="field"><label for="email">Email address <span aria-hidden="true">*</span></label><input id="email" name="email" type="email" maxlength="100" required value="<?= esc($values['email'] ?? '') ?>" aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"><small class="field-error"><?= esc($errors['email'] ?? '') ?></small></div>
<div class="field"><label for="phone">Phone number <span class="optional">Optional</span></label><input id="phone" name="phone" type="tel" maxlength="20" value="<?= esc($values['phone'] ?? '') ?>" aria-invalid="<?= isset($errors['phone']) ? 'true' : 'false' ?>"><small class="field-error"><?= esc($errors['phone'] ?? '') ?></small></div>
</div>
<div class="form-actions"><button class="button button-dark" type="submit"><?= $editing ? 'Save changes' : 'Create customer' ?></button><a class="button button-outline" href="<?= site_url('customers') ?>">Cancel</a></div>
</form><?php if ($editing): ?><form class="delete-form" method="post" action="<?= site_url('customers/' . $customer['id'] . '/delete') ?>" onsubmit="return confirm('Remove this customer? Past sales will remain.');"><?= csrf_field() ?><button class="delete-button" type="submit">Delete customer</button></form><?php endif ?></section>
<?= $this->endSection() ?>
