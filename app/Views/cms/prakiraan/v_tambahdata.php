<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Model</h4>
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
        <a href="<?php echo base_url('/datamodel') ?>">Data Model</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/datamodel/tambah') ?>"><?= $title; ?></a>
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
              <form action="<?php echo base_url('/datamodel/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                  <label for="id_barang">Nama Barang</label>
                  <select class="form-control" id="id_barang" name="id_barang" value="<?= old("id_barang") ?>">
                    <?php foreach ($barang as $brg) : ?>
                      <option value="<?= $brg['id_barang'] ?>"><?= $brg['nama_barang'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="lim_akurasi">Limit Nilai Akurasi</label>
                  <input type="number" class="form-control <?= validation_show_error("lim_akurasi") ? 'is-invalid' : ""; ?>" id="lim_akurasi" name="lim_akurasi" placeholder="Limit Nilai Akurasi" value="50">
                  <div class="invalid-feedback">
                    <?= validation_show_error("lim_akurasi") ?>
                  </div>
                </div>
                <div class="form-group">
                  <h6 class="float-left">*Semakin besar batas nilai akurasi yang diinginkan maka semakin lama waktu tunggu</h6>
                </div>
                <br>
                <div class="card-action">
                  <button type="submit" class="btn btn-outline-success float-right mr-2" onclick="simpan()">Simpan</button>
                  <a href="<?php echo base_url('/datamodel') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
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
  function simpan() {
    Swal.fire({
      title: 'Sedang Diproses!',
      html: 'mohon tunggu proses sampai selesai, semakin besar nilai akurasi yang diinginkan maka semakin lama waktu tunggu',
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });
  }
  <?php if (session()->getFlashdata('gagal_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Gagal Ditambahkan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>