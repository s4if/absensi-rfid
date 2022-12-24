<?php echo $this->extend('admin/layout'); ?>

<?= $this->section('content_css'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/dataTables.bootstrap5.min.css">
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="row mb-3 text-center">
	<h1>Tabel Siswa</h1>
</div>
<div class="row mb-3">
	<table id="tbl_siswa" class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th>NIS</th>
				<th>Nama</th>
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
<?= $this->endSection(); ?>

<?= $this->section('content_js');?>
<script src="<?=base_url()?>/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
const set_modal = new bootstrap.Modal('#set_modal', {
	'keyboard':true,
});
const b_nis = document.getElementById('b_nis');
const b_nama = document.getElementById('b_nama');
const b_classroom = document.getElementById('b_classroom');
const b_rfid = document.getElementById('b_rfid');
const b_device = document.getElementById('b_device');
const b_time = document.getElementById('b_time');
function set_rfid(id) {
	fetch('<?=base_url();?>/admin/get_siswa/'+id)
	.then((response) => response.json())
  	.then(data => {
    	b_nis.innerHTML = data.nis;
    	b_nama.innerHTML = data.name;
    	b_classroom.innerHTML = data.classroom;
    	b_rfid.innerHTML = "";
    	b_device.innerHTML = "";
    	b_time.innerHTML = "";
    	set_btn.innerHTML = "Hapus RFID"
    	set_btn.classList.add('btn-danger');
    	set_btn.classList.remove('btn-success');
  	})
  	.catch(console.error);

	set_modal.toggle();
}

$(document).ready(function () {
    var table = $('#tbl_siswa').DataTable({
        ajax: '<?=base_url()?>/admin/get_rfid',
        columns: [
            { data: 'nis' },
            { data: 'name' },
            { data: 'rfid' },
            { data: 'action' },
        ],
    });

    let sync_btn = document.getElementById('sync_rfid_btn');
    sync_btn.onclick = () => {
    	fetch('<?=base_url();?>/rfid/get_current/')
		.then((response) => response.json())
	  	.then(data => {
	    	b_rfid.innerHTML = data.rfid;
	    	b_device.innerHTML = data.device;
	    	b_time.innerHTML = data.time;
	    	set_btn.innerHTML = "Simpan RFID";
	    	set_btn.classList.add('btn-success');
	    	set_btn.classList.remove('btn-danger');
	    	// todo, set btn danger menjadi btn-primary
	  	})
	  	.catch(console.error);
    };

    let set_btn = document.getElementById('set_btn');
    set_btn.onclick = () => {
    	let data = {
    		'nis': document.getElementById('b_nis').innerHTML,
    		'rfid': document.getElementById('b_rfid').innerHTML,
    	};

		fetch('<?=base_url();?>/admin/set_rfid', {
			method: 'PUT', // or 'POST'
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(data),
		})
		.then((response) => response.json())
		.then((data) => {
			console.log('Success:', data);
			// TODO: JS Alert?
			table.ajax.reload();
			set_modal.toggle();
		})
		.catch((error) => {
			console.error('Error:', error);
		});
    };
});
</script>
<?= $this->endSection(); ?>

