<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="auth-page" aria-labelledby="sign-in-title">
    <div class="auth-intro"><p class="eyebrow">STAFF ACCESS</p><h1 id="sign-in-title">Welcome back.</h1><p>Sign in to manage tasks and shop records.</p></div>
    <form class="auth-card" action="<?= site_url('login') ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($notice): ?><p class="auth-notice" role="status"><?= esc($notice) ?></p><?php endif ?>
        <?php if ($error): ?><p class="auth-error" role="alert"><?= esc($error) ?></p><?php endif ?>
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= esc($username) ?>" autocomplete="username" required maxlength="50">
        <label for="password">Password</label>
        <div class="password-field"><input id="password" name="password" type="password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-controls="password" aria-pressed="false" aria-label="Show password">Show</button></div>
        <button class="button button-primary" type="submit">Sign in</button>
        <p class="auth-help">Use the staff credentials configured during setup.</p>
    </form>
</section>
<?= $this->endSection() ?>
