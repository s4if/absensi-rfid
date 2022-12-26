<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Presensi</h1>
</div>
<div class="row mb-3">
	<button type="button" id="add_btn" class="btn btn-success col-md-3">
		<i class="bi-database-fill-add"></i>&nbsp;&nbsp;Absen Manual
	</button>
</div>
<div class="row mb-3">
	<table id="tbl_presensi" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>NIS</th>
				<th>Nama</th>
				<th>Kelas</th>
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
			    Nis = <b id="del_nis"></b><br>
			    Nama = <b id="del_name"></b><br>
			    Kelas = <b id="del_classroom"></b><br>
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
			    <h1 class="modal-title fs-5">Presensi Manual</h1>
			    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  	</div>
		  	<div class="modal-body">
		  		<form>
		  			<div class="mb-3">
						<label class="form-label">Siswa</label>
						<select class="form-select" id="edit_student" name="student">
						</select>						
					</div>
					<div class="mb-3">
						<label class="form-label">Waktu</label>
						<input type="time" id="edit_time" name="time" class="form-control">
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
let tbl_presensi;

// del element
let del_modal = new bootstrap.Modal('#del_modal', {
	'keyboard':true,
});
let del_btn = document.getElementById('del_btn');
let del_name = document.getElementById('del_name');
let del_nis = document.getElementById('del_nis');
let del_classroom = document.getElementById('del_classroom');
let del_time = document.getElementById('del_time');

// edit elemet
let edit_modal = new bootstrap.Modal('#edit_modal', {
	'keyboard':true,
});
let edit_time = document.getElementById('edit_time');
let edit_student = document.getElementById('edit_student');
let edit_submit = document.getElementById('edit_submit');

async function del(id) {
	let response = await fetch('<?=base_url();?>/admin/get_item_presensi/'+id);
	if (response.ok) {
		let data = await response.json();
		del_btn.onclick = async () => {
			let res2 = await fetch('<?=base_url();?>/admin/hapus_item_presensi/'+id, {
			    method: 'DELETE',
			});
			if (res2.ok) {
				alert('Data Berhasil Dihapus!');
				tbl_presensi.ajax.reload();
				del_modal.toggle();
			} else {
				alert('Data Gagal Dihapus!');
			}
		};
		del_nis.innerHTML = data.nis;
		del_name.innerHTML = data.name;
		del_classroom.innerHTML = data.classroom;
		del_time.innerHTML = data.time;
		del_modal.toggle();
	} else {
		alert('data dengan id:'+id+" tidak ditemukan");
	}
}
$(document).ready(function () {
    tbl_presensi = $('#tbl_presensi').DataTable({
        ajax: '<?=base_url()?>/admin/get_presensi/<?=$sess_id;?>',
        columns: [
            { data: 'nis' },
            { data: 'name' },
            { data: 'classroom' },
            { data: 'time' },
            { data: 'action' },
        ],
    });
    document.getElementById('add_btn').onclick = async () => {
    	edit_student.innerHTML = "";
    	let response = await fetch('<?=base_url();?>/admin/get_siswa_belum_presensi/<?=$sess_id;?>');
    	if (response.ok) {
    		let data = await response.json();
    		data.forEach((item) => {
    			console.log(item);
    			let opt = document.createElement('option');
    			opt.value = item.id;
    			opt.innerHTML = item.name+' ('+item.classroom+')';
    			edit_student.append(opt);
    		});
    	}
    	edit_submit.onclick = async () => {
	    	let data = {
	    		'student_id': edit_student.value,
	    		'time': edit_time.value
	    	};
	    	console.log(data);
	    	let response = await fetch('<?=base_url();?>/admin/presensi_manual/<?=$sess_id;?>', {
			    method: 'POST',
			    headers: {
			      'Content-Type': 'application/json'
			    },
			    body: JSON.stringify(data) // body data type must match "Content-Type" header
			});
			if (response.ok) {
				alert('Simpan Berhasil!');
				tbl_presensi.ajax.reload();
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

