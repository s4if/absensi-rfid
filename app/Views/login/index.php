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
  <div class="card col-lg-6">
    <div class="card-body">
      <h5 class="card-title">Halaman Login</h5>
      <form action="/login" method="POST">
        <div class="mb-3">
          <label class="form-label">Username : </label>
          <input type="text" name="username" class="form-control" >
        </div>
        <div class="mb-3">
          <label class="form-label">Password : </label>
          <input type="password" name="password" class="form-control" >
        </div>
        <div class="mb-3">
          <input type="submit" value="Login" class="btn btn-success">
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection();?>