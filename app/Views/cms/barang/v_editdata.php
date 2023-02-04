<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title"><?= $title; ?></h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="<?= base_url('/') ?>">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?= base_url('/databarang') ?>"><?= $title; ?></a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/databarang/ubah/') . "/" . $barang["id_barang"] ?>">Detail Data</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-5">
      <!-- <div class="card-header">
        <div class="card-title"><?= $title; ?></div>
      </div> -->
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 col-lg-12">
              <div class="card-body">
                <!-- <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd"> -->
                <h4>Gambar Barang</h4>
                <img src="<?= base_url("assets/image/barang/" . $barang["image_barang"]); ?>" class="avatar-img rounded" style="max-width:auto; height: auto;">
                <!-- </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="card">
        <!-- <div class="card-header">
          <div class="card-title"><?= $title; ?></div>
        </div> -->
        <div class="col-md-6 col-lg-12">
          <div class="card-body">
            <?= $validation->listErrors(); ?>
            <form action="<?php echo base_url('/databarang/edit/') . "/" . $barang["id_barang"] ?>" method="post" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" class="form-control <?= $validation->hasError(
                                                          "nama_barang"
                                                        )
                                                          ? "is-invalid"
                                                          : "" ?>" id="nama_barang" name="nama_barang" placeholder="Nama Barang" value="<?= old("nama_barang")
                                                                                                                                          ? old("nama_barang")
                                                                                                                                          : $barang["nama_barang"] ?>" autofocus>
                <div class="invalid-feedback">
                  <?= $validation->getError("nama_barang") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="id_kategori">Nama Kategori</label>
                <select class="form-control" id="id_kategori" name="id_kategori" value="<?= old("id_kategori") ?>">
                  <?php foreach ($kategori as $ktg) : ?>
                    <option <?php if ($barang['id_kategori'] == $ktg['id_kategori']) {
                              echo 'selected="selected"';
                            } ?> value="<?= $ktg['id_kategori'] ?>"><?= $ktg['nama_kategori'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="stok_barang">Stok</label>
                <input type="number" class="form-control" id="stok_barang" name="stok_barang" placeholder="Stok" value="<?= old("stok_barang")
                                                                                                                          ? old("stok_barang")
                                                                                                                          : $barang["stok_barang"] ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("stok_barang") ?>
                </div>
              </div>
              <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input type="text" class="form-control" id="harga_beli" name="harga_beli" value="<?= old("harga_beli")
                                                                                                      ? old("harga_beli")
                                                                                                      : $barang["harga_beli"] ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                  <div class="invalid-feedback">
                    <?= $validation->getError("harga_beli") ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp.</span>
                  </div>
                  <input type="text" class="form-control" id="harga_jual" name="harga_jual" value="<?= old("harga_jual")
                                                                                                      ? old("harga_jual")
                                                                                                      : $barang["harga_jual"] ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                  <div class="invalid-feedback">
                    <?= $validation->getError("harga_jual") ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="image_barang">Gambar Barang</label>
                <input type="file" class="form-control-file" id="image_barang" name="image_barang" value="<?= old("image_barang")
                                                                                                            ? old("image_barang")
                                                                                                            : $barang["image_barang"] ?>">
                <div class="invalid-feedback">
                  <?= $validation->getError("image_barang") ?>
                </div>
              </div>
              <br>
              <div class="card-action">
                <button type="submit" class="btn btn-outline-success float-right mr-2">Simpan</button>
                <a href="<?php echo base_url('/databarang') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
              </div>
            </form>
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