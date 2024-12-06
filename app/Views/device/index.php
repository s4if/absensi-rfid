<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Perangkat</h1>
</div>
<div class="row mb-3">
	<button type="button" id="add_btn" class="btn btn-success col-md-3">
		<i class="bi-person-fill-add"></i>&nbsp;&nbsp;Tambah Perangkat
	</button>
</div>
<div class="row mb-3">
	<table id="tbl_device" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nama</th>
				<th>Token</th>
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
			    <h1 class="modal-title fs-5">Hapus Data</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
			    Apakah Anda Yakin untuk menghapus ID dengan<br>
			    ID: <b id="del_id"></b><br>
			    Nama: <b id="del_name"></b><br>
			    Token: <b id="del_token"></b><br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<a href="#" id="del_btn" class="btn btn-danger">Hapus</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 class="modal-title fs-5">Edit/Tambah Data</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
		  		<form>
					<div class="mb-3">
						<label class="form-label">ID : </label>
						<input type="number" id="edit_id" name="nis" class="form-control">
			        </div>
			        <div class="mb-3">
						<label class="form-label">Nama : </label>
						<input type="text" id="edit_name" name="name" class="form-control">
			        </div>
			        <div class="mb-3">
						<label class="form-label">Token : </label>
						<input type="text" id="edit_token" name="name" class="form-control">
			        </div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" id="edit_submit" class="btn btn-success">Simpan</button>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content_js');?>
<script src="<?=base_url()?>/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
// tabel siswa
let tbl_device;

// tambah siswa
let edit_modal = new bootstrap.Modal('#edit_modal', {
	'keyboard':true,
});
let edit_id = document.getElementById('edit_id');
let edit_name = document.getElementById('edit_name');
let edit_token = document.getElementById('edit_token');
let edit_submit = document.getElementById('edit_submit');


async function del(id) {
	let del_modal = new bootstrap.Modal('#del_modal', {
		'keyboard':true,
	});
	let response = await fetch('<?=base_url();?>/admin/get_device/'+id);
	if (response.ok) {
		data = await response.json();
		let del_btn = document.getElementById('del_btn');
		del_btn.href = '<?=base_url();?>/admin/hapus_device/'+id; // todo: dibuat ajax
		let del_id = document.getElementById('del_id');
		del_id.innerHTML = data.id;
		let del_name = document.getElementById('del_name');
		del_name.innerHTML = data.name;
		let del_token = document.getElementById('del_token');
		del_token.innerHTML = data.token;
		del_modal.toggle();
	}
}

async function edit(id) {
	let response = await fetch('<?=base_url();?>/admin/get_device/'+id);
	if (response.ok) {
		// TODO: lengkapi disini
  	} else {
  		alert('unkown error')
  	}
}

$(document).ready(function () {
    tbl_siswa = $('#tbl_device').DataTable({
        ajax: '<?=base_url()?>/admin/get_device',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'token' },
            { data: 'action' },
        ],
    });
});
</script>
<?= $this->endSection(); ?>

