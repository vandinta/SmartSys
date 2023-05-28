<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("title") ?>
	<title>Karyawan - SmartSys</title>
<?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title"><?= $title; ?></h4>
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
        <a href="<?php echo base_url('/karyawan') ?>"><?= $title; ?></a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h4 class="card-title"><?= $title; ?></h4>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="add-row" class="display table table-striped table-hover">
              <thead>
                <tr>
                  <th style="width: 9%">No</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Terakhir Login</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; ?>
                <?php foreach ($karyawan as $kyn) : ?>
                  <tr>
                    <th scope="row"><?= $no++ ?></th>
                    <td><?= $kyn["email"] ?></td>
                    <td><?= $kyn["username"] ?></td>
                    <td><?php if ($kyn["last_login"] != null) {
                      echo tgl_indonesia($kyn["last_login"]);
                    } ?></td>
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
</script>
<?= $this->endSection() ?>