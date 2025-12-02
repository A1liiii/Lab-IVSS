<?php

$page = $_GET['page'] ?? 'home';

// daftar route
$routes = [

    // PUBLIC
    'home' => [
        'controller' => 'app/Controllers/public/home.php',
        'class' => 'HomePublicController',
        'method' => 'index'
    ],

    // ADMIN
    'admin-dashboard' => [
        'controller' => 'app/Controllers/admin/dashboard.php',
        'class' => 'DashboardController',
        'method' => 'index'
    ],

    'admin-logs' => [
        'controller' => 'app/Controllers/admin/logs.php',
        'class' => 'LogsController',
        'method' => 'index'
    ],

    'admin-approvals' => [
        'controller' => 'app/Controllers/admin/approvals.php',
        'class' => 'ApprovalsController',
        'method' => 'index'
    ],
    'admin-approvals-approve' => [
        'controller' => 'app/Controllers/admin/approvals.php',
        'class' => 'ApprovalsController',
        'method' => 'approve'
    ],

    // OPERATOR
    'operator-dashboard' => [
        'controller' => 'app/Controllers/operator/dashboard.php',
        'class' => 'OperatorDashboardController',
        'method' => 'index'
    ],

    'operator-berita' => [
        'controller' => 'app/Controllers/operator/berita.php',
        'class' => 'OperatorBeritaController',
        'method' => 'index'
    ],
    'operator-berita-add' => [
        'controller' => 'app/Controllers/operator/berita_add.php',
        'class' => 'OperatorBeritaAddController',
        'method' => 'index'
    ],
    'operator-berita-edit' => [
        'controller' => 'app/Controllers/operator/berita_edit.php',
        'class' => 'OperatorBeritaEditController',
        'method' => 'index'
    ],
    'operator-berita-delete' => [
        'controller' => 'app/Controllers/operator/berita_delete.php',
        'class' => 'OperatorBeritaDeleteController',
        'method' => 'index'
    ],

    // operator dokumentasi
    'operator-dokumentasi' => [
        'controller' => 'app/Controllers/operator/dokumentasi.php',
        'class' => 'OperatorDokumentasiController',
        'method' => 'index'
    ],
    'operator-dokumentasi-add' => [
        'controller' => 'app/Controllers/operator/dokumentasi_add.php',
        'class' => 'OperatorDokumentasiAddController',
        'method' => 'index'
    ],
    'operator-dokumentasi-edit' => [
        'controller' => 'app/Controllers/operator/dokumentasi_edit.php',
        'class' => 'OperatorDokumentasiEditController',
        'method' => 'index'
    ],
    'operator-dokumentasi-delete' => [
        'controller' => 'app/Controllers/operator/dokumentasi_delete.php',
        'class' => 'OperatorDokumentasiDeleteController',
        'method' => 'index'
    ],

    // operator fasilitas
    'operator-fasilitas' => [
        'controller' => 'app/Controllers/operator/fasilitas.php',
        'class' => 'OperatorFasilitasController',
        'method' => 'index'
    ],
    'operator-fasilitas-add' => [
        'controller' => 'app/Controllers/operator/fasilitas_add.php',
        'class' => 'OperatorFasilitasAddController',
        'method' => 'index'
    ],
    'operator-fasilitas-edit' => [
        'controller' => 'app/Controllers/operator/fasilitas_edit.php',
        'class' => 'OperatorFasilitasEditController',
        'method' => 'index'
    ],
    'operator-fasilitas-delete' => [
        'controller' => 'app/Controllers/operator/fasilitas_delete.php',
        'class' => 'OperatorFasilitasDeleteController',
        'method' => 'index'
    ],

    // operator proyek & publikasi
    'operator-proyek' => [
        'controller' => 'app/Controllers/operator/proyek.php',
        'class' => 'OperatorProyekController',
        'method' => 'index'
    ],
    'operator-publikasi' => [
        'controller' => 'app/Controllers/operator/publikasi.php',
        'class' => 'OperatorPublikasiController',
        'method' => 'index'
    ]
];


// ============= ROUTER HANDLER =============

// jika route tidak terdaftar → default: public home
if (!isset($routes[$page])) {
    require 'app/Controllers/public/home.php';
    $default = new HomePublicController();
    return $default->index();
}

$route = $routes[$page];

// load controller file
require_once $route['controller'];

// instantiate class
$controller = new $route['class']();

// panggil method
if (!method_exists($controller, $route['method'])) {
    die("Method '{$route['method']}' tidak ditemukan di controller {$route['class']}");
}

return $controller->{$route['method']}();
