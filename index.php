<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {

    case 'admin-dashboard':
        require 'app/Controllers/admin/dashboard.php';
        $c = new DashboardController();
        $c->index();
        break;
    
        case 'admin-logs':
            require 'app/Controllers/admin/logs.php';
            $c = new LogsController();
            $c->index();
            break;        
    
    default:
        require 'app/Controllers/public/home.php';
        break;
}
