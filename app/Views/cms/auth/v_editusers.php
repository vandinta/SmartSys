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
        <a href="<?= base_url("/datausers") ?>"><?= $title; ?></a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?= base_url("/datausers/ubah") . "/" . $user['user_id'] ?>">Ubah Data User</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 col-lg-12">
              <div class="card-body">
                <!-- <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd"> -->
                <h4>Gambar Profile</h4>
                <img src="<?= base_url("assets/image/profile/" . $user["profile_picture"]); ?>" class="avatar-img rounded" style="max-width:auto; height: auto;">
                <!-- </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="card">
        <div class="col-md-6 col-lg-12">
          <div class="card-body">
            <?= $validation->listErrors(); ?>
            <form action="<?php echo base_url('/datausers/edit/') . "/" . $user["user_id"] ?>" method="post" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="<?= $user["email"] ?>" disabled>
                <div class="invalid-feedback">
                  <?= $validation->getError("email") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?= old("password") ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("password") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="confpassword">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confpassword" name="confpassword" placeholder="Konfirmasi Password" value="<?= old("confpassword") ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("confpassword") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control <?= $validation->hasError(
                                                          "username"
                                                        )
                                                          ? "is-invalid"
                                                          : "" ?>" id="username" name="username" placeholder="Username" value="<?= old("username")
                                                                                                                                  ? old("username")
                                                                                                                                  : $user["username"] ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("username") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" value="<?= old("role") ?>">
                  <option <?php if ($user['role'] == 'superadmin') {
                            echo 'selected="selected"';
                          } ?> value="superadmin">Super Admin</option>
                  <option <?php if ($user['role'] == 'admin') {
                            echo 'selected="selected"';
                          } ?> value="admin">Admin</option>
                  <option <?php if ($user['role'] == 'petugas') {
                            echo 'selected="selected"';
                          } ?> value="petugas">Petugas</option>
                </select>
              </div>
              <div class="form-group">
                <label for="profile_picture">Gambar Profile</label>
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture" value="<?= old("profile_picture")
                                                                                                                  ? old("profile_picture")
                                                                                                                  : $user["profile_picture"] ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("profile_picture") ?>
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

  <?php if (session()->getFlashdata('berhasil_diubah') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Diubah!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>