<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="grid" style="grid-template-columns: 1fr auto; align-items: center; gap: 1rem;">
    <div>
        <h1>Dettagli canzone</h1>
        <p>Visualizza tutte le informazioni collegate a questa canzone.</p>
    </div>
    <div>
        <a href="/canzoni" class="btn secondary">← Torna alle canzoni</a>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
            <h2><?= esc($song['artist'] ?? '') ?> — <?= esc($song['song'] ?? '') ?></h2>
            <p><strong>Interprete:</strong> <?= esc($song['author'] ?? '') ?></p>
            <p><strong>Editore:</strong> <?= esc($song['label'] ?? '') ?></p>
            <p><strong>Durata:</strong> <?= esc($song['duration'] ?? '') ?> sec</p>
            <p><strong>Categoria:</strong> <?= esc($song['level'] ?? 'N/A') ?></p>
        </div>
        <div>
            <p><strong>Contenitore:</strong> <?= esc($song['container'] ?? '—') ?></p>
            <p><strong>ISWC:</strong> <?= esc($song['iswc'] ?? '—') ?></p>
            <p><strong>ISRC:</strong> <?= esc($song['isrc'] ?? '—') ?></p>
            <p><strong>Easy Listening:</strong> <?= (! empty($song['is_easy_listening']) ? 'Sì' : 'No') ?></p>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 1.5rem;">
        <a href="/canzoni/edit/<?= urlencode($song['id']) ?>" class="btn primary">Modifica</a>
        <form action="/canzoni/delete/<?= urlencode($song['id']) ?>" method="post" style="display:inline-block; margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="btn danger" onclick="return confirm('Sei sicuro di voler eliminare questa canzone?')">Elimina</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
