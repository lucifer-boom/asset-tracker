<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard | CA Asset Management</title>
    <script src="https://kit.fontawesome.com/e5c508369c.js" crossorigin="anonymous"></script>
    <!-- Custom fonts for this template-->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">


    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/css/dashboard-min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">

<!-- select 2 css -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">




</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">

                <div class="sidebar-brand-text mx-3">CA Sri Lanka</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
    <a class="nav-link" href="<?= base_url('/dashboard') ?>">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>


            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
          
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa-solid fa-computer"></i>
                    <span>Assets</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Assets:</h6>
                         <?php if(has_role('super admin') || has_role('it admin')): ?>
                        <a class="collapse-item" href="<?= base_url('assets/manage') ?>">Assets Creation</a>
                        <a class="collapse-item" href="<?=base_url('assets/assignments') ?>">Assets Assign & Return</a>
                        <!-- <a class="collapse-item" href="<?=base_url('assets/movements') ?>">Assets Transfers & <br>Disposals</a> -->
                        <a class="collapse-item" href="<?=base_url('/assets/assets_transfers') ?>">Assets Tranfers</a>
                        <a class="collapse-item" href="<?=base_url('assets/maintenance') ?>">Assets Maintenance</a>
                        <a class="collapse-item" href="<?=base_url('assets/qr') ?>">Assets QR Codes</a>
                        <?php endif; ?>
                        <?php if(has_role('dashboard viewer')): ?>
                    <a class="collapse-item" href="<?=base_url('/asset-transfer/pending') ?>">Asset Transfer Approval</a>
                    <a class="collapse-item" href="<?=base_url('/assets/asset-transfer/received') ?>">Asset Transfer Received</a>
                    <?php endif; ?> 
                        
                    </div>
                   
                </div>
                
            </li>
            <!-- <li class="nav-item">
                 <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLicense"
                    aria-expanded="true" aria-controls="collapseLicense">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Software Licensing</span>
                </a>
                <div id="collapseLicense" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Software License:</h6>
                         <?php if(has_role('super admin') || has_role('it admin')): ?>
                        <a class="collapse-item" href="<?= base_url('') ?>">New License Purchase</a>
                        <a class="collapse-item" href="<?=base_url('') ?>">License Assgins</a>
                        <a class="collapse-item" href="<?=base_url('') ?>">Due and Renew Dates</a>
                        <?php endif; ?>
                    </div>
                   
                </div>
            </li> -->

            <!-- Nav Item - Utilities Collapse Menu -->
             <?php if(has_role('super admin')|| has_role('it admin')): ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Admin Panel</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?=base_url('assets/suppliers') ?>">Suppliers Creation</a>
                    <a class="collapse-item" href="<?=base_url('assets/categories') ?>">Asset Main Categories<br> Creation</a>
                    <a class="collapse-item" href="<?=base_url('assets/sub_categories') ?>">Asset Sub Categories<br> Creation</a>
                    <a class="collapse-item" href="<?=base_url('assets/models') ?>">Asset Models Creation</a>
                    <?php if(has_role('super admin')): ?>
                    <a class="collapse-item" href="<?=base_url('auth/users') ?>">User Manage</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- Divider -->
            <hr class="sidebar-divider">

          <li class="nav-item">
                <a class="nav-link" href="">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Reports</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

          

        </ul>