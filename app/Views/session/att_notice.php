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
  <div class="card col-lg-10">
    <div class="card-body text-center">
      <h5 class="card-title">Tidak Ada Sesi</h5>
      <p>Tidak ada sesi yang sedang berlangsung.</p>
    </div>
  </div>
</div>
<?= $this->endSection();?>
<?= $this->section('content_js');?>
<script type="text/javascript">

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

document.addEventListener("DOMContentLoaded", async () => {
  await sleep(5000);
  location.reload();
});
</script>
<?= $this->endSection();?>