<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Siswa</h1>
</div>
<div class="row mb-3">
	<button type="button" id="add_btn" class="btn btn-success col-md-3">
		<i class="bi-person-fill-add"></i>&nbsp;&nbsp;Tambah Siswa
	</button>
	<button type="button" id="import_btn" class="btn btn-secondary col-md-3">
		<i class="bi-upload"></i>&nbsp;&nbsp;Import Siswa
	</button>
</div>
<div class="row mb-3">
	<table id="tbl_siswa" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>NIS</th>
				<th>Nama</th>
				<th>P/L</th>
				<th>Kelas</th>
				<th>Rfid</th>
				<th>Aksi</th>
			</tr>
		</thead>
	</table>
</div>
<?= $this->endSection(); ?>

<?= $this->section('content_modal'); ?>
<!-- Modal -->
<div class="modal fade" id="set_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 class="modal-title fs-5">Set RFID</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
			    NIS = <b id="b_nis"></b><br>
			    Nama = <b id="b_nama"></b><br>
			    Kelas = <b id="b_classroom"></b><br>
			    <hr>
			    RFID Terakhir = <b id="b_rfid"></b><br>
			    Perangkat = <b id="b_device"></b><br>
			    Waktu Terbaca = <b id="b_time"></b><br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button id="sync_rfid_btn" class="btn btn-primary">Ambil RFID Terakhir</button>
				<button id="set_btn" class="btn">Hapus RFID</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="del_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 class="modal-title fs-5">Hapus Data</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
			    Apakah Anda Yakin untuk menghapus siswa dengan<br>
			    NIS: <b id="del_nis"></b><br>
			    Nama: <b id="del_name"></b><br>
			    Kelas <b id="del_classroom"></b>
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
						<label class="form-label">NIS : </label>
						<input type="number" id="edit_nis" name="nis" class="form-control">
			        </div>
			        <div class="mb-3">
						<label class="form-label">Nama : </label>
						<input type="text" id="edit_name" name="name" class="form-control">
			        </div>
			        <div class="mb-3">
			        	<label class="form-label">P/L:</label>
			        	<select class="form-select" id="edit_gender" name="gender">
							<option value="L">Laki-Laki</option>
							<option value="P">Perempuan</option>
						</select>
			        </div>
			        <div class="mb-3">
			        	<label class="form-label">Kelas:</label>
			        	<select class="form-select" id="edit_classroom" name="classroom">
							<option value="TJKT1">12 TJKT1</option>
							<option value="TJKT2">12 TJKT2</option>
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

<div class="modal fade" id="upload_modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  	<div class="modal-header">
			    <h1 class="modal-title fs-5">Import Data</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
		  		<form>
					<div class="mb-3">
						<label class="form-label">Template : </label>
						<input type="file" id="upload_file" required name="file" accept=".xls" class="form-control">
			        </div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" id="upload_submit" class="btn btn-success">Upload</button>
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
let tbl_siswa;
//set rfid
let set_modal = new bootstrap.Modal('#set_modal', {
	'keyboard':true,
});
let b_nis = document.getElementById('b_nis');
let b_nama = document.getElementById('b_nama');
let b_classroom = document.getElementById('b_classroom');
let b_rfid = document.getElementById('b_rfid');
let b_device = document.getElementById('b_device');
let b_time = document.getElementById('b_time');

// tambah siswa
let edit_modal = new bootstrap.Modal('#edit_modal', {
	'keyboard':true,
});
let edit_nis = document.getElementById('edit_nis');
let edit_name = document.getElementById('edit_name');
let edit_gender = document.getElementById('edit_gender');
let edit_classroom = document.getElementById('edit_classroom');
let edit_submit = document.getElementById('edit_submit');

// upload template
let upload_modal = new bootstrap.Modal('#upload_modal', {
	'keyboard':true,
});

async function set_rfid(id) {
	let response = await fetch('<?=base_url();?>/admin/get_siswa/'+id);
	if (response.ok) {
		data = await response.json();
    	b_nis.innerHTML = data.nis;
    	b_nama.innerHTML = data.name;
    	b_classroom.innerHTML = data.classroom;
    	b_rfid.innerHTML = "";
    	b_device.innerHTML = "";
    	b_time.innerHTML = "";
    	set_btn.innerHTML = "Hapus RFID"
    	set_btn.classList.add('btn-danger');
    	set_btn.classList.remove('btn-success');
		set_modal.toggle();
  	} else {
  		alert('unkown error')
  	}
}

async function del(id) {
	let del_modal = new bootstrap.Modal('#del_modal', {
		'keyboard':true,
	});
	let response = await fetch('<?=base_url();?>/admin/get_siswa/'+id);
	if (response.ok) {
		data = await response.json();
		let del_btn = document.getElementById('del_btn');
		del_btn.href = '<?=base_url();?>/admin/hapus_siswa/'+id; // todo: dibuat ajax
		let del_nis = document.getElementById('del_nis');
		del_nis.innerHTML = data.nis;
		let del_name = document.getElementById('del_name');
		del_name.innerHTML = data.name;
		let del_classroom = document.getElementById('del_classroom');
		del_classroom.innerHTML = data.classroom;
		del_modal.toggle();
	}
}

