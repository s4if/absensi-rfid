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
    </div>
    <script src="<?=base_url()?>/js/popper.min.js"></script>
    <script src="<?=base_url()?>/js/bootstrap.bundle.min.js"></script>
    <?php echo $this->renderSection('content_jss');?>
  </body>
</html>