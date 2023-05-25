<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Ubah - SmartSys</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicon -->
  <link href="dashmin/img/favicon.ico" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="dashmin/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="dashmin/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="dashmin/css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="dashmin/css/style.css" rel="stylesheet">
</head>

<body>
  <div class="container-xxl position-relative bg-white d-flex p-0">
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <!-- Spinner End -->

    <!-- Form Start -->
    <div class="container-fluid">
      <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
          <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
            <?php if (session()->getFlashdata('berhasil') !== NULL) : ?>
              <div class="alert alert-success" role="alert">
                <?php echo session()->getFlashdata('berhasil'); ?>
              </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error') !== NULL) : ?>
              <div class="alert alert-danger" role="alert">
                <?php echo session()->getFlashdata('error'); ?>
              </div>
            <?php endif; ?>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h3 class="text-primary">SmartSys</h3>
              <h5><?= $title; ?></h5>
            </div>
            <form action="/reset/<?= $user['user_id']; ?>" method="post">
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="passwordbaru" name="passwordbaru" placeholder="password" required>
                <label for="passwordbaru">Kata Sandi Baru</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="konfirmasipassword" name="konfirmasipassword" placeholder="password" required>
                <label for="konfirmasipassword">Konfirmasi Kata Sandi</label>
              </div>
              <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Simpan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Sign In End -->
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="dashmin/lib/chart/chart.min.js"></script>
  <script src="dashmin/lib/easing/easing.min.js"></script>
  <script src="dashmin/lib/waypoints/waypoints.min.js"></script>
  <script src="dashmin/lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="dashmin/lib/tempusdominus/js/moment.min.js"></script>
  <script src="dashmin/lib/tempusdominus/js/moment-timezone.min.js"></script>
  <script src="dashmin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

  <!-- Template Javascript -->
  <script src="dashmin/js/main.js"></script>
</body>

</html>