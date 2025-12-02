<?php
require_once 'app/models/log.php';

class LogsController {

    public function index() {

        $logModel = new Log();
        $logs = $logModel->getAll();

        $active = 'logs';
        $title = "Admin Logs";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/admin_sidebar.php';
        require 'app/Views/Admin/logs.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
