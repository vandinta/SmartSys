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
        <th style="width: 7%">No</th>
        <th style="width: 20%">Nama Barang</th>
        <th style="width: 15%">Kategori</th>
        <th style="width: 10%">Stok</th>
        <th style="width: 15%">Harga Beli</th>
        <th style="width: 15%">Harga Jual</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; ?>
      <?php foreach ($barang as $brg) : ?>
        <tr>
          <td scope="row"><?= $no++ ?></td>
          <td><?= $brg["nama_barang"] ?></td>
          <td><?= $brg["nama_kategori"] ?></td>
          <td><?= $brg["stok_barang"] ?></td>
          <td><?= rupiah($brg["harga_beli"]) ?></td>
          <td><?= rupiah($brg["harga_jual"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>