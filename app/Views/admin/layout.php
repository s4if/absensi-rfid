<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title?></title>
    <link href="<?=base_url()?>css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bootstrap-icons.css">
    <style type="text/css">
      body {
        padding-top: 50px;
      }
    </style>
    <?php echo $this->renderSection('content_css');?>
  </head>
  <body>
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Absensi RFID</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User Menu
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passwd_modal">Ganti Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?=base_url()?>logout">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <a href="<?=base_url()?>admin" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-house">&nbsp;&nbsp;Beranda</span>
                </a>
                <a href="<?=base_url()?>admin/siswa" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-people">&nbsp;&nbsp;Siswa</span>
                </a>
                <a href="<?=base_url()?>admin/device" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-router">&nbsp;&nbsp;Device</span>
                </a>
                <a href="<?=base_url()?>admin/sesi" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-database-gear">&nbsp;&nbsp;Atur Sesi</span>
                </a>
                <a href="<?=base_url()?>presensi" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-person-check">&nbsp;&nbsp;Presensi</span>
                </a>
                <a href="<?=base_url()?>admin/rekap" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-clipboard-check">&nbsp;&nbsp;Rekap</span>
                </a>
            </div>
        </div>
        <div class="col py-3">
          <div class="container-fluid">
            <?php if (!is_null($alert)) : ?>
              <div class="row justify-content-center">
                <div class="col alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                  <?php echo nl2br((string) $alert['msg']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
            <?php endif; ?>
            
            <?php echo $this->renderSection('content');?>
          </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="passwd_modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Ganti Password</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Password Lama</label>
          <input type="password" class="form-control" id="old_password">
        </div>
        <div class="mb-3">
          <label class="form-label">Password Baru</label>
          <input type="password" class="form-control" id="new_password">
        </div>
        <div class="mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" class="form-control" id="confirm_password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="ganti_password()" class="btn btn-primary">simpan</button>
      </div>
    </div>
  </div>
</div>
<?php echo $this->renderSection('content_modal');?>
    
<script src="<?=base_url()?>js/jquery-3.6.2.min.js"></script>
<script src="<?=base_url()?>js/popper.min.js"></script>
<script src="<?=base_url()?>js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
async function ganti_password(){
  let data = {
    'old_password': document.getElementById('old_password').value,
    'new_password': document.getElementById('new_password').value,
    'confirm_password': document.getElementById('confirm_password').value
  };
  let res2 = await fetch('<?=base_url();?>admin/ganti_password/', {
    method: 'PUT', // *GET, POST, PUT, DELETE, etc.
    headers: {
      'Content-Type': 'application/json'
    },
    redirect: 'follow', // manual, *follow, error
    body: JSON.stringify(data) // body data type must match "Content-Type" header
  });
  if (res2.ok) {
    alert('Simpan Berhasil!');
  } else {
    alert('Simpan Gagal!');
  }
}
</script>
<?php echo $this->renderSection('content_js');?>
</body>
</html>
