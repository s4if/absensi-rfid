<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Sesi</h1>
</div>
<div class="row mb-3">
	<button type="button" id="add_btn" class="btn btn-success col-md-3">
		<i class="bi-database-fill-add"></i>&nbsp;&nbsp;Tambah Sesi <!-- TODO: Dijadikan generate saja? -->
	</button>
</div>
<div class="row mb-3">
	<table id="tbl_siswa" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>No.</th>
				<th>Nama</th>
				<th>Tanggal</th>
				<th>Mode</th>
				<th>Waktu</th>
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
			    <h1 class="modal-title fs-5">Konfirmasi Hapus</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
			    Apakah Anda Yakin untuk menghapus sesi dengan detail:<br>
			    Nama Sesi = <b id="del_name"></b><br>
			    Tanggal = <b id="del_date"></b><br>
			    Mode = <b id="del_mode"></b><br>
			    Waktu = <b id="del_time"></b><br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" id="del_btn" class="btn btn-danger">Hapus</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 id="em_title" class="modal-title fs-5"></h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
		  		<form>
					<div class="mb-3">
						<label class="form-label">Nama</label>
						<input type="text" id="edit_name" name="name" class="form-control">
					</div>
					<div class="mb-3">
						<label class="form-label">Tanggal</label>
						<input type="date" id="edit_date" name="date" class="form-control">
					</div>
					<div class="mb-3">
						<label class="form-label">Waktu</label>
						<input type="time" id="edit_time" name="time" class="form-control">
					</div>
					<div class="mb-3">
						<label class="form-label">Mode</label>
						<select class="form-select" id="edit_mode" name="mode">
							<option value="check-in">Check In</option>
							<option value="check-out">Check Out</option>
						</select>						
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
let tbl_sesi;

// del element
let del_modal = new bootstrap.Modal('#del_modal', {
	'keyboard':true,
});
let del_btn = document.getElementById('del_btn');
let del_name = document.getElementById('del_name');
let del_date = document.getElementById('del_date');
let del_mode = document.getElementById('del_mode');
let del_time = document.getElementById('del_time');

// edit elemet
let edit_modal = new bootstrap.Modal('#edit_modal', {
	'keyboard':true,
});
let em_title = document.getElementById('em_title');
let edit_name = document.getElementById('edit_name');
let edit_date = document.getElementById('edit_date');
let edit_time = document.getElementById('edit_time');
let edit_mode = document.getElementById('edit_mode');
let edit_submit = document.getElementById('edit_submit');

async function edit(id) {
    em_title.innerHTML = 'Edit Sesi';
    let response = await fetch('<?=base_url();?>/admin/get_sesi/'+id);
    if (response.ok) {
    	let data = await response.json();
    	edit_name.value = data.name;
    	edit_date.value = data.date;
    	edit_time.value = data.time;
    	edit_mode.value = data.mode;
    }
    edit_submit.onclick = async () => {
    	let data = {
	    	'name' : edit_name.value,
	    	'date' : edit_date.value,
	    	'time' : edit_time.value,
	    	'mode' : edit_mode.value,
    	};
    	console.log(data);
    	let response = await fetch('<?=base_url();?>/admin/edit_sesi/'+id, {
		    method: 'PUT', // *GET, POST, PUT, DELETE, etc.
		    headers: {
		      'Content-Type': 'application/json'
		    },
		    redirect: 'follow', // manual, *follow, error
		    body: JSON.stringify(data) // body data type must match "Content-Type" header
		});
		if (response.ok) {
			alert('Simpan Berhasil!');
			tbl_sesi.ajax.reload();
			edit_modal.toggle();
		} else {
			alert('Simpan Gagal!');
		}
    };
	edit_modal.toggle();
}

async function del(id) {
	let response = await fetch('<?=base_url();?>/admin/get_sesi/'+id);
	if (response.ok) {
		let data = await response.json();
		del_btn.onclick = async () => {
			let res2 = await fetch('<?=base_url();?>/admin/hapus_sesi/'+id, {
			    method: 'DELETE',
			});
			if (res2.ok) {
				alert('Data Berhasil Dihapus!');
				tbl_sesi.ajax.reload();
				del_modal.toggle();
			} else {
				alert('Data Gagal Dihapus!');
			}
		};
		del_name.innerHTML = data.name;
		del_mode.innerHTML = data.mode;
		del_time.innerHTML = data.time;
		del_date.innerHTML = data.date;
		del_modal.toggle();
	} else {
		alert('data dengan id:'+id+" tidak ditemukan");
	}
}
$(document).ready(function () {
    tbl_sesi = $('#tbl_siswa').DataTable({
        ajax: '<?=base_url()?>/admin/get_sesi',
        columns: [
            { data: 'counter' },
            { data: 'name' },
            { data: 'date' },
            { data: 'mode' },
            { data: 'time' },
            { data: 'action' },
        ],
    });
    document.getElementById('add_btn').onclick = async () => {
    	em_title.innerHTML = 'Tambah Sesi';
    	edit_name.value = "";
    	edit_date.value = "";
    	edit_time.value = "";
    	edit_mode.value = "";
    	edit_submit.onclick = async () => {
	    	let data = {
		    	'name' : edit_name.value,
		    	'date' : edit_date.value,
		    	'time' : edit_time.value,
		    	'mode' : edit_mode.value,
	    	};
	    	console.log(data);
	    	let response = await fetch('<?=base_url();?>/admin/tambah_sesi', {
			    method: 'POST', // *GET, POST, PUT, DELETE, etc.
			    headers: {
			      'Content-Type': 'application/json'
			    },
			    redirect: 'follow', // manual, *follow, error
			    body: JSON.stringify(data) // body data type must match "Content-Type" header
			});
			if (response.ok) {
				alert('Simpan Berhasil!');
				tbl_sesi.ajax.reload();
    			edit_modal.toggle();
			} else {
				alert('Simpan Gagal!');
			}
    	};
    	edit_modal.toggle();
    };
});
</script>
<?= $this->endSection(); ?>

