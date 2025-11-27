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
            case 'operator-dashboard':
                require 'app/Controllers/operator/dashboard.php';
                $c = new OperatorDashboardController();
                $c->index();
                break;
            
            case 'operator-berita':
                require 'app/Controllers/operator/berita.php';
                $c = new OperatorBeritaController();
                $c->index();
                break;
            
            case 'operator-dokumentasi':
                require 'app/Controllers/operator/dokumentasi.php';
                $c = new OperatorDokumentasiController();
                $c->index();
                break;
            
            case 'operator-fasilitas':
                require 'app/Controllers/operator/fasilitas.php';
                $c = new OperatorFasilitasController();
                $c->index();
                break;
            
            case 'operator-proyek':
                require 'app/Controllers/operator/proyek.php';
                $c = new OperatorProyekController();
                $c->index();
                break;
            
            case 'operator-publikasi':
                require 'app/Controllers/operator/publikasi.php';
                $c = new OperatorPublikasiController();
                $c->index();
                break;
            

    default:
        require 'app/Controllers/public/home.php';
        break;
}
