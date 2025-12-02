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
        
        case 'operator-berita-add':
            require 'app/Controllers/operator/berita_add.php';
            $c = new OperatorBeritaAddController();
            $c->index();
            break;
            
        case 'operator-berita-edit':
            require 'app/Controllers/operator/berita_edit.php';
            $c = new OperatorBeritaEditController();
            $c->index();
            break;
        
        case 'operator-berita-delete':
            require 'app/Controllers/operator/berita_delete.php';
            $c = new OperatorBeritaDeleteController();
            $c->index();
            break;
            
        case 'operator-dokumentasi':
            require 'app/Controllers/operator/dokumentasi.php';
            $c = new OperatorDokumentasiController();
            $c->index();
            break;
            
        case 'operator-dokumentasi-add':
            require 'app/Controllers/operator/dokumentasi_add.php';
            $c = new OperatorDokumentasiAddController();
            $c->index();
            break;
    
            case 'operator-publikasi':
                require 'app/Controllers/operator/publikasi.php';
                $c = new OperatorPublikasiController();
                $c->index();
                break;

                case 'admin-user':
        require 'app/Controllers/admin/user.php';
        $c = new UserController();
        $c->index();
        break;

    case 'admin-user-create':
        require 'app/Controllers/admin/user.php';
        $c = new UserController();
        $c->create();
        break;

   case 'admin-user-store':
    require 'app/Controllers/admin/user.php';
    $c = new UserController();
    $c->store($_POST); // ✅ kirim data form sebagai parameter
    break;


    case 'admin-user-edit':
    require 'app/Controllers/admin/user.php';
    $c = new UserController();
    $c->edit($_GET['id']);
    break;

    case 'admin-user-update':
        require 'app/Controllers/admin/user.php';
        $c = new UserController();
        $c->update($_POST);
        break;

    case 'admin-user-delete':
        require 'app/Controllers/admin/user.php';
        $c = new UserController();
        $c->delete($_GET['id']);
        break;
    
        
        case 'operator-dokumentasi-edit':
            require 'app/Controllers/operator/dokumentasi_edit.php';
            $c = new OperatorDokumentasiEditController();
            $c->index();
            break;
            
        case 'operator-dokumentasi-delete':
            require 'app/Controllers/operator/dokumentasi_delete.php';
            $c = new OperatorDokumentasiDeleteController();
            $c->index();
            break;
            
        case 'operator-fasilitas':
            require 'app/Controllers/operator/fasilitas.php';
            $c = new OperatorFasilitasController();
            $c->index();
            break;
        
        case 'operator-fasilitas-add':
            require 'app/Controllers/operator/fasilitas_add.php';
            $c = new OperatorFasilitasAddController();
            $c->index();
            break;
                
        case 'operator-fasilitas-edit':
            require 'app/Controllers/operator/fasilitas_edit.php';
            $c = new OperatorFasilitasEditController();
            $c->index();
            break;
                
        case 'operator-fasilitas-delete':
            require 'app/Controllers/operator/fasilitas_delete.php';
            $c = new OperatorFasilitasDeleteController();
            $c->index();
            break;
            
        case 'operator-proyek':
            require 'app/Controllers/operator/proyek.php';
            $c = new OperatorProyekController();
            $c->index();
            break;

        case 'operator-proyek-add':
            require 'app/Controllers/operator/proyek_add.php';
            $c = new OperatorProyekAddController();
            $c->index();
            break;
                        
        case 'operator-proyek-edit':
            require 'app/Controllers/operator/proyek_edit.php';
            $c = new OperatorProyekEditController();
            $c->index();
            break;
                        
        case 'operator-proyek-delete':
            require 'app/Controllers/operator/proyek_delete.php';
            $c = new OperatorProyekDeleteController();
            $c->index();
            break;
            
        case 'operator-publikasi':
            require 'app/Controllers/operator/publikasi.php';
            $c = new OperatorPublikasiController();
            $c->index();
            break;

        case 'operator-publikasi-add':
            require 'app/Controllers/operator/publikasi_add.php';
            $c = new OperatorPublikasiAddController();
            $c->index();
            break;
                    
        case 'operator-publikasi-edit':
            require 'app/Controllers/operator/publikasi_edit.php';
            $c = new OperatorPublikasiEditController();
            $c->index();
            break;
                    
        case 'operator-publikasi-delete':
            require 'app/Controllers/operator/publikasi_delete.php';
            $c = new OperatorPublikasiDeleteController();
            $c->index();
            break;
            
    default:
        require 'app/Controllers/public/home.php';
        break;
}
