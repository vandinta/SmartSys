<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Penjualan</h4>
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
        <a href="<?php echo base_url('/datapenjualan') ?>"><?= $title; ?></a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/datapenjualan/ubah/') . "/" . $penjualan["id_penjualan"]?>">Ubah Data</a>
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
              <?= $validation->listErrors(); ?>
              <form action="<?php echo base_url('/datapenjualan/edit/') . "/" . $penjualan["id_penjualan"] ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="nama_penjualan">Nama Penjualan</label>
                  <input type="text" class="form-control <?= $validation->hasError(
                                                            "nama_penjualan"
                                                          )
                                                            ? "is-invalid"
                                                            : "" ?>" id="nama_penjualan" name="nama_penjualan" placeholder="Nama Penjualan" value="<?= old("nama_penjualan")
                                                                                                                                                  ? old("nama_penjualan")
                                                                                                                                                  : $penjualan["nama_penjualan"] ?>" autofocus>
                  <div class="invalid-feedback">
                    <?= $validation->getError("nama_penjualan") ?>
                  </div>
                </div>
                <div class="card-action">
                  <button type="submit" class="btn btn-outline-success float-right mr-2">Simpan</button>
                  <a href="<?php echo base_url('/datapenjualan') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
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