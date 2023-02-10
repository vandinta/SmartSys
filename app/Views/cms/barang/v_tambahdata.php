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
        <a href="<?php echo base_url('/databarang') ?>">Data Barang</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/databarang/tambah') ?>"><?= $title; ?></a>
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
              <form action="<?php echo base_url('/databarang/input') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="nama_barang">Nama Barang</label>
                  <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Nama Barang" value="<?= old("nama_barang") ?>" autofocus>
                  <div class="invalid-feedback">
                    <?= $validation->getError("nama_barang") ?>
                  </div>
                </div>
                <div class="form-group">
                  <label for="id_kategori">Nama Kategori</label>
                  <select class="form-control" id="id_kategori" name="id_kategori" value="<?= old("id_kategori") ?>">
                    <?php foreach ($kategori as $ktg) : ?>
                      <option value="<?= $ktg['id_kategori'] ?>"><?= $ktg['nama_kategori'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="stok_barang">Stok</label>
                  <input type="number" class="form-control" id="stok_barang" name="stok_barang" placeholder="Stok" value="<?= old("stok_barang") ?>">
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
                    <input type="text" class="form-control" id="harga_beli" name="harga_beli" value="<?= old("harga_beli") ?>">
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
                    <input type="text" class="form-control" id="harga_jual" name="harga_jual" value="<?= old("harga_jual") ?>">
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
                  <input type="file" class="form-control-file" id="image_barang" name="image_barang">
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