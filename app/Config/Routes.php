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

$routes->group('/datakategori', static function ($routes) {
    $routes->get("", "KategoriController::index");
    $routes->get("tambah", "KategoriController::create");
    $routes->get("ubah/(:num)", "KategoriController::edit/$1");
    $routes->post("input", "KategoriController::save");
    $routes->post("edit/(:num)", "KategoriController::update/$1");
    $routes->delete("(:num)", "KategoriController::delete/$1");
});

$routes->group('/databarang', static function ($routes) {
    $routes->get("", "BarangController::index");
    $routes->get("tambah", "BarangController::create");
    $routes->get("ubah/(:num)", "BarangController::edit/$1");
    $routes->post("input", "BarangController::save");
    $routes->post("edit/(:num)", "BarangController::update/$1");
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
    $routes->post("input", "PenjualanController::save");
    $routes->post("edit/(:num)", "PenjualanController::update/$1");
    $routes->delete("(:num)", "PenjualanController::delete/$1");
});

$routes->get("/datamodel", "AuthController::index");
$routes->get("/prakiraan", "AuthController::index");
$routes->get("/history", "AuthController::index");

$routes->get("/setting/(:segment)", "AuthController::edit/$1");
$routes->get("/karyawan", "AuthController::listKaryawan");
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
