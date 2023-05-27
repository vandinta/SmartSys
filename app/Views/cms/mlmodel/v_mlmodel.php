<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Model Perhitungan</h4>
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
        <a href="<?php echo base_url('/datamodel') ?>"><?= $title; ?></a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h4 class="card-title"><?= $title; ?></h4>
            <div class="ml-auto">
              <!-- <button type="button" class="btn btn-icon btn-round btn-info mr-1" data-toggle="modal" data-target="#importModal">
                <i class="fa fa-sign-in-alt"></i>
              </button>
              <button type="button" class="btn btn-icon btn-round btn-info" data-toggle="modal" data-target="#exportModal">
                <i class="fa fa-sign-out-alt"></i>
              </button> -->
            </div>
            <a href="<?php echo base_url('/datamodel/tambah') ?>" type="button" class="btn btn-primary btn-round ml-2" <?php if ($_SESSION['role'] == "superadmin") {
                                                                                                                          echo "hidden";
                                                                                                                        } ?>><i class="fa fa-plus"></i> Buat Model Perhitungan</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="add-row" class="display table table-striped table-hover">
              <thead>
                <tr>
                  <th style="width: 9%">No</th>
                  <!-- <th style="width: 20%">Image Barang</th> -->
                  <th>Nama Model Perhitungan</th>
                  <th>Nilai Akurasi Model</th>
                  <!-- <th>Harga Beli</th>
                  <th>Harga Jual</th> -->
                  <!-- <th style="width: 8%" <?php if ($_SESSION['role'] == "superadmin") {
                                          echo "hidden";
                                        } ?>>Aksi</th> -->
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($model as $mdl) : ?>
                  <tr>
                    <th scope="row"><?= $no++ ?></th>
                    <td><?= $mdl["nama_model"] ?></td>
                    <td>
                      <?= $mdl["nilai_akurasi"];
                      ?>
                    </td>
                    <?php  ?>
                    <!-- <td <?php if ($_SESSION['role'] == "superadmin") {
                          echo "hidden";
                        } ?>>
                      <div class="form-button-action">
                        <a href="<?= base_url('/datamodel/ubah/') . "/" . $mdl["id_barang"] ?>">
                          <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-success" data-original-title="Detail">
                            <i class="fa fa-eye"></i>
                          </button>
                        </a>
                        <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" onclick="hapus(<?= $mdl["id_barang"] ?>)" data-original-title="Hapus">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </td> -->
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section("content_js") ?>
<script>
  $(document).ready(function() {
    // $('#multi-filter-select').DataTable({
    //   "pageLength": 5,
    //   initComplete: function() {
    //     this.api().columns().every(function() {
    //       var column = this;
    //       var select = $('<select class="form-control"><option value=""></option></select>')
    //         .appendTo($(column.head()).empty())
    //         .on('change', function() {
    //           var val = $.fn.dataTable.util.escapeRegex(
    //             $(this).val()
    //           );

    //           column
    //             .search(val ? '^' + val + '$' : '', true, false)
    //             .draw();
    //         });

    //       column.data().unique().sort().each(function(d, j) {
    //         select.append('<option value="' + d + '">' + d + '</option>')
    //       });
    //     });
    //   }
    // });

    // Add Row
    $('#add-row').DataTable({
      "pageLength": 5,
    });

    var action = '<td> <div class="form-button-action"> <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';
  });

  <?php if (session()->getFlashdata('berhasil_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Ditambahkan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
  
  <?php if (session()->getFlashdata('berhasil_import') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Diimport!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>

  <?php if (session()->getFlashdata('gagal_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Anda Tidak Ditemukan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>