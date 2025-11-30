<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
    <div class="card">
        <div class="card-body">
            <h1>Halo, Dunia!</h1>
            <p>Ini adalah tampilan yang menggunakan Bootstrap 5 dan struktur layout CI4.</p>
            <button class="btn btn-success">Tombol Bootstrap</button>
        </div>
    </div>
<?= $this->endSection(); ?>