async function edit(id) {
	let response = await fetch('<?=base_url();?>/admin/get_siswa/'+id);
	if (response.ok) {
		let data = await response.json();
    	edit_nis.value = data.nis;
    	edit_name.value = data.name;
    	edit_classroom.value = data.classroom;
    	edit_gender.value = data.gender;
		edit_modal.toggle();
		edit_submit.onclick = async () => {
			let data = {
				'nis' : edit_nis.value,
				'name' : edit_name.value,
				'classroom' : edit_classroom.value,
				'gender' : edit_gender.value,
			};
			let res2 = await fetch('<?=base_url();?>/admin/edit_siswa/'+id, {
			    method: 'PUT', // *GET, POST, PUT, DELETE, etc.
			    headers: {
			      'Content-Type': 'application/json'
			    },
			    redirect: 'follow', // manual, *follow, error
			    body: JSON.stringify(data) // body data type must match "Content-Type" header
			});
			if (res2.ok) {
				alert('Simpan Berhasil!');
				tbl_siswa.ajax.reload();
				edit_modal.toggle();
			} else {
				alert('Simpan Gagal!');
			}
		};
  	} else {
  		alert('unkown error')
  	}
}

$(document).ready(function () {
    tbl_siswa = $('#tbl_siswa').DataTable({
        ajax: '<?=base_url()?>/admin/get_siswa',
        columns: [
            { data: 'nis' },
            { data: 'name' },
            { data: 'gender' },
            { data: 'classroom' },
            { data: 'rfid' },
            { data: 'action' },
        ],
    });

    let add_btn = document.getElementById('add_btn');
    add_btn.onclick = async () => {
		edit_nis.value = "";
		edit_name.value = "";
		edit_classroom.value = "";
		edit_gender.value = "";
		edit_modal.toggle();
		edit_submit.onclick = async () => {
			let data = {
				'nis' : edit_nis.value,
				'name' : edit_name.value,
				'classroom' : edit_classroom.value,
				'gender' : edit_gender.value,
			};
			let response = await fetch('<?=base_url();?>/admin/tambah_siswa', {
			    method: 'POST', // *GET, POST, PUT, DELETE, etc.
			    headers: {
			      'Content-Type': 'application/json'
			    },
			    redirect: 'follow', // manual, *follow, error
			    body: JSON.stringify(data) // body data type must match "Content-Type" header
			});
			if (response.ok) {
				alert('Simpan Berhasil!');
				tbl_siswa.ajax.reload();
				edit_modal.toggle();
			} else {
				alert('Simpan Gagal!');
			}
		};
    };

    let import_btn = document.getElementById('import_btn');
    import_btn.onclick = async () => {
		upload_modal.toggle();
    }
    let upload_submit = document.getElementById('upload_submit');
    upload_submit.onclick = async () => {
    	let fd = new FormData();
    	let upload_file = document.getElementById('upload_file');
    	let current_file = upload_file.files[0];
		fd.append('template', current_file);
		let response = await fetch('<?=base_url()?>/admin/import_siswa', {
			method: "POST", 
			body: fd,
		});
		if (response.ok) {
			alert('upload berhasil');
			upload_modal.toggle();
			tbl_siswa.ajax.reload();
		} else {
			alert('unkown error');
		}
    };

    let sync_btn = document.getElementById('sync_rfid_btn');
    sync_btn.onclick = async () => {
	    let response = await fetch('<?=base_url();?>/rfid/get_current/');
		if (response.ok) {
			let data = await response.json();
			b_rfid.innerHTML = data.rfid;
			b_device.innerHTML = data.device;
			b_time.innerHTML = data.time;
			set_btn.innerHTML = "Simpan RFID";
			set_btn.classList.add('btn-success');
			set_btn.classList.remove('btn-danger');
		} else {
			alert('belum ada data rfid yang masuk');
		}
    };

    let set_btn = document.getElementById('set_btn');
    set_btn.onclick = async () => {
    	let data = {
    		'nis': b_nis.innerHTML,
    		'rfid': b_rfid.innerHTML,
    	};

    	let put_url;
    	let method;
    	let notice;
    	if (set_btn.innerHTML == "Simpan RFID") {
    		put_url = '<?=base_url();?>/admin/set_rfid';
    		method = 'PUT';
    		notice = 'Data Berhasil Disimpan';
    	} else {
    		put_url = '<?=base_url();?>/admin/set_rfid';
    		method = 'DELETE';
    		notice = 'Data Berhasil Dihapus';
    	}

		let response = await fetch(put_url, {
			method: method,
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(data),
		});
		if (response.ok) {
			alert(notice);
			tbl_siswa.ajax.reload();
			set_modal.toggle();
		} else {
			alert("Data Gagal Disimpan!\nMungkin terjadi duplikasi?\nMohon RFID Terakhir di Cek Kembali!");
			set_modal.toggle();
		}
    };
});
</script>
<?= $this->endSection(); ?>

