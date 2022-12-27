<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title?></title>
    <link href="<?=base_url()?>/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>/css/bootstrap-icons.css">
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
                <li><a class="dropdown-item" href="<?=base_url()?>/admin/password">Ganti Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?=base_url()?>/logout">Logout</a></li>
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
                <a href="<?=base_url()?>/admin" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-compass">&nbsp;&nbsp;Beranda</span>
                </a>
                <a href="<?=base_url()?>/admin/siswa" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-people">&nbsp;&nbsp;Siswa</span>
                </a>
                <a href="<?=base_url()?>/admin/sesi" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-database-gear">&nbsp;&nbsp;Atur Sesi</span>
                </a>
                <a href="<?=base_url()?>/admin/presensi" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-person-check">&nbsp;&nbsp;Presensi</span>
                </a>
                <a href="<?=base_url()?>/admin/rekap" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline bi bi-clipboard-check">&nbsp;&nbsp;Rekap</span>
                </a>
            </div>
        </div>
        <div class="col py-3">
          <div class="container-fluid">
            <?php if (!is_null($alert)) : ?>
              <div class="row justify-content-center">
                <div class="col alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                  <?php echo nl2br($alert['msg']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
            <?php endif; ?>
            
            <?php echo $this->renderSection('content');?>
          </div>
        </div>
    </div>
</div>
<?php echo $this->renderSection('content_modal');?>
        
    <script src="<?=base_url()?>/js/jquery-3.6.2.min.js"></script>
    <script src="<?=base_url()?>/js/popper.min.js"></script>
    <script src="<?=base_url()?>/js/bootstrap.bundle.min.js"></script>
    <?php echo $this->renderSection('content_js');?>
  </body>
</html>