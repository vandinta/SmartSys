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
          <div class="card-title">Detail Penjualan</div>
        </div>
        <div class="card-body">
          <form action="<?php echo base_url('/datapenjualan/ubah_penjualan') . "/" . $penjualan["id_penjualan"] ?>" method="post" id="form_order" role="form">
            <?= csrf_field() ?>
            <div class="form-group">
              <label for="nama_penjualan">Nama Penjualan</label>
              <input type="text" class="form-control <?= validation_show_error("nama_penjualan") ? 'is-invalid' : ""; ?>" id="nama_penjualan" name="nama_penjualan" value="<?= old("nama_penjualan")
                                                                                                                                                                              ? old("nama_penjualan")
                                                                                                                                                                              : $penjualan["nama_penjualan"] ?>" placeholder="Nama Penjualan" <?php if ($expired != 1) {
                                                                                                                                                                                                                                                echo 'disabled';
                                                                                                                                                                                                                                              } ?>>
              <div class="invalid-feedback">
                <?= validation_show_error("nama_penjualan") ?>
              </div>
            </div>
            <div class="form-group">
              <label for="user">Di Inputkan Oleh</label>
              <input type="text" class="form-control" id="user" name="user" value="<?= $penjualan["username"] ?>" placeholder="Username Admin" disabled>
            </div>
            <input type="hidden" id="jumlah_harga_hide" name="jumlah_harga_hide[]">
            <br>
            <?php if ($expired == 1) { ?>
              <div class="card-action">
                <button type="submit" class="btn btn-outline-primary float-right mr-2"><i class="fa fa-pen"> Ubah</i></button>
              </div>
            <?php } ?>
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
            <h2><?= rupiah($penjualan['total_harga']) ?></h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h4 class="card-title">List Barang</h4>
            <?php if ($expired == 1) { ?>
              <button type="button" class="btn btn-outline-primary ml-auto" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Tambah</button>
            <?php } ?>
          </div>
        </div>
        <div class="card-body">
          <table id="list_pembelian" class="table table-hover">
            <thead>
              <tr>
                <th style="width: 3%; text-align:center;">No</th>
                <th style="width: 15%; text-align:center;">Barang</th>
                <th style="width: 15%; text-align:center;">Harga Satuan</th>
                <th style="width: 5%; text-align:center;">Banyak</th>
                <th style="width: 15%; text-align:center;">Jumlah</th>
                <?php if ($expired == 1) { ?>
                  <th style="width: 10%; text-align:center;">Aksi</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              <?php foreach ($order as $odr) : ?>
                <tr>
                  <th scope="row" style="text-align:center;"><?= $no++ ?></th>
                  <td style="text-align:center;"><?= $odr["nama_barang"] ?></td>
                  <td style="text-align:center;"><?= rupiah($odr["harga_jual"]) ?></td>
                  <td style="text-align:center;"><?= $odr["jumlah_barang"] ?></td>
                  <td style="text-align:center;"><?php $total = $odr["jumlah_barang"] * $odr["harga_jual"];
                                                  echo rupiah($total) ?></td>
                  <?php if ($expired == 1) { ?>
                    <td style="text-align:center;">
                      <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-sm btn-edit" data-original-title="Ubah" data-id_penjualan="<?= $odr['id_penjualan']; ?>" data-id_barang="<?= $odr['id_barang']; ?>" data-id_order="<?= $odr['id_order']; ?>" data-nama_barang="<?= $odr['nama_barang']; ?>" data-jumlah_barang="<?= $odr['jumlah_barang']; ?>" data-harga_jual="<?= $odr['harga_jual_barang']; ?>">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" onclick="hapus(<?= $odr["id_order"] ?>)" data-original-title="Hapus">
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  <?php } ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card-action">
          <a href="<?php echo base_url('/datapenjualan') ?>" type="button" class="btn btn-outline-danger float-right mr-2">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="addModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('/datapenjualan/input_order') ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-body">
          <input type="hidden" id="id_penjualan" name="id_penjualan" value="<?= $penjualan["id_penjualan"] ?>">
          <div class="form-group">
            <label for="pencarian">Cari Nama Barang</label>
            <div>
              <select class="form-control" style="width: 450px;" id="id_barang" name="id_barang" onchange="Hitung(this);" autofocus>
                <option value=""></option>
                <?php foreach ($barang as $brg) : ?>
                  <option data-harga="<?= $brg['harga_jual'] ?>" data-nama="<?= $brg['nama_barang'] ?>" value="<?= $brg['id_barang'] ?>"><?= $brg['nama_barang'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
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
          <input type="hidden" id="jumlah_harga_barang" name="jumlah_harga_barang">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal Tambah Data -->

<!-- Modal Edit Data -->
<div class="modal fade" id="editModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo base_url('/datapenjualan/ubah_order') ?>" method="post">
        <div class="modal-body">
          <input type="hidden" class="form-control id_penjualan" name="id_penjualan">
          <input type="hidden" class="form-control id_barang" name="id_barang">
          <input type="hidden" class="form-control id_order" name="id_order">
          <div class="form-group">
            <label for="qty">QTY</label>
            <input type="number" class="form-control jumlah_barang" id="qty" name="qty" placeholder="QTY">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal Edit Data-->
<?= $this->endSection() ?>

<?= $this->section("content_js") ?>
<script>
  $("#id_barang").select2({
    placeholder: "Pilih Nama Barang"
  });

  function Hitung(v) {
    var harga = $('#id_barang option:selected').data('harga');
    var jumlah = $("#qty").val();

    var total = harga * jumlah;

    $('#harga_barang').val(harga);
    $('#jumlah_harga').val(total);
    $('#jumlah_harga_barang').val(total);
  }

  $(document).ready(function() {

    $('.btn-edit').on('click', function() {
      const id_penjualan = $(this).data('id_penjualan');
      const id_order = $(this).data('id_order');
      const id_barang = $(this).data('id_barang');
      const nama_barang = $(this).data('nama_barang');
      const jumlah_barang = $(this).data('jumlah_barang');
      const harga_jual = $(this).data('harga_jual');
      $('.id_penjualan').val(id_penjualan);
      $('.id_order').val(id_order);
      $('.id_barang').val(id_barang);
      $('.nama_barang').val(nama_barang).trigger('change');
      $('.jumlah_barang').val(jumlah_barang);
      $('.harga_jual').val(harga_jual);
      $('#editModal').modal('show');
    });
  });

  function hapus(id) {
    Swal.fire({
      title: 'Peringatan!!!',
      text: "apakah anda yakin ingin menghapus Data Barang ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= base_url('/datapenjualan/delete_order') . "/" ?>' + id,
          method: 'delete',
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil Menghapus Data!',
              text: response.message,
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'Oke',
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.reload(true);
              }
            })
            fetchAllPosts();
          }
        });
      }
    });
  }

  <?php if (session()->getFlashdata('gagal_diubah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Gagal Diubah!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>

  <?php if (session()->getFlashdata('berhasil_tambah_order') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Barang Berhasil Ditambahkan!',
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

  <?php if (session()->getFlashdata('berhasil_diubah_order') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Barang Berhasil Diubah!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>