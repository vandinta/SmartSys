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
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h4 class="card-title"><?= $title; ?></h4>
            <a href="<?php echo base_url('/datakategori/tambah') ?>" type="button" class="btn btn-primary btn-round ml-auto" <?php if ($_SESSION['role'] == "superadmin") {
                                                                                                                                echo "hidden";
                                                                                                                              } ?>><i class="fa fa-plus"></i>Tambah Data</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="add-row" class="display table table-striped table-hover">
              <thead>
                <tr>
                  <th style="width: 9%">No</th>
                  <th>Kategori</th>
                  <th style="width: 17%" <?php if ($_SESSION['role'] == "superadmin") {
                                            echo "hidden";
                                          } ?>>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($kategori as $ktg) : ?>
                  <tr>
                    <th scope="row"><?= $no++ ?></th>
                    <td><?= $ktg["nama_kategori"] ?></td>
                    <?php  ?>
                    <td <?php if ($_SESSION['role'] == "superadmin") {
                          echo "hidden";
                        } ?>>
                      <div class="form-button-action">
                        <a href="<?php echo base_url('/datakategori/ubah/') . "/" . $ktg["id_kategori"] ?>">
                          <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary" data-original-title="Ubah">
                            <i class="fa fa-edit"></i>
                          </button>
                        </a>
                        <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-danger" onclick="hapus(<?= $ktg["id_kategori"] ?>)" data-original-title="Hapus">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </td>
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

    // $('#addRowButton').click(function() {
    //   $('#add-row').dataTable().fnAddData([
    //     $("#nama_kategori").val(),
    //     action
    //   ]);
    //   $('#addRowModal').modal('hide');

    // });

  });

  function hapus(id) {
    Swal.fire({
      title: 'Peringatan!!!',
      text: "apakah anda yakin ingin menghapus Data Kategori ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= base_url('/datakategori') . "/" ?>' + id,
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

  <?php if (session()->getFlashdata('berhasil_tambah') != NULL) { ?>
    Swal.fire({
      icon: 'success',
      title: 'Data Berhasil Ditambahkan!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>