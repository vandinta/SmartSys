<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="content">
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div class="col-md-8 mt-3">
          <h2 class="text-white pb-2 fw-bold">Selamat Datang, <?php echo session('username') ?></h2>
          <h5 class="text-white op-7 mb-2">Satu-satunya cara untuk melakukan pekerjaan hebat yaitu dengan mencintai apa yang sedang kamu lakukan.</h5>
        </div>
        <div class="col-md-4">
          <script src="//widget.time.is/id.js"></script>
          <script>
            time_is_widget.init({
              Klaten_z41c: {
                template: "TIME<br>DATE",
                date_format: "dayname, dnum monthname year"
              }
            });
          </script>
          <div class="card card-stats card-round">
            <div class="card-body ">
              <div class="row">
                <div class="col-3">
                  <div class="icon-big text-center">
                    <i class="flaticon-time text-warning"></i>
                  </div>
                </div>
                <div class="col-9 col-stats">
                  <div class="numbers">
                    <p class="card-title"><span id="Klaten_z41c" style="font-size:20px"></span></p>
                    <p class="card-category"><a href="https://time.is/Klaten" id="time_is_link" rel="nofollow" style="font-size:20px"></a></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row">
      <div class="col-md-4">
        <div class="card card-dark bg-primary-gradient">
          <div class="card-body pb-0">
            <h2 class="mb-2"><?= $transaksi; ?></h2>
            <p>Transaksi Hari Ini</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-dark bg-secondary-gradient">
          <div class="card-body pb-0">
            <h2 class="mb-2"><?php if ($items != null) {
                                echo $items;
                              } else {
                                echo 0;
                              } ?></h2>
            <p>Item Terjual Hari Ini</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-dark bg-success2">
          <div class="card-body pb-0">
            <h2 class="mb-2"><?php if ($keuntungan != null) {
                                echo rupiah($keuntungan);
                              } else {
                                echo 0;
                              } ?></h2>
            <p>Keuntungan Hari Ini</p>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Penjualan Barang Hari Ini</div>
          </div>
          <div class="card-body pb-0" style="display:block; height:350px; overflow:auto;">
            <?php if ($harian == 'kosong') { ?>
              <h4 class="mb-1">Tidak Ada Penjualan</h4>
            <?php } else { ?>
              <?php foreach ($harian as $har) : ?>
                <div class="d-flex">
                  <div class="flex-1 ml-2 pt-1">
                    <h4 class="mb-1"><?= $har['nama_barang']; ?></h4>
                  </div>
                  <div class="float-right pt-1">
                    <h4 class="text-info"><?= $har['jumlah_barang'] . ' Item'; ?></h4>
                  </div>
                </div>
                <div class="separator-dashed"></div>
              <?php endforeach; ?>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Penjualan Barang Bulan Ini</div>
          </div>
          <div class="card-body pb-0" style="display:block; height:350px; overflow:auto;">
            <?php if ($bulanan == 'kosong') { ?>
              <h4 class="mb-1">Tidak Ada Penjualan</h4>
            <?php } else { ?>
              <?php foreach ($bulanan as $bul) : ?>
                <div class="d-flex">
                  <div class="flex-1 ml-2 pt-1">
                    <h4 class="mb-1"><?= $bul['nama_barang']; ?></h4>
                  </div>
                  <div class="float-right pt-1">
                    <h4 class="text-info"><?= $bul['jumlah_barang'] . ' Item'; ?></h4>
                  </div>
                </div>
                <div class="separator-dashed"></div>
              <?php endforeach; ?>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Barang Stok Sedikit</div>
            </div>
          </div>
          <div class="card-body">
            <?php foreach ($stok as $stk) : ?>
              <div class="d-flex">
                <div class="flex-1 ml-2 pt-1">
                  <h4 class="mb-1"><?= $stk["nama_barang"] ?> <?php if ($stk["stok_barang"] == 0) {
                                                                echo '<span class="text-dark pl-3">Habis</span>';
                                                              } elseif ($stk["stok_barang"] < 20) {
                                                                echo '<span class="text-danger pl-3">Menipis</span>';
                                                              } elseif ($stk["stok_barang"] < 50) {
                                                                echo '<span class="text-warning pl-3">Sedang</span>';
                                                              } else {
                                                                echo '<span class="text-success pl-3">Aman</span>';
                                                              }
                                                              ?></h4>
                </div>
                <div class="float-right pt-1">
                  <h5 class="mb-1"><?= $stk["stok_barang"] ?> Item</h5>
                </div>
              </div>
              <div class="separator-dashed"></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?= $this->endSection() ?>