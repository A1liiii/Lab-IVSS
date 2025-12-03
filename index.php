<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$routes = [

    // ============================
    // PUBLIC
    // ============================
    'home' => [
        'controller' => 'app/Controllers/public/home.php',
        'class' => 'HomePublicController',
        'method' => 'index'
    ],

    // ============================
    // ADMIN ROUTES
    // ============================
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

        'admin-user' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'index',
        'params' => []
    ],

    'admin-user-create' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'create',
        'params' => []
    ],

    'admin-user-store' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'store',
        'params' => ['post' => true]
    ],

    'admin-user-edit' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'edit',
        'params' => ['get' => 'id']
    ],

    'admin-user-update' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'update',
        'params' => ['post' => true]
    ],

    'admin-user-delete' => [
        'controller' => 'app/Controllers/admin/user.php',
        'class' => 'UserController',
        'method' => 'delete',
        'params' => ['get' => 'id']
    ],

    // ============================
    // OPERATOR DASHBOARD
    // ============================
    'operator-dashboard' => [
        'controller' => 'app/Controllers/operator/dashboard.php',
        'class' => 'OperatorDashboardController',
        'method' => 'index'
    ],

    // ============================
    // OPERATOR BERITA
    // ============================
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

    // ============================
    // OPERATOR DOKUMENTASI
    // ============================
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

    // ============================
    // OPERATOR FASILITAS
    // ============================
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

    // ============================
    // OPERATOR PROYEK
    // ============================
    'operator-proyek' => [
        'controller' => 'app/Controllers/operator/proyek.php',
        'class' => 'OperatorProyekController',
        'method' => 'index'
    ],

    'operator-proyek-add' => [
        'controller' => 'app/Controllers/operator/proyek_add.php',
        'class' => 'OperatorProyekAddController',
        'method' => 'index'
    ],

    'operator-proyek-edit' => [
        'controller' => 'app/Controllers/operator/proyek_edit.php',
        'class' => 'OperatorProyekEditController',
        'method' => 'index'
    ],

    'operator-proyek-delete' => [
        'controller' => 'app/Controllers/operator/proyek_delete.php',
        'class' => 'OperatorProyekDeleteController',
        'method' => 'index'
    ],

    // ============================
    // OPERATOR PUBLIKASI
    // ============================
    'operator-publikasi' => [
        'controller' => 'app/Controllers/operator/publikasi.php',
        'class' => 'OperatorPublikasiController',
        'method' => 'index'
    ],

    'operator-publikasi-add' => [
        'controller' => 'app/Controllers/operator/publikasi_add.php',
        'class' => 'OperatorPublikasiAddController',
        'method' => 'index'
    ],

    'operator-publikasi-edit' => [
        'controller' => 'app/Controllers/operator/publikasi_edit.php',
        'class' => 'OperatorPublikasiEditController',
        'method' => 'index'
    ],

    'operator-publikasi-delete' => [
        'controller' => 'app/Controllers/operator/publikasi_delete.php',
        'class' => 'OperatorPublikasiDeleteController',
        'method' => 'index'
    ],
];


// ================= ROUTER EXECUTOR ===================

if (!isset($routes[$page])) {
    require 'app/Controllers/public/home.php';
    $c = new HomePublicController();
    return $c->index();
}

$route = $routes[$page];

require_once $route['controller'];
$controller = new $route['class']();
$method = $route['method'];

if (!method_exists($controller, $method)) {
    die("Method $method tidak ditemukan di controller {$route['class']}");
}

return $controller->$method();
