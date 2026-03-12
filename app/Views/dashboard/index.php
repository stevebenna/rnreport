<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="grid" style="grid-template-columns: 1fr auto; align-items: center; gap: 1rem;">
    <div>
        <h1>Dashboard</h1>
        <p>Benvenuto nel pannello di controllo. Da qui puoi gestire le tue canzoni e controllare lo stato dell'account.</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1.25rem;">
    <div class="card">
        <h2>Info utente</h2>
        <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
        <p><strong>ID:</strong> <span style="word-break: break-all;"><?= esc($user['id']) ?></span></p>
        <p><strong>Provider:</strong> <?= esc($user['app_metadata']['provider'] ?? 'email') ?></p>
        <p><strong>Iscritto il:</strong> <?= esc(date('d M Y', strtotime($user['created_at']))) ?></p>
    </div>

    <div class="card">
        <h2>Azioni rapide</h2>
        <p>Vai subito a gestire il catalogo delle canzoni.</p>
        <a href="/canzoni" class="btn primary">Vai a Canzoni</a>
    </div>
</div>

<?= $this->endSection() ?>
