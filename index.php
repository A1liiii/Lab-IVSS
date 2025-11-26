<?php

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {

    case 'admin-dashboard':
        require 'app/Controllers/admin/dashboard.php';
        $c = new DashboardController();
        $c->index();
        break;

    case 'admin-approvals':
        require 'app/Controllers/admin/approvals.php';
        $c = new ApprovalsController();
        $c->index();
        break;

    case 'admin-approvals-approve':
        require 'app/Controllers/admin/Approvals.php';
        $c = new ApprovalsController();
        $c->approve($_GET['id']);
        break;

    default:
        require 'app/Controllers/public/home.php';
        break;
}
