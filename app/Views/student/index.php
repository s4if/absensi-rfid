<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Siswa</h1>
</div>
<div class="row mb-3">
	<a href="<?= base_url()?>/admin/tambah_siswa" class="btn btn-success col-md-3">
		<i class="bi-person-fill-add"></i>&nbsp;&nbsp;Tambah Siswa
	</a>
</div>
<div class="row mb-3">
	<table id="tbl_siswa" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>NIS</th>
				<th>Nama</th>
				<th>P/L</th>
				<th>Kelas</th>
				<th>Aksi</th>
			</tr>
		</thead>
	</table>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content_modal'); ?>
<!-- Modal -->
<div class="modal fade" id="del_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 class="modal-title fs-5">Modal title</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
			    Apakah Anda Yakin untuk menghapus siswa dengan NIS:<b id="del_nis"></b> <!-- TODO: nis dan nama -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<a href="#" id="del_btn" class="btn btn-danger">Hapus</a>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content_js');?>
<script src="<?=base_url()?>/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
function del(id,nis) {
	let del_modal = new bootstrap.Modal('#del_modal', {
		'keyboar':true,
	});
	let del_btn = document.getElementById('del_btn');
	del_btn.href = '<?=base_url();?>/admin/hapus_siswa/'+id;
	let del_nis = document.getElementById('del_nis');
	del_nis.innerHTML = nis;
	del_modal.toggle();
}
$(document).ready(function () {
    $('#tbl_siswa').DataTable({
        ajax: '<?=base_url()?>/admin/get_siswa',
        columns: [
            { data: 'nis' },
            { data: 'name' },
            { data: 'gender' },
            { data: 'classroom' },
            { data: 'action' },
        ],
    });
});
</script>
<?= $this->endSection(); ?>

