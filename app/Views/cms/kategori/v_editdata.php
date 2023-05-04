<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Kategori</h4>
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
        <a href="<?php echo base_url('/datakategori') ?>">Data Kategori</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/datakategori/ubah/') . "/" . $kategori["id_kategori"]?>">Ubah Data</a>
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
              <form action="<?php echo base_url('/datakategori/edit/') . "/" . $kategori["id_kategori"] ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="nama_kategori">Nama Kategori</label>
                  <input type="text" class="form-control <?= validation_show_error("nama_kategori") ? 'is-invalid' : ""; ?>" id="nama_kategori" name="nama_kategori" placeholder="Nama Kategori" value="<?= old("nama_kategori")
                                                                                                                                                  ? old("nama_kategori")
                                                                                                                                                  : $kategori["nama_kategori"] ?>" autofocus>
                  <div class="invalid-feedback">
                    <?= validation_show_error("nama_kategori") ?>
                  </div>
                </div>
                <div class="card-action">
                  <button type="submit" class="btn btn-outline-success float-right mr-2">Simpan</button>
                  <a href="<?php echo base_url('/datakategori') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
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