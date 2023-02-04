<!-- Sidebar -->
<div class="sidebar sidebar-style-2">
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <div class="user">
        <div class="avatar-sm float-left mr-2">
          <img src="<?= base_url("assets/image/profile/" . $_SESSION['profile_picture']); ?>" alt="..." class="avatar-img rounded-circle">
        </div>
        <div class="info">
          <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
            <span>
              <?= $_SESSION['username']; ?>
              <span class="user-level"><?= $_SESSION['role']; ?></span>
            </span>
          </a>
          <div class="clearfix"></div>
        </div>
      </div>
      <ul class="nav nav-primary">
        <li class="nav-item <?= $menu == 'dashboard' ? 'active': '' ?>">
          <a href="<?= base_url("/") ?>">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'masterdata' ? 'active': '' ?>">
          <a data-toggle="collapse" href="#base">
            <i class="fas fa-folder"></i>
            <p>Master Data</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?= $menu == 'masterdata' ? 'show': '' ?>" id="base">
            <ul class="nav nav-collapse">
              <li class="nav-item <?= $submenu == 'datakategori' ? 'active': '' ?>">
                <a href="<?= base_url("/datakategori") ?>">
                  <i class="fas fa-cubes"></i>
                  <p>Data Kategori</p>
                </a>
              </li>
              <li class="nav-item <?= $submenu == 'databarang' ? 'active': '' ?>">
                <a href="<?= base_url("/databarang") ?>">
                  <i class="fas fa-archive"></i>
                  <p>Data Barang</p>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item <?= $menu == 'datapenjualan' ? 'active': '' ?>">
          <a href="<?= base_url("/datapenjualan") ?>">
            <i class="fas fa-shopping-basket"></i>
            <p>Data Penjualan</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'datamodel' ? 'active': '' ?>">
          <a href="#">
            <i class="fas fa-suitcase"></i>
            <p>Data Model</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'prakiraan' ? 'active': '' ?>" <?php if($_SESSION['role'] != "admin"){ echo "hidden";} ?>>
          <a href="#">
            <i class="fas fa-chart-line"></i>
            <p>Prakiraan</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'datausers' ? 'active': '' ?>" <?php if($_SESSION['role'] != "superadmin"){ echo "hidden";} ?>>
          <a href="<?= base_url("/datausers") ?>">
            <i class="fas fa-users"></i>
            <p>Data Users</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'karyawan' ? 'active': '' ?>" <?php if($_SESSION['role'] != "admin"){ echo "hidden";} ?>>
          <a href="<?= base_url("/karyawan") ?>">
            <i class="fas fa-users"></i>
            <p>Data Karyawan</p>
          </a>
        </li>
        <li class="nav-item <?= $menu == 'history' ? 'active': '' ?>">
          <a href="#">
            <i class="fas fa-history"></i>
            <p>History</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- End Sidebar -->