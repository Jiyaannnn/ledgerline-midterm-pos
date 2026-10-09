<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="inner-hero"><div class="inner-hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> Print shop / community</p><h1>Customers shape <em>the work.</em></h1><p>Keep the details behind every print relationship accurate and ready to update.</p></div><?= view('components/hero_art', ['kind' => 'people']) ?></section>
<section class="listing-section"><div class="section-top"><div><p class="eyebrow">Customer directory</p><h2><?= count($customers) ?> customer records<span class="heading-period">.</span></h2></div><a class="button button-dark" href="<?= site_url('customers/new') ?>">+ New customer</a></div>
<div class="table-frame"><table><thead><tr><th>Customer</th><th>Email</th><th>Phone</th><th>Added</th><th>Action</th></tr></thead><tbody>
<?php foreach ($customers as $customer): ?><tr>
<td data-label="Customer"><span class="person-cell"><span class="person-monogram" aria-hidden="true"><?= esc(strtoupper(substr($customer['full_name'], 0, 1))) ?></span><strong><?= esc($customer['full_name']) ?></strong></span></td>
<td data-label="Email"><a href="mailto:<?= esc($customer['email'], 'attr') ?>"><?= esc($customer['email']) ?></a></td>
<td data-label="Phone"><?= esc($customer['phone']) ?></td>
<td data-label="Added"><?= esc(date('M j, Y', strtotime($customer['created_at']))) ?></td>
<td data-label="Action"><a class="inline-link" href="<?= site_url('customers/' . $customer['id'] . '/edit') ?>">Edit ↗</a></td>
</tr><?php endforeach ?></tbody></table></div></section>
<?= $this->endSection() ?>
