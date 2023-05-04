<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Home::index');

// dahboard
$routes->get("/", "AuthController::index");

// proses login
$routes->post("/login", "AuthController::login");
$routes->get("/logout", "AuthController::logout");
$routes->get("/viewgetemail", "AuthController::viewgetEmail");
$routes->post("/getemail", "AuthController::getEmail");
$routes->get("/aktivasi", "AuthController::aktivasiUser");

$routes->group('/datakategori', static function ($routes) {
    $routes->get("", "KategoriController::index");
    $routes->get("tambah", "KategoriController::create");
    $routes->get("ubah/(:num)", "KategoriController::edit/$1");
    $routes->get("exportexcel", "KategoriController::exportExcel");
    $routes->get("exportcsv", "KategoriController::exportCsv");
    $routes->get("exportpdf", "KategoriController::exportPdf");
    $routes->post("input", "KategoriController::save");
    $routes->post("edit/(:num)", "KategoriController::update/$1");
    $routes->post("import", "KategoriController::import");
    $routes->delete("(:num)", "KategoriController::delete/$1");
});

$routes->group('/databarang', static function ($routes) {
    $routes->get("", "BarangController::index");
    $routes->get("tambah", "BarangController::create");
    $routes->get("ubah/(:num)", "BarangController::edit/$1");
    $routes->get("exportexcel", "BarangController::exportExcel");
    $routes->get("exportcsv", "BarangController::exportCsv");
    $routes->get("exportpdf", "BarangController::exportPdf");
    $routes->post("input", "BarangController::save");
    $routes->post("edit/(:num)", "BarangController::update/$1");
    $routes->post("import", "BarangController::import");
    $routes->delete("(:num)", "BarangController::delete/$1");
});

$routes->group('/datausers', static function ($routes) {
    $routes->get("", "AuthController::listUsers");
    $routes->get("tambah", "AuthController::create");
    $routes->get("ubah/(:num)", "AuthController::ubah/$1");
    $routes->post("input", "AuthController::save");
    $routes->post("edit/(:num)", "AuthController::ganti/$1");
    $routes->delete("(:num)", "AuthController::delete/$1");
});

$routes->group('/datapenjualan', static function ($routes) {
    $routes->get("", "PenjualanController::index");
    $routes->get("tambah", "PenjualanController::create");
    $routes->get("ubah/(:num)", "PenjualanController::edit/$1");
    $routes->get("exportexcel", "PenjualanController::exportExcel");
    $routes->get("exportcsv", "PenjualanController::exportCsv");
    $routes->get("exportpdf", "PenjualanController::exportPdf");
    $routes->post("input", "PenjualanController::save");
    $routes->post("input_cart", "PenjualanController::input_cart");
    $routes->post("input_order", "PenjualanController::input_order");
    // $routes->post("edit/(:num)", "PenjualanController::update/$1");
    $routes->post("ubah_penjualan/(:num)", "PenjualanController::update_penjualan/$1");
    $routes->post("ubah_order", "PenjualanController::update_order");
    $routes->post("import", "PenjualanController::import");
    $routes->delete("(:num)", "PenjualanController::delete/$1");
    $routes->delete("delete_cart/(:num)", "PenjualanController::delete_cart/$1");
    $routes->delete("delete_order/(:num)", "PenjualanController::delete_order/$1");
});

$routes->group('/datamodel', static function ($routes) {
    $routes->get("", "MLController::index");
    $routes->get("tambah", "MLController::create");
    $routes->get("create", "MLController::model");
    $routes->match(['get', 'post'], "create", "MLController::model");
});

$routes->get("/prakiraan", "AuthController::index");
$routes->get("/history", "AuthController::index");

$routes->get("/ubahpassword/(:segment)", "AuthController::ubah/$1");
$routes->get("/setting/(:segment)", "AuthController::edit/$1");
$routes->get("/karyawan", "AuthController::listKaryawan");
$routes->post("/ubahpassword/(:num)", "AuthController::ganti/$1");
$routes->post("/setting/edit/(:num)", "AuthController::update/$1");
// $routes->get("/pages/index", "Pages::index");
// $routes->get("/pages/about", "Pages::about");
// $routes->get("/pages/contact", "Pages::contact");
// $routes->get("/berita", "Berita::index");
// $routes->get("/berita/index", "Berita::index");
// $routes->get("/berita/create", "Berita::create");
// $routes->get("/berita/edit/(:segment)", "Berita::edit/$1");
// $routes->get("/berita/(:any)", "Berita::detail/$1");

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
