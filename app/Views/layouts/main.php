<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ledgerline Print & Supply: inventory, sales, customers, and daily work for a print and stationery shop.">
    <meta name="theme-color" content="#f7f4ec">
    <title><?= esc($title) ?> | Ledgerline Print & Supply</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <script>
    // Apply the saved or system theme before the stylesheet paints the page.
    (() => {
        let saved;
        try { saved = localStorage.getItem('ledgerline-theme'); } catch (_) {}
        document.documentElement.dataset.theme = saved || 'light';
    })();
    </script>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="site-shell">
    <div class="top-note"><span>LEDGERLINE PRINT & SUPPLY</span><span>Print, paper, and everyday supplies.</span></div>
    <header class="site-header">
        <a class="brand" href="<?= site_url('/') ?>" aria-label="Ledgerline Print & Supply home"><img class="brand-logo" src="<?= base_url('assets/images/ledgerline-print-mark.svg') ?>" width="42" height="42" alt=""><span class="brand-name">ledgerline<span> / print & supply</span></span></a>
        <nav class="site-nav" id="site-navigation" aria-label="Main navigation">
            <a class="<?= $activePage === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Today</a>
            <a class="<?= $activePage === 'tasks' ? 'active' : '' ?>" href="<?= site_url('tasks') ?>">Task board</a>
            <?php if (session()->get('staff_id')): ?>
            <a class="<?= $activePage === 'products' ? 'active' : '' ?>" href="<?= site_url('products') ?>">Products</a>
            <a class="<?= $activePage === 'sales' ? 'active' : '' ?>" href="<?= site_url('sales') ?>">Sales</a>
            <a class="<?= $activePage === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>">Customers</a>
            <a class="<?= $activePage === 'users' ? 'active' : '' ?>" href="<?= site_url('users') ?>">Staff</a>
            <?php endif ?>
            <a class="<?= $activePage === 'profile' ? 'active' : '' ?>" href="<?= site_url('profile') ?>">Team</a>
            <a class="<?= $activePage === 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
        </nav>
        <div class="header-controls">
            <?php if (session()->get('staff_id')): ?>
            <form class="header-auth" action="<?= site_url('logout') ?>" method="post"><?= csrf_field() ?><button type="submit">Sign out</button></form>
            <?php else: ?><a class="header-signin" href="<?= site_url('login') ?>">Staff sign in</a><?php endif ?>
            <button class="theme-toggle" type="button" aria-pressed="false" aria-label="Switch to dark mode">
                <svg class="theme-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg class="theme-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.2 16.5A8.5 8.5 0 0 1 7.5 3.8 8.5 8.5 0 1 0 20.2 16.5Z"/></svg>
                <span class="theme-label">Dark mode</span>
            </button>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">Menu <span aria-hidden="true">☰</span></button>
        </div>
    </header>
    <main id="main-content" tabindex="-1">
        <?php if (session()->getFlashdata('success')): ?><p class="notice" role="status"><?= esc(session()->getFlashdata('success')) ?></p><?php endif ?>
        <?php if (session()->getFlashdata('error')): ?><p class="notice error-notice" role="alert"><?= esc(session()->getFlashdata('error')) ?></p><?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>
    <footer class="site-footer"><div><strong>Made for the work behind the counter.</strong><span>Ledgerline Print & Supply is a group midterm project for IT0049.</span></div><span class="footer-team">Jian Edward Acob · Howard Mac Callanta · Gerard Eric Doroja</span></footer>
</div>
<script>
// The menu button exposes the same page links on narrow screens.
const menuButton = document.querySelector('.nav-toggle');
const nav = document.querySelector('.site-nav');
const themeButton = document.querySelector('.theme-toggle');
const themeLabel = document.querySelector('.theme-label');
function updateThemeButton() {
    const dark = document.documentElement.dataset.theme === 'dark';
    themeButton.setAttribute('aria-pressed', String(dark));
    themeButton.setAttribute('aria-label', `Switch to ${dark ? 'light' : 'dark'} mode`);
    themeLabel.textContent = dark ? 'Light mode' : 'Dark mode';
}
updateThemeButton();
themeButton.addEventListener('click', () => {
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    try { localStorage.setItem('ledgerline-theme', next); } catch (_) {}
    updateThemeButton();
});
menuButton?.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') === 'true';
    menuButton.setAttribute('aria-expanded', String(!open));
    nav.classList.toggle('open', !open);
});
// Keep the small-screen menu dismissible without requiring another tap on Menu.
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuButton?.getAttribute('aria-expanded') === 'true') {
        menuButton.setAttribute('aria-expanded', 'false');
        nav.classList.remove('open');
        menuButton.focus();
    }
});
document.addEventListener('click', (event) => {
    if (menuButton?.getAttribute('aria-expanded') !== 'true') return;
    if (menuButton.contains(event.target) || nav.contains(event.target)) return;
    menuButton.setAttribute('aria-expanded', 'false');
    nav.classList.remove('open');
});
// A visible password helps staff catch typing errors; the button never submits the form.
const passwordToggle = document.querySelector('.password-toggle');
passwordToggle?.addEventListener('click', () => {
    const input = document.getElementById('password');
    const visible = input.type === 'password';
    input.type = visible ? 'text' : 'password';
    passwordToggle.textContent = visible ? 'Hide' : 'Show';
    passwordToggle.setAttribute('aria-pressed', String(visible));
    passwordToggle.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
});
// Reveal editorial sections once; task data and forms stay still for quick reading.
if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealTargets = [...document.querySelectorAll('[data-reveal]')];
    const observer = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (!entry.isIntersecting) continue;
            entry.target.classList.add('reveal-visible');
            observer.unobserve(entry.target);
        }
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    for (const element of revealTargets) {
        if (element.getBoundingClientRect().top < window.innerHeight * 0.9) continue;
        element.classList.add('reveal-pending');
        observer.observe(element);
    }
}

</script>
</body>
</html>
