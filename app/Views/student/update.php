<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1><?= $title; ?></h1>
</div>
<div class="row mb-3">
	<form action="<?= $action ?>" method="POST">
		<div class="mb-3">
			<label class="form-label">NIS : </label>
			<input type="number" name="nis" class="form-control" value="<?=$student->nis;?>" >
        </div>
        <div class="mb-3">
			<label class="form-label">Nama : </label>
			<input type="text" name="name" class="form-control" value="<?=$student->name;?>" >
        </div>
        <div class="mb-3">
        	<label class="form-label">P/L:</label>
        	<select class="form-select" name="gender" aria-label="Default select example">
				<option value="L" <?= ($student->gender == 'L')?"selected":"";?> >Laki-Laki</option>
				<option value="P" <?= ($student->gender == 'P')?"selected":"";?> >Perempuan</option>
			</select>
        </div>
        <div class="mb-3">
        	<label class="form-label">Kelas:</label>
        	<select class="form-select" name="classroom" aria-label="Default select example">
				<option value="TJKT1" <?= ($student->classroom == 'TJKT1')?"selected":"";?> >11 TKJ1</option>
				<option value="TJKT2" <?= ($student->classroom == 'TJKT2')?"selected":"";?> >11 TKJ2</option>
			</select> 
        </div>
        <div class="mb-3">
			<input type="submit" value="Simpan" class="btn btn-primary">
        </div>
	</form>
</div>
<?= $this->endSection(); ?>