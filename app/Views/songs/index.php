<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="grid" style="grid-template-columns: 1fr auto; align-items: center; gap: 1rem;">
    <div>
        <h1>Canzoni</h1>
        <p>Gestisci il catalogo delle canzoni collegate a Supabase.</p>
    </div>
    <div>
        <a href="/canzoni/create" class="btn primary">+ Nuova canzone</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert error"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card" style="margin-top: 1.5rem;">
    <form method="get" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center; margin-bottom:1rem;">
        <div style="flex:1; min-width:220px;">
            <label for="q">Cerca artista o titolo</label>
            <input id="q" name="q" type="text" value="<?= esc($search ?? '') ?>" placeholder="Inserisci testo..." />
        </div>
        <button type="submit" class="btn secondary" style="margin-top:1.75rem;">Filtra</button>
    </form>

    <?php if (empty($songs)) : ?>
        <p>Ancora nessuna canzone. Clicca "Nuova canzone" per iniziare.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Artista</th>
                    <th>Canzone</th>
                    <th>Interprete</th>
                    <th>Editore</th>
                    <th>Durata</th>
                    <th>Livello</th>
                    <th>Contenitore</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $song) : ?>
                    <tr>
                        <td><?= esc($song['artist'] ?? '') ?></td>
                        <td>
                            <a href="/canzoni/show/<?= urlencode($song['id']) ?>" style="color: var(--brand-dark); font-weight:700;">
                                <?= esc($song['song'] ?? '') ?>
                            </a>
                        </td>
                        <td><?= esc($song['author'] ?? '') ?></td>
                        <td><?= esc($song['label'] ?? '') ?></td>
                        <td><?= esc($song['duration'] ?? '') ?></td>
                        <td>
                            <?php if (! empty($song['level'])): ?>
                                <span class="badge"><?= esc($song['level']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($song['container'] ?? '') ?></td>
                        <td style="white-space: nowrap;">
                            <a href="/canzoni/show/<?= urlencode($song['id']) ?>" class="btn secondary">Dettagli</a>
                            <a href="/canzoni/edit/<?= urlencode($song['id']) ?>" class="btn secondary">Modifica</a>
                            <form action="/canzoni/delete/<?= urlencode($song['id']) ?>" method="post" style="display:inline-block; margin:0;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn danger" onclick="return confirm('Sei sicuro di voler eliminare questa canzone?')">Elimina</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
            $totalPages = $count ? (int) ceil($count / $perPage) : 1;
            $currentPage = max(1, (int) ($page ?? 1));
        ?>

        <?php if ($totalPages > 1) : ?>
            <div style="margin-top:1.25rem; display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                <span style="font-size:0.9rem; color: var(--muted);">Pagina <?= $currentPage ?> di <?= $totalPages ?> (<?= $count ?> risultati)</span>
                <?php for ($p = 1; $p <= $totalPages; $p++) : ?>
                    <?php
                        $query = ['page' => $p];
                        if (! empty($search)) {
                            $query['q'] = $search;
                        }
                        $url = '/canzoni?' . http_build_query($query);
                    ?>
                    <a href="<?= esc($url) ?>" class="btn <?= $p === $currentPage ? 'primary' : 'secondary' ?>" style="padding:0.4rem 0.7rem; font-size:0.85rem;"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
