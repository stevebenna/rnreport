<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="grid" style="grid-template-columns: 1fr auto; align-items: center; gap: 1rem;">
    <div>
        <h1>Report SCF/ItsRight</h1>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert error"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card" style="margin-top: 1.5rem;">
    <form action="/report-scf/process" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-row">
            <div>
                <label for="scf_zip">File ZIP</label>
                <input id="scf_zip" name="scf_zip" type="file" accept=".zip" required />
                <p class="field-note">Carica un file ZIP contenente uno o più file CSV
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">Elabora e scarica</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
