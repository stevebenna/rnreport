<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php $isEdit = ! empty($song['id']); ?>

<div class="grid" style="grid-template-columns: 1fr auto; align-items: center; gap: 1rem;">
    <div>
        <h1><?= $isEdit ? 'Modifica canzone' : 'Nuova canzone' ?></h1>
        <p>Compila i campi per salvare la canzone su Supabase.</p>
    </div>
    <div>
        <a href="/canzoni" class="btn secondary">← Torna a tutte le canzoni</a>
    </div>
</div>

<?php if (! empty($errors)) : ?>
    <div class="alert error">
        <ul style="margin:0; padding-left:1.25rem;">
            <?php foreach ($errors as $field => $message) : ?>
                <li><?= esc($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $isEdit ? '/canzoni/update/'.$song['id'] : '/canzoni/store' ?>" method="post">
    <?= csrf_field() ?>

    <div class="card">
        <div class="form-row">
            <div>
                <label for="artist">Artista *</label>
                <input id="artist" name="artist" type="text" required value="<?= esc(old('artist', $song['artist'] ?? '')) ?>" />
            </div>

            <div>
                <label for="song">Canzone *</label>
                <input id="song" name="song" type="text" required value="<?= esc(old('song', $song['song'] ?? '')) ?>" />
            </div>

            <div>
                <label for="author">Interprete *</label>
                <input id="author" name="author" type="text" required value="<?= esc(old('author', $song['author'] ?? '')) ?>" />
            </div>

            <div>
                <label for="label">Editore *</label>
                <input id="label" name="label" type="text" required value="<?= esc(old('label', $song['label'] ?? '')) ?>" />
            </div>

            <div>
                <label for="duration">Durata (secondi) *</label>
                <input id="duration" name="duration" type="number" min="0" required value="<?= esc(old('duration', $song['duration'] ?? '')) ?>" />
            </div>

            <div>
                <label for="level">Categoria (1-5)</label>
                <input id="level" name="level" type="number" min="1" max="5" value="<?= esc(old('level', $song['level'] ?? '')) ?>" />
                <p class="field-note">Lascia vuoto se non vuoi assegnare una categoria.</p>
            </div>

            <div>
                <label for="container">Contenitore</label>
                <select id="container" name="container">
                    <option value="">-- Seleziona --</option>
                    <?php foreach ($containers as $containerOption) : ?>
                        <option value="<?= esc($containerOption) ?>" <?= old('container', $song['container'] ?? '') === $containerOption ? 'selected' : '' ?>><?= esc($containerOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; align-items:center; gap:0.5rem;">
                <input id="is_easy_listening" name="is_easy_listening" type="checkbox" value="1" <?= old('is_easy_listening', $song['is_easy_listening'] ?? '') ? 'checked' : '' ?> />
                <label for="is_easy_listening" style="margin:0;">Easy Listening</label>
            </div>

            <div>
                <label for="iswc">ISWC</label>
                <input id="iswc" name="iswc" type="text" value="<?= esc(old('iswc', $song['iswc'] ?? '')) ?>" />
            </div>

            <div>
                <label for="isrc">ISRC</label>
                <input id="isrc" name="isrc" type="text" value="<?= esc(old('isrc', $song['isrc'] ?? '')) ?>" />
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary"><?= $isEdit ? 'Salva modifiche' : 'Crea canzone' ?></button>
            <a href="/canzoni" class="btn secondary">Annulla</a>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
