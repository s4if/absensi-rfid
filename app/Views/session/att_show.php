<?php echo $this->extend('login/layout'); ?>

<?= $this->section('content_css'); ?>
<style type="text/css">
  body {
    padding-top: 80px;
  }
</style>
<?= $this->endSection();?>
<?= $this->section('content'); ?>
<div class="row g-1 justify-content-center">
  <div class="card col-lg-12">
    <div class="card-body">
      <h5 class="card-title">Tampilan Presensi</h5>
      <div class="row mb-3">
        <button type="button" id="refresh_btn" class="btn btn-primary col-3">Refresh</button>
        <div class="col-3">[tombol toggle autorefresh disini...]</div>
        <div class="col-3">[animasi loading autorefres disini...]</div>
      </div>
      <div class="row mb-3">
        [keterangan sesi disini...]
      </div>
      <!-- 
        TODO: javascript bar untuk progress autorefresh
        TODO: Info Sesi (Penting)
      -->
      <div class="row mb-3">
        <table id="tbl_presensi" class="table table-striped" style="width:100%">
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
    </div>
  </div>
</div>
<?= $this->endSection();?>

<?= $this->section('content_js');?>
<script src="<?=base_url()?>/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
let tbl_presensi;

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
      { data: 'time' },
      { data: 'comment' },
    ],
  });
  document.getElementById('refresh_btn').onclick = async () => {
    tbl_presensi.ajax.reload();
  };
  while(true){
    await sleep(1000);
    tbl_presensi.ajax.reload();
  }
});
</script>
<?= $this->endSection();?>
