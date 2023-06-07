<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("title") ?>
	<title>Users - SmartSys</title>
<?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Data Users</h4>
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
        <a href="<?php echo base_url('/datausers') ?>">Data User</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/datausers/tambah') ?>"><?= $title; ?></a>
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
              <form action="<?php echo base_url('/datausers/input') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control <?= validation_show_error("email") ? 'is-invalid' : ""; ?>" id="email" name="email" placeholder="Email" value="<?= old("email") ?>" autofocus>
                  <div class="invalid-feedback">
                    <?= validation_show_error("email") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control <?= validation_show_error("password") ? 'is-invalid' : ""; ?>" id="password" name="password" placeholder="Password" value="<?= old("password") ?>">
                  <div class="invalid-feedback">
                    <?= validation_show_error("password") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confpassword">Konfirmasi Password</label>
                  <input type="password" class="form-control <?= validation_show_error("confpassword") ? 'is-invalid' : ""; ?>" id="confpassword" name="confpassword" placeholder="Konfirmasi Password" value="<?= old("confpassword") ?>">
                  <div class="invalid-feedback">
                    <?= validation_show_error("confpassword") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="username">Username</label>
                  <div class="input-icon">
                    <span class="input-icon-addon">
                      <i class="fa fa-user"></i>
                    </span>
                    <input type="text" class="form-control <?= validation_show_error("username") ? 'is-invalid' : ""; ?>" id="username" name="username" placeholder="Username">
                    <div class="invalid-feedback">
                      <?= validation_show_error("username") ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="role">Role User</label>
                  <select class="form-control" id="role" name="role" value="<?= old("role") ?>">
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                  </select>
                  <div class="invalid-feedback">
                    <?= validation_show_error("role") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="profile_picture">Foto Profile</label>
                  <input type="file" class="form-control-file <?= validation_show_error("profile_picture") ? 'is-invalid' : ""; ?>" id="profile_picture" name="profile_picture">
                  <div class="invalid-feedback">
                    <?= validation_show_error("profile_picture") ?>
                  </div>
                </div>
                <br>
                <div class="card-action">
                  <button type="submit" class="btn btn-outline-success float-right mr-2">Simpan</button>
                  <a href="<?php echo base_url('/datausers') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
                </div>
              </form>
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
  <?php if (session()->getFlashdata('gagal_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Gagal Ditambahkan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>