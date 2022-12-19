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
      <?php echo $this->renderSection('content');?>
    </div>
    <script src="<?=base_url()?>/js/popper.min.js"></script>
    <script src="<?=base_url()?>/js/bootstrap.bundle.min.js"></script>
    <?php echo $this->renderSection('content_jss');?>
  </body>
</html>