<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<div class="row text-center">
	<h1>Halaman Beranda</h1>
</div>
<div class="row">
	<a href="<?= base_url()?>/logout" class="btn btn-danger">Logout</a>
</div>
<?= $this->endSection(); ?>