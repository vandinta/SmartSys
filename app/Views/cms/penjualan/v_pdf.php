<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title; ?></title>
  <style>
    #table {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      margin-left: auto;
      margin-right: auto;
      width: 100%;
    }

    #table td,
    #table th {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }

    #table tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    #table tr:hover {
      background-color: #ddd;
    }

    #table th {
      padding-top: 10px;
      padding-bottom: 10px;
      text-align: center;
      background-color: #4040ff;
      color: white;
    }
  </style>
</head>

<body>
  <div style="text-align:center">
    <h3><?= $title; ?></h3>
  </div>
  <table id="table">
    <thead>
      <tr>
        <th style="width: 8%">No</th>
        <th style="width: 25%">Tanggal Pembelian</th>
        <th style="width: 20%">Nama Barang</th>
        <th style="width: 8%">Jumlah</th>
        <th style="width: 20%">Harga Pembelian</th>
        <th style="width: 20%">Harga Penjualan</th>
        <th style="width: 20%">Jumlah Harga</th>
        <th style="width: 20%">Total Harga</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; ?>
      <?php foreach ($order as $odr) : ?>
        <tr>
          <td scope="row"><?= $no++ ?></td>
          <td>
            <?php
              $waktu = $odr["nama_penjualan"];
              $cleanwaktu = preg_replace("/Penjualan Pada /","", $waktu);
              echo $cleanwaktu;
            ?>
          </td>
          <td><?= $odr["nama_barang"] ?></td>
          <td><?= $odr["jumlah_barang"] ?></td>
          <td><?= rupiah($odr["harga_beli_barang"]) ?></td>
          <td><?= rupiah($odr["harga_jual_barang"]) ?></td>
          <td><?php $hitung = $odr["jumlah_barang"] * $odr["harga_jual_barang"]; echo rupiah($hitung); ?></td>
          <td><?= rupiah($odr["total_harga"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>