<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Prediksi Penjualan</h4>
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
        <a href="<?php echo base_url('/dataprakiraan') ?>"><?= $title; ?></a>
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
            </div>
            <a href="<?php echo base_url('/dataprakiraan/tambah') ?>" type="button" class="btn btn-primary btn-round ml-2" <?php if ($_SESSION['role'] == "superadmin") {
                                                                                                                              echo "hidden";
                                                                                                                            } ?>><i class="fa fa-plus"></i> Tambah Data Prediksi</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="add-row" class="display table table-striped table-hover">
              <thead>
                <tr>
                  <th style="width: 10%; text-align:center;">No</th>
                  <th>Nama Barang</th>
                  <!-- <th>Prakiraan</th> -->
                  <th style="width: 14%; text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($prakiraan as $pra) : ?>
                  <tr>
                    <th scope="row" style="text-align:center;"><?= $no++ ?></th>
                    <td><?= $pra["nama_barang"] ?></td>
                    <!-- <td><?= $pra["nama_prakiraan"] ?></td> -->
                    <td style="text-align:center;">
                      <div class="form-button-action">
                        <a href="<?= base_url('/dataprakiraan/detail/') . "/" . $pra["id_prakiraan"] ?>">
                          <button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-success" data-original-title="Detail">
                            <i class="fa fa-eye"></i>
                          </button>
                        </a>
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

  <?php if (session()->getFlashdata('gagal_import') != NULL) { ?>
    Swal.fire({
      icon: 'error',
      title: 'Data Anda Tidak Sesuai!',
      confirmButtonColor: '#1572E8',
    });
  <?php } ?>
</script>
<?= $this->endSection() ?>