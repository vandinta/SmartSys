<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title"><?= $title; ?></h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="<?php echo base_url('/') ?>">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?= base_url("/ubahpassword") . "/" . $user['email'] ?>"><?= $title; ?></a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><?= $title; ?></div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 col-lg-12">
              <form action="<?php echo base_url('/ubahpassword') . "/" . $user["user_id"] ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="password">Password Lama</label>
                  <input type="password" class="form-control <?= validation_show_error("password_lama") ? 'is-invalid' : ""; ?>" id="password_lama" name="password_lama" placeholder="Password Lama" value="<?= old("password_lama") ?>">
                  <div class="invalid-feedback">
                    <?= validation_show_error("password_lama") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="password_baru">Password Baru</label>
                  <input type="password" class="form-control <?= validation_show_error("password_baru") ? 'is-invalid' : ""; ?>" id="password_baru" name="password_baru" placeholder="Password Baru" value="<?= old("password_baru") ?>">
                  <div class="invalid-feedback">
                    <?= validation_show_error("password_baru") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confpassword_baru">Konfirmasi Password Baru</label>
                  <input type="password" class="form-control <?= validation_show_error("confpassword_baru") ? 'is-invalid' : ""; ?>" id="confpassword_baru" name="confpassword_baru" placeholder="Konfirmasi Password Baru" value="<?= old("confpassword_baru") ?>">
                  <div class="invalid-feedback">
                    <?= validation_show_error("confpassword_baru") ?>
                  </div>
                </div>
                <br>
                <div class="card-action">
                  <button type="submit" class="btn btn-outline-success float-right mr-2">Simpan</button>
                  <a href="<?= base_url("/datausers") ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
                </div>
              </form>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section("content_js") ?>
<script>
  <?php if (session()->getFlashdata('gagal_diubah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Gagal Diubah!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>

  <?php if (session()->getFlashdata('password_gagal') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Password Tidak Cocok!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>

  <?php if (session()->getFlashdata('berhasil_diubah') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Diubah!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>