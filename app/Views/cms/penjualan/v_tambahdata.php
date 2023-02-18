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
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><?= $title; ?></div>
        </div>
        <div class="card-body">
          <form action="<?php echo base_url('/datapenjualan/save') ?>" method="post" class="formtambahpenjualan">
            <?= csrf_field() ?>
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Barang</th>
                  <th scope="col">Harga</th>
                  <th scope="col">Banyak</th>
                  <th scope="col">Jumlah</th>
                  <th style="width: 3%; text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody class="formtambah">
                <tr>
                  <td>
                    <select class="form-control" style="margin: 10px;" id="id_barang" name="id_barang" onchange="Hitung(this);">
                      <?php foreach ($barang as $brg) : ?>
                        <option data-harga="<?= $brg['harga_jual'] ?>" value="<?= $brg['id_barang'] ?>"><?= $brg['nama_barang'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <input type="text" class="form-control" id="harga_barang" name="harga_barang" disabled>
                  </td>
                  <td>
                    <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" onchange="Hitung(this);" placeholder="Jumlah">
                  </td>
                  <td>
                    <input type="text" class="form-control" id="jumlah_harga_barang" name="jumlah_harga_barang" disabled>
                  </td>
                  <td>
                    <button type="button" data-toggle="tooltip" title="" class="btn btn-outline-primary addform" data-original-title="Tambah">
                      <i class="fa fa-plus"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="col-md-2 col-lg-3">
              <div class="coloum">
                <h4>Total Harga</h4>
                <input type="text" class="form-control" id=" " name=" " disabled>
              </div>
            </div>
            <br><br>
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
  $(document).ready(function(e) {
    $('.addform').click(function(e) {
      e.preventDefault();

      var html = '<tr><td><select class="form-control" style="margin: 10px;" id="id_barang" name="id_barang" onchange="Hitung(this);"><?php foreach ($barang as $brg) : ?><option data-harga="<?= $brg['harga_jual'] ?>" value="<?= $brg['id_barang'] ?>"><?= $brg['nama_barang'] ?></option><?php endforeach; ?></select></td><td><input type="text" class="form-control" id="harga_barang" name="harga_barang" disabled></td><td><input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" onchange="Hitung(this);" placeholder="Jumlah"></td><td><input type="text" class="form-control" id="jumlah_harga_barang" name="jumlah_harga_barang" disabled></td><td><button type="button" data-toggle="tooltip" title="" class="btn btn-outline-danger deleteform" data-original-title="Hapus"><i class="fa fa-trash"></i></button></td></tr>';

      $('.formtambah').append(html);
    });
  });

  $(document).on('click', '.deleteform', function(e) {
    e.preventDefault();

    $(this).parents('tr').remove();
  });

  $(document).ready(function(e) {
    $('.formtambahpenjualan').click(function(e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        dataType: 'json',
        beforeSend: function() {
          $('.simpandata').attr('disable', 'disabled');
          $('.simpandata').html('<i class="fa fa-spin fa-spinner"></i>');
        },
        complete: function() {
          $('.simpandata').removeAttr('disable');
          $('.simpandata').html('Simpan');
        },
        success: function() {
          if (response.error) {
            window.location.href = ("<?php echo base_url('/datapenjualan') ?>");
          }
        }
      })
    });
  });

  $('#id_barang').on('change', function() {
    const harga = $('#id_barang option:selected').data('harga');
    alert(harga);

    $('#harga_barang').val(harga);
  });

  function Hitung(v) {
    var index = $(v).parent().parent().index();

    var harga = document.getElementsByName("harga_barang")[index].value;
    var jumlah = document.getElementsByName("jumlah_barang")[index].value;
    var jumlah_harga = harga * jumlah;

    document.getElementsByName('jumlah_harga_barang')[index].value = jumlah_harga;
  }

  // function Hitung(v) {
  //   var index = $(v).parent().parent().index();

  //   // var select = document.getElementById("id_barang").length;;
  //   // alert(select);
  //   // var barang = document.getElementById('id_barang')[0];
  //   var barang = document.getElementById('id_barang');
  //   // var harga = barang.getAttribute('data-harga');
  //   // alert(barang);
  //   var harga = barang.selectedIndex;
  //   // alert(harga);
  //   var harga_barang = barang.options[harga].getAttribute('data-harga');
  //   // alert(harga_barang);
  //   // alert(harga);
  //   var jumlah = document.getElementsByName("jumlah_barang")[index].value;
  //   // alert(jumlah);

  //   var jumlah_harga = harga_barang * jumlah;

  //   // $('#jumlah_harga_barang')[index].val(total);


  //   // var harga = document.getElementsByName('#id_barang option:selected')[index].value;
  //   // var jumlah = document.getElementsByName('jumlah_barang')[index].value;

  //   // var jumlah_harga = harga * jumlah;
  //   document.getElementsByName('jumlah_harga_barang')[index].value = jumlah_harga;
  // }

  // $(document).ready(function(e) {
  //   $('#id_barang').on('change', function() {
  //     const harga = $('#id_barang option:selected').data('harga');
  //     const jumlah = $("#jumlah_barang").val();

  //     const total = harga * jumlah;

  //     $('#jumlah_harga_barang').val(total);
  //   });

  //   $('#jumlah_barang').on('change', function() {
  //     const harga = $('#id_barang option:selected').data('harga');
  //     const jumlah = $("#jumlah_barang").val();

  //     const total = harga * jumlah;

  //     $('#jumlah_harga_barang').val(total);
  //   });
  // });

  // $('#id_barang').on('change', function() {
  //   const harga = $('#id_barang option:selected').data('harga');
  //   const jumlah = $("#jumlah_barang").val();

  //   const total = harga * jumlah;

  //   $('#jumlah_harga_barang').val(total);
  // });

  // $('#jumlah_barang').on('change', function() {
  //   const harga = $('#id_barang option:selected').data('harga');
  //   const jumlah = $("#jumlah_barang").val();

  //   const total = harga * jumlah;

  //   $('#jumlah_harga_barang').val(total);
  // });

  <?php if (session()->getFlashdata('gagal_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Gagal Ditambahkan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>