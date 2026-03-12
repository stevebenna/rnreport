<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="card" style="max-width: 440px; margin: 4rem auto;">
    <h1 style="margin-bottom: 0.5rem;">Accesso</h1>
    <p style="margin-bottom: 1.5rem; color: var(--muted);">Inserisci le tue credenziali per accedere al pannello.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="/login" method="post" style="display: grid; gap: 1rem;">
        <?= csrf_field() ?>
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="name@example.com" required />
        </div>
        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required />
        </div>
        <button type="submit" class="btn primary">Accedi</button>
    </form>
</div>

<?= $this->endSection() ?>
