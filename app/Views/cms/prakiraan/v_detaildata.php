<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Prakiraan</h4>
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
        <a href="<?php echo base_url('/dataprakiraan') ?>">Data Prakiraan</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#"><?= $title; ?></a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="card-title">Grafik Data <?= $dataprakiraan['nama_barang']; ?></div>
            <div class="ml-auto">
            </div>
            <a href="<?php echo base_url('/datapenjualan/update') ?>" type="button" class="btn btn-primary btn-round ml-2"><i class="fa flaticon-repeat"></i>  Perbarui Prakiraan</a>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="multipleLineChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h4 class="card-title">Perbandingan Stok</h4>
          </div>
        </div>
        <div class="card-body">
          <table id="list_pembelian" class="table table-hover">
            <thead>
              <tr>
                <th style="width: 1%; text-align:center; font-size: 16px;">No</th>
                <th style="width: 10%; text-align:center; font-size: 16px;">Tanggal/Bulan</th>
                <th style="width: 10%; text-align:center; font-size: 16px;">Prakiraan</th>
                <th style="width: 5%; text-align:center; font-size: 16px;">Status Stok</th>
                <th style="width: 25%; text-align:center; font-size: 16px;">Catatan</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              <?php foreach ($grafik as $dp) : ?>
                <tr>
                  <th scope="row" style="text-align:center; font-size: 16px;"><?= $no++ ?></th>
                  <td style="text-align:center; font-size: 16px;"><?= bulan_indonesia($dp["bulan"]); ?></td>
                  <td style="text-align:center; font-size: 16px;"><?= $dp["hasil_prakiraan"]; ?></td>
                  <td style="text-align:center; font-size: 16px;">
                    <?php
                    if ($dp['status'] == 'aman') {
                      echo '<span class="badge badge-success" style="text-align:center; font-size: 12px;">Aman</span>';
                    } elseif ($dp['status'] == 'cukup') {
                      echo '<span class="badge badge-warning" style="text-align:center; font-size: 12px;">Cukup</span>';
                    } else {
                      echo '<span class="badge badge-danger" style="text-align:center; font-size: 12px;">Kurang</span>';
                    }
                    ?>
                  </td>
                  <td style="text-align:center;">
                    <?php
                    if ($dp['selisih'] < 0) {
                      echo 'Stok Barang Ini Pada Bulan Ini Tidak Mencukupi, Atau Kurang Sebanyak ' . abs($dp['selisih']) . ' Items';
                    } elseif ($dp['selisih'] == 0) {
                      echo 'Stok Barang Ini Pada Bulan Ini Tidak Memiliki Sisi';
                    } else {
                      echo 'Stok Barang Ini Pada Bulan Ini Memiliki Sisa Sebanyak ' . abs($dp['selisih']) . ' Items';
                    }
                    ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php
    $bulan = "";
    $prakiraan = null;
    foreach ($grafik as $gf) {
      $tgl = bulan_indonesia($gf['bulan']);
      $bulan .= "'$tgl'" . ", ";
      $jum = $gf['hasil_prakiraan'];
      $prakiraan .= "$jum" . ", ";
    }
    ?>
    <?php
    $data_penjualan = null;
    foreach ($penjualan as $pjl) {
      $jum = $pjl['jumlah_barang'];
      $data_penjualan .= "$jum" . ", ";
    }
    ?>

  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section("content_js") ?>
<script>
  var multipleLineChart = document.getElementById('multipleLineChart').getContext('2d');

  var myMultipleLineChart = new Chart(multipleLineChart, {
    type: 'line',
    data: {
      labels: [<?= $bulan; ?>],
      datasets: [{
        label: "Prakiraan",
        borderColor: "#1d7af3",
        pointBorderColor: "#FFF",
        pointBackgroundColor: "#1d7af3",
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2,
        data: [<?= $prakiraan; ?>]
      }, {
        label: "Penjualan",
        borderColor: "#59d05d",
        pointBorderColor: "#FFF",
        pointBackgroundColor: "#59d05d",
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2,
        data: [<?= $data_penjualan; ?>]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'top',
      },
      tooltips: {
        bodySpacing: 4,
        mode: "nearest",
        intersect: 0,
        position: "nearest",
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10
      },
      layout: {
        padding: {
          left: 15,
          right: 15,
          top: 15,
          bottom: 15
        }
      }
    }
  });
</script>
<?= $this->endSection() ?>