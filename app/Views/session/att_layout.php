<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title?></title>
    <link href="<?=base_url()?>/css/bootstrap.min.css" rel="stylesheet">
    <?php echo $this->renderSection('content_css');?>
  </head>
  <body>
    <div class="container-fluid">
      <?php if (!is_null($alert)) : ?>
        <div class="row justify-content-center">
          <div class="col-lg-6 alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $alert['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      <?php endif; ?>

      <?php echo $this->renderSection('content');?>

<div class="modal modal-lg" id="mdl_guru" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Guru yang Sudah Presensi Hari ini</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <h5>Tanggal: <?=$date?></h5>
        </div>
        <div class="row">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Waktu Absen</th>
                <th>Rfid</th>
              </tr>
            </thead>
            <tbody id="tbl_root"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    </div>
    <script src="<?=base_url()?>/js/jquery-3.6.2.min.js"></script>
    <script src="<?=base_url()?>/js/popper.min.js"></script>
    <script src="<?=base_url()?>/js/bootstrap.bundle.min.js"></script>
    <?php echo $this->renderSection('content_js');?>
  </body>
</html>