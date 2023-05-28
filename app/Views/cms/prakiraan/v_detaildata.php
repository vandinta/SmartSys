<?= $this->extend("cms/layout/v_template") ?>

<?= $this->section("title") ?>
	<title>Prediksi Penjualan - SmartSys</title>
<?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Data Prediksi Penjualan</h4>
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
        <a href="<?php echo base_url('/dataprakiraan') ?>">Data Prediksi Penjualan</a>
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
      <div class="card" style="height: 470px">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="card-title">Grafik 1 Prediksi Data <?= $dataprakiraan['nama_barang']; ?></div>
            <div class="ml-auto">
            </div>
            <a href="<?php echo base_url('/dataprakiraan/update') . '/' . $dataprakiraan['id_barang'];?>" type="button" class="btn btn-primary btn-round ml-2"><i class="fa flaticon-repeat"></i> Perbarui Prediksi</a>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container" style="height: 350px">
            <canvas id="multipleLineChart1"></canvas>
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="card-title">Grafik 2 Data <?= $dataprakiraan['nama_barang']; ?></div>
            <div class="ml-auto">
            </div>
            <a href="<?php echo base_url('/datapenjualan/update') ?>" type="button" class="btn btn-primary btn-round ml-2"><i class="fa flaticon-repeat"></i> Perbarui Prakiraan</a>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="multipleLineChart2"></canvas>
          </div>
        </div>
      </div>
    </div> -->
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
                <th style="width: 10%; text-align:center; font-size: 16px;">Bulan</th>
                <th style="width: 10%; text-align:center; font-size: 16px;">Prediksi</th>
                <th style="width: 5%; text-align:center; font-size: 16px;">Status Stok</th>
                <th style="width: 25%; text-align:center; font-size: 16px;">Catatan</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              <?php foreach ($perbandingan as $pb) : ?>
                <tr>
                  <th scope="row" style="text-align:center; font-size: 16px;"><?= $no++ ?></th>
                  <td style="text-align:center; font-size: 16px;"><?= bulan_indonesia($pb["bulan"]); ?></td>
                  <td style="text-align:center; font-size: 16px;"><?= $pb["hasil_prakiraan"]; ?></td>
                  <td style="text-align:center; font-size: 16px;">
                    <?php
                    if ($pb['status'] == 'aman') {
                      echo '<span class="badge badge-success" style="text-align:center; font-size: 12px;">Aman</span>';
                    } elseif ($pb['status'] == 'cukup') {
                      echo '<span class="badge badge-warning" style="text-align:center; font-size: 12px;">Cukup</span>';
                    } else {
                      echo '<span class="badge badge-danger" style="text-align:center; font-size: 12px;">Kurang</span>';
                    }
                    ?>
                  </td>
                  <td style="text-align:center;">
                    <?php
                    if ($pb['selisih'] < 0) {
                      echo 'Stok Barang Pada Bulan Ini Tidak Mencukupi, Atau Kurang Sebanyak ' . abs($pb['selisih']) . ' Items';
                    } elseif ($pb['selisih'] == 0) {
                      echo 'Stok Barang Pada Bulan Ini Tidak Memiliki Sisa';
                    } else {
                      echo 'Stok Barang Pada Bulan Ini Memiliki Sisa Sebanyak ' . abs($pb['selisih']) . ' Items';
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
  var multipleLineChart1 = document.getElementById('multipleLineChart1').getContext('2d');
  // var multipleLineChart2 = document.getElementById('multipleLineChart2').getContext('2d');

  var myMultipleLineChart1 = new Chart(multipleLineChart1, {
    type: 'bar',
    data: {
      labels: [<?= $bulan; ?>],
      datasets: [{
        label: "Penjualan",
        backgroundColor: "rgb(89, 208, 93, 0.7)",
        borderColor: "rgb(89, 208, 93, 0.7)",
        data: [<?= $data_penjualan; ?>]
      }, {
        label: "Penjualan",
        borderColor: "rgb(89, 208, 93, 0.7)",
        pointBorderColor: "#FFF",
        pointBackgroundColor: "rgb(89, 208, 93, 0.7)",
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2,
        data: [<?= $data_penjualan; ?>],
        type: 'line',
      }, {
        label: "Prediksi",
        backgroundColor: "rgb(0,106, 245, 0.7)",
        borderColor: "rgb(0,106, 245, 0.7)",
        data: [<?= $prakiraan; ?>],
      }, {
        label: "Prediksi",
        borderColor: "rgb(0,106, 245, 0.7)",
        pointBorderColor: "#FFF",
        pointBackgroundColor: "rgb(0,106, 245, 0.7)",
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: 'transparent',
        fill: true,
        borderWidth: 2,
        data: [<?= $prakiraan; ?>],
        type: 'line',
      }, ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      tooltips: {
        callbacks: {
          afterLabel: function(tooltipItem) {
            return 'Items';
          }
        },
      },
      scales: {
        xAxes: [{
          stacked: true,
          scaleLabel: {
            display: true,
            labelString: 'Bulan'
          }
        }],
        yAxes: [{
          ticks: {
            beginAtZero: true
          },
          stacked: true,
          scaleLabel: {
            display: true,
            labelString: 'Items'
          }
        }]
      },
    }
    // options: {
    //   responsive: true,
    //   maintainAspectRatio: false,
    //   legend: {
    //     position: 'top',
    //   },
    //   tooltips: {
    //     bodySpacing: 4,
    //     mode: "nearest",
    //     intersect: 0,
    //     position: "nearest",
    //     xPadding: 10,
    //     yPadding: 10,
    //     caretPadding: 10
    //   },
    //   layout: {
    //     padding: {
    //       left: 15,
    //       right: 15,
    //       top: 15,
    //       bottom: 15
    //     }
    //   }
    // }
  });
  // var myMultipleLineChart = new Chart(multipleLineChart, {
  //   type: 'line',
  //   data: {
  //     labels: [<?= $bulan; ?>],
  //     datasets: [{
  //       label: "Prakiraan",
  //       borderColor: "#1d7af3",
  //       pointBorderColor: "#FFF",
  //       pointBackgroundColor: "#1d7af3",
  //       pointBorderWidth: 2,
  //       pointHoverRadius: 4,
  //       pointHoverBorderWidth: 1,
  //       pointRadius: 4,
  //       backgroundColor: 'transparent',
  //       fill: true,
  //       borderWidth: 2,
  //       data: [<?= $prakiraan; ?>]
  //     }, {
  //       label: "Penjualan",
  //       borderColor: "#59d05d",
  //       pointBorderColor: "#FFF",
  //       pointBackgroundColor: "#59d05d",
  //       pointBorderWidth: 2,
  //       pointHoverRadius: 4,
  //       pointHoverBorderWidth: 1,
  //       pointRadius: 4,
  //       backgroundColor: 'transparent',
  //       fill: true,
  //       borderWidth: 2,
  //       data: [<?= $data_penjualan; ?>]
  //     }]
  //   },
  //   options: {
  //     responsive: true,
  //     maintainAspectRatio: false,
  //     legend: {
  //       position: 'top',
  //     },
  //     tooltips: {
  //       bodySpacing: 4,
  //       mode: "nearest",
  //       intersect: 0,
  //       position: "nearest",
  //       xPadding: 10,
  //       yPadding: 10,
  //       caretPadding: 10
  //     },
  //     layout: {
  //       padding: {
  //         left: 15,
  //         right: 15,
  //         top: 15,
  //         bottom: 15
  //       }
  //     }
  //   }
  // });

  // var myMultipleLineChart2 = new Chart(multipleLineChart2, {
  //   type: 'bar',
  //   data: {
  //     labels: [<?= $bulan; ?>],
  //     datasets: [{
  //       label: "Prakiraan",
  //       backgroundColor: 'rgb(23, 125, 255)',
  //       borderColor: "#1d7af3",
  //       data: [<?= $prakiraan; ?>]
  //     }, {
  //       label: "Prakiraan",
  //       borderColor: "#1d7af3",
  //       pointBorderColor: "#FFF",
  //       pointBackgroundColor: "#1d7af3",
  //       pointBorderWidth: 2,
  //       pointHoverRadius: 4,
  //       pointHoverBorderWidth: 1,
  //       pointRadius: 4,
  //       backgroundColor: 'transparent',
  //       fill: true,
  //       borderWidth: 2,
  //       data: [<?= $prakiraan; ?>],
  //       type: 'line',
  //     }, {
  //       label: "Penjualan",
  //       backgroundColor: 'rgb(0, 0, 0)',
  //       borderColor: "#59d05d",
  //       data: [<?= $data_penjualan; ?>]
  //     }, {
  //       label: "Penjualan",
  //       borderColor: 'rgb(0, 0, 0)',
  //       pointBorderColor: "#FFF",
  //       pointBackgroundColor: 'rgb(0, 0, 0)',
  //       pointBorderWidth: 2,
  //       pointHoverRadius: 4,
  //       pointHoverBorderWidth: 1,
  //       pointRadius: 4,
  //       backgroundColor: 'transparent',
  //       fill: true,
  //       borderWidth: 2,
  //       data: [<?= $data_penjualan; ?>],
  //       type: 'line',
  //     }]
  //   },
  //   options: {
  //     responsive: true,
  //     maintainAspectRatio: false,
  //     scales: {
  //       yAxes: [{
  //         ticks: {
  //           beginAtZero: true
  //         }
  //       }]
  //     },
  //   }
  //   // options: {
  //   //   responsive: true,
  //   //   maintainAspectRatio: false,
  //   //   legend: {
  //   //     position: 'top',
  //   //   },
  //   //   tooltips: {
  //   //     bodySpacing: 4,
  //   //     mode: "nearest",
  //   //     intersect: 0,
  //   //     position: "nearest",
  //   //     xPadding: 10,
  //   //     yPadding: 10,
  //   //     caretPadding: 10
  //   //   },
  //   //   layout: {
  //   //     padding: {
  //   //       left: 15,
  //   //       right: 15,
  //   //       top: 15,
  //   //       bottom: 15
  //   //     }
  //   //   }
  //   // }
  // });
  // // var myMultipleLineChart = new Chart(multipleLineChart, {
  // //   type: 'line',
  // //   data: {
  // //     labels: [<?= $bulan; ?>],
  // //     datasets: [{
  // //       label: "Prakiraan",
  // //       borderColor: "#1d7af3",
  // //       pointBorderColor: "#FFF",
  // //       pointBackgroundColor: "#1d7af3",
  // //       pointBorderWidth: 2,
  // //       pointHoverRadius: 4,
  // //       pointHoverBorderWidth: 1,
  // //       pointRadius: 4,
  // //       backgroundColor: 'transparent',
  // //       fill: true,
  // //       borderWidth: 2,
  // //       data: [<?= $prakiraan; ?>]
  // //     }, {
  // //       label: "Penjualan",
  // //       borderColor: "#59d05d",
  // //       pointBorderColor: "#FFF",
  // //       pointBackgroundColor: "#59d05d",
  // //       pointBorderWidth: 2,
  // //       pointHoverRadius: 4,
  // //       pointHoverBorderWidth: 1,
  // //       pointRadius: 4,
  // //       backgroundColor: 'transparent',
  // //       fill: true,
  // //       borderWidth: 2,
  // //       data: [<?= $data_penjualan; ?>]
  // //     }]
  // //   },
  // //   options: {
  // //     responsive: true,
  // //     maintainAspectRatio: false,
  // //     legend: {
  // //       position: 'top',
  // //     },
  // //     tooltips: {
  // //       bodySpacing: 4,
  // //       mode: "nearest",
  // //       intersect: 0,
  // //       position: "nearest",
  // //       xPadding: 10,
  // //       yPadding: 10,
  // //       caretPadding: 10
  // //     },
  // //     layout: {
  // //       padding: {
  // //         left: 15,
  // //         right: 15,
  // //         top: 15,
  // //         bottom: 15
  // //       }
  // //     }
  // //   }
  // // });
</script>
<?= $this->endSection() ?>