<?php echo $this->extend('login/layout'); ?>

<?= $this->section('content_css'); ?>
<style type="text/css">
  body {
    padding-top: 20px;
  }
</style>
<?= $this->endSection();?>
<?= $this->section('content'); ?>
<div class="row g-1 justify-content-center">
  <div class="card col-lg-12">
    <div class="card-body">
      <h5 class="card-title">Tampilan Presensi</h5>
      <div class="container-fluid">
        <div class="row">
          <div class="col">
            <div class="container-fluid">
              <div class="row mb-3">
                <button type="button" id="refresh_btn" class="btn btn-primary col">Refresh</button>
                <button type="button" id="toggle_btn" class="btn btn-secondary col">Disable Autorefresh</button>
              </div>
              <div class="row mb-3">
                <div class="col btn btn-dark" id="waktu"></div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="container-fluid">
              <div class="row mb-3" style="border: 1px solid;">
                <p><strong>
                  Tanggal: <?=$date?><br>
                  Batas Waktu: <?=$time;?><br>
                  Tipe: <?=$mode;?><br>
                </strong></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- 
        TODO: javascript bar untuk progress autorefresh
        TODO: Info Sesi (Penting)
      -->
      <div class="row mb-3">
        <div class="col-8">
          <div class="row mb3"><h5>Sudah Presensi</h5></div>
          <table id="tbl_presensi" class="table table-striped mb3" style="width:100%">
            <thead>
              <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Waktu</th>
                <th>Keterangan</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="col-4" style="border: 1px solid black;">
          <div class="row mb3"><h5>Belum Presensi</h5></div>
          <div>
            <table id="tbl_belum" class="table table-striped mb3" style="width:100%">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Kelas</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection();?>

<?= $this->section('content_js');?>
<script src="<?=base_url()?>/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
$.fn.dataTable.ext.errMode = 'none';
let autrefresh_state = true;
let tbl_presensi;
let tbl_belum;

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

$(document).ready(async () => {
  tbl_presensi = $('#tbl_presensi').DataTable({
    ajax: '<?=base_url()?>/presensi/get_presensi/<?=$sess_id;?>',
    columns: [
      { data: 'nis' },
      { data: 'name' },
      { data: 'classroom' },
      { data: 'time_short' },
      { data: 'comment' },
    ],
    pageLength: 25,
    order: []
  });
  tbl_presensi.on('error.dt', () => {
    location.reload();
  });

  tbl_belum = $('#tbl_belum').DataTable({
    ajax: '<?=base_url()?>/presensi/not_yet_attend/<?=$sess_id;?>',
    columns: [
      { data: 'name' },
      { data: 'classroom' },
    ],
    pageLength: 25,
  });
  tbl_belum.on('error.dt', () => {
    location.reload();
  });

  document.getElementById('refresh_btn').onclick = async () => {
    Promise.all([
      (async () => tbl_presensi.ajax.reload())(),
      (async () => tbl_belum.ajax.reload())(),
    ]);
  };
  document.getElementById('toggle_btn').onclick = async () => {
    if (autrefresh_state) {
      autrefresh_state = false;
      document.getElementById('waktu').innerHTML = "refresh disabled"
      document.getElementById('toggle_btn').innerHTML = "Enable Autorefresh";
    } else {
      autrefresh_state = true;
      document.getElementById('toggle_btn').innerHTML = "Disable Autorefresh";
    }
  };

  Promise.all([
    (async () => {
      while(true){
        await sleep(1000);
        if (autrefresh_state) {
          Promise.all([
            (async () => tbl_presensi.ajax.reload())(),
            (async () => tbl_belum.ajax.reload())(),
          ]);
          let today = new Date();
          let time = today.getHours() + ":" + String(today.getMinutes()).padStart(2, '0') 
            + ":" + String(today.getSeconds()).padStart(2, '0');
          document.getElementById('waktu').innerHTML = "last refresh : ["+time+"]";
        }
      }
    })(),
    (async () => {
      await sleep(2700000);
      location.reload();
    })(),
  ]);
});
</script>
<?= $this->endSection();?>
