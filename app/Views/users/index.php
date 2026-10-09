<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="inner-hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> Print shop / staff</p><h1>People behind <em>the counter.</em></h1><p>Staff accounts can be updated with current names and profile pictures.</p></div><?= view('components/hero_art', ['kind' => 'people']) ?></section>
<section class="listing-section"><div class="section-top"><div><p class="eyebrow">Staff directory</p><h2><?= count($users) ?> staff records<span class="heading-period">.</span></h2></div><a class="button button-dark" href="<?= site_url('users/new') ?>">+ New user</a></div>
<div class="table-frame"><table><thead><tr><th>Staff member</th><th>Username</th><th>Added</th><th>Action</th></tr></thead><tbody>
<?php foreach ($users as $user): ?><tr>
<?php $avatarAlt = empty($user['avatar']) ? 'Default profile picture' : (str_starts_with($user['avatar'], 'assets/images/avatars/') ? 'Illustrated avatar for ' : 'Profile picture of ') . $user['full_name']; ?>
<td data-label="Staff member"><span class="person-cell"><img class="staff-avatar" src="<?= ! empty($user['avatar']) ? base_url($user['avatar']) : base_url('assets/img/avatar-placeholder.svg') ?>" alt="<?= esc($avatarAlt, 'attr') ?>"><strong><?= esc($user['full_name']) ?></strong></span></td>
<td data-label="Username">@<?= esc($user['username']) ?></td>
<td data-label="Added"><?= esc(date('M j, Y', strtotime($user['created_at']))) ?></td>
<td data-label="Action"><a class="inline-link" href="<?= site_url('users/' . $user['id'] . '/edit') ?>">Edit ↗</a></td>
</tr><?php endforeach ?></tbody></table></div></section>
<?= $this->endSection() ?>
