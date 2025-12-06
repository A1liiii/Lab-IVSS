<?php
$routes->get('/proyek', 'Proyek::index');
$routes->get('/proyek/tambah', 'Proyek::create');
$routes->post('/proyek/store', 'Proyek::store');
$routes->get('/proyek/detail/(:num)', 'Proyek::detail/$1');

$routes->get('/mahasiswa/dashboard', 'mahasiswa\Dashboard::index');
?>