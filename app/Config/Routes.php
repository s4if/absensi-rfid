<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

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
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::doLogin');
$routes->get('logout', static function () {
    $session = \Config\Services::session();
    session_destroy();
    return redirect()->to('/login');
});
$routes->group('admin', ['filter' => 'loggedin'], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('beranda', 'Admin::index');

    // management Siswa
    $routes->get('siswa', 'Student::index');
    $routes->get('get_siswa', 'Student::list');
    $routes->get('get_siswa/(:num)', 'Student::show/$1');
    $routes->get('tambah_siswa', 'Student::new');
    $routes->post('tambah_siswa', 'Student::create');
    $routes->get('edit_siswa/(:num)', 'Student::edit/$1');
    $routes->post('edit_siswa/(:num)', 'Student::update/$1');
    $routes->get('hapus_siswa/(:num)', 'Student::delete/$1');

    // management rfid
    $routes->get('rfid', 'Rfid::showStudents');
    $routes->get('get_rfid', 'Rfid::getStudents');
    $routes->put('set_rfid', 'Rfid::setStudentRfid');
    $routes->delete('set_rfid', 'Rfid::setStudentRfid/hapus');

     // management Sesi
    $routes->get('sesi', 'Session::index');
    $routes->get('get_sesi', 'Session::list');
    $routes->get('get_sesi/(:num)', 'Session::show/$1');
    $routes->post('tambah_sesi', 'Session::create');
    $routes->put('edit_sesi/(:num)', 'Session::update/$1');
    $routes->delete('hapus_sesi/(:num)', 'Session::delete/$1');
});

//api call
$routes->get('rfid/check_in/(:any)', 'Rfid::readRfid/$1');
$routes->get('rfid/get_current', 'Rfid::getCurrent');
//$routes->get('rfid/session', 'Rfid::getSession');
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
