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
        <a href="<?php echo base_url('/datapenjualan') ?>">Data Penjualan</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url('/datapenjualan/tambah') ?>"><?= $title; ?></a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Tambah Barang</div>
        </div>
        <div class="card-body">
          <form action="<?php echo base_url('/datapenjualan/input_cart') ?>" method="post" id="form_order" role="form">
            <?= csrf_field() ?>
            <div class="form-group">
              <label for="pencarian">Cari Nama Barang</label>
              <select class="form-control" style="margin: 10px;" id="id_barang" name="id_barang" onchange="Hitung(this);" autofocus>
                <option value=""></option>
                <?php foreach ($barang as $brg) : ?>
                  <option data-harga="<?= $brg['harga_jual'] ?>" data-nama="<?= $brg['nama_barang'] ?>" value="<?= $brg['id_barang'] ?>"><?= $brg['nama_barang'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <!-- <input type="hidden" id="nama_barang" name="nama_barang[]"> -->
            <div class="form-group">
              <label for="harga_barang">Harga Barang</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp.</span>
                </div>
                <input type="text" class="form-control" id="harga_barang" name="harga_barang[]" value="" disabled>
                <div class="input-group-append">
                  <span class="input-group-text">.00</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="qty">QTY</label>
              <input type="number" class="form-control" id="qty" name="qty" onchange="Hitung(this);" placeholder="QTY">
            </div>
            <div class="form-group">
              <label for="jumlah_harga">Jumlah Harga</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp.</span>
                </div>
                <input type="text" class="form-control" id="jumlah_harga" name="jumlah_harga[]" value="" disabled>
                <div class="input-group-append">
                  <span class="input-group-text">.00</span>
                </div>
              </div>
            </div>
            <input type="hidden" id="jumlah_harga_hide" name="jumlah_harga_hide[]">
            <br>
            <div>
              <button type="submit" class="btn btn-outline-primary float-right mr-2"><i class="fa fa-plus"> Tambah</i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Total Harga</div>
        </div>
        <div class="card-body">
          <div class="form-group">
            <h2><?php if($harga['total_harga'] != null){
              echo rupiah($harga['total_harga']);
            } else {
              echo rupiah('0');
            } ?></h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">List Barang</div>
        </div>
        <div class="card-body">
          <form action="<?php echo base_url('/datapenjualan/save') ?>" method="post" class="formtambahpenjualan">
            <?= csrf_field() ?>
            <table id="list_pembelian" class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">No</th>
                  <th scope="col">Barang</th>
                  <th scope="col">Harga Satuan</th>
                  <th scope="col">Banyak</th>
                  <th scope="col">Jumlah</th>
                  <th style="width: 3%; text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($cart as $crt) : ?>
                  <tr>
                    <th scope="row"><?= $no++ ?></th>
                    <td><?= $crt["nama_barang"] ?></td>
                    <td><?= rupiah($crt["harga_jual"]) ?></td>
                    <td><?= $crt["qty"] ?></td>
                    <td><?= rupiah($crt["jumlah_harga"]) ?></td>
                    <td>
                      <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" onclick="hapus(<?= $crt["id_cart"] ?>)" data-original-title="Hapus">
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <br>
            <div>
              <button type="submit" class="btn btn-outline-success float-right mr-2 simpandata">Simpan</button>
              <a href="<?php echo base_url('/datapenjualan') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Batal</a>
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
  $("#id_barang").select2({
    placeholder: "Pilih Nama Barang"
  });

  function Hitung(v) {
    // var nama = $('#id_barang option:selected').data('nama');
    var harga = $('#id_barang option:selected').data('harga');
    var jumlah = $("#qty").val();

    var total = harga * jumlah;

    // $('#nama_barang').val(nama);
    $('#harga_barang').val(harga);
    $('#jumlah_harga').val(total);
    $('#jumlah_harga_hide').val(total);
  }

  function hapus(id) {
    $.ajax({
      url: '<?= base_url('/datapenjualan/delete_cart') . "/" ?>' + id,
      method: 'delete',
    });
    window.location.reload(true);
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