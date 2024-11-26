<?php
$authorize = service('authorization');
$cache = \Config\Services::cache();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    
    <!-- Another style -->
    <?php foreach ($css as $style):?>
    <link rel="stylesheet" href="<?= base_url($style); ?>">
    <?php endforeach; ?>

    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">

</head>

<body class="hold-transition sidebar-mini sidebar-collapse">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>                
            </ul>
            <!-- Right navbar links -->
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url(); ?>" class="brand-link">
                <img src="../../dist/img/logo_imt_small.png" alt="IMT Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                    
                <span class="brand-text font-weight-light">IMT</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="<?= base_url('profile'); ?>" class="d-block"><?= user()->username; ?></a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="<?= base_url('logout'); ?>" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <p> Logout </p>
                            </a>
                        </li>
                        <li class='nav-item'>
                            <a href="#" class="nav-link">
                                <i class="fas fa-tasks"></i>
                                <p> Management <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview" style="display: block;">
                                <?php if ($authorize->hasPermission('management.account.view', intval(user()->id))): ?>                         
                                <li class="nav-item">
                                    <a href="<?= base_url('accounts'); ?>" class="nav-link">
                                        <i class="nav-icon fas fa-user-circle"></i>
                                        <p>Accounts</p>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if ($authorize->hasPermission('management.permission.view', intval(user()->id)) || $authorize->hasPermission('management.group.view', intval(user()->id))): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-key"></i>
                                        <p>Auth</p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php if ($authorize->hasPermission('management.permission.view', intval(user()->id))): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url('permissions'); ?>" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>Permissions</p>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if ($authorize->hasPermission('management.group.view', intval(user()->id))): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url('groups'); ?>" class="nav-link">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>Groups</p>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>  
                                <?php endif; ?>  
                            </ul>
                        </li>


                        <li class='nav-item'>
                            <a href="#" class="nav-link">
                                <i class="fas fa-tasks"></i>
                                <p> Tools <i class="right"></i></p>
                            </a>
                            <ul class="nav nav-treeview" style="display: block;">
                                <?php if ($authorize->hasPermission('tool.player.view', intval(user()->id))): ?>                         
                                <li class="nav-item">
                                    <a href="<?= base_url('player'); ?>" class="nav-link">
                                        <i class="nav-icon far fa-file-audio"></i>
                                        <p>Player</p>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>




                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- CONTENT HERE -->

        <?= $this->renderSection('content'); ?>       

        <!-- ./CONTENT HERE -->

        <?= $this->renderSection('popup'); ?>      

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright © 2023 <a href="https://indomegateknologi.com">PT. Indomega Teknologi</a>.</strong>
            All rights reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
   
    <!-- Another JS -->
    <?php foreach ($js as $j):?>
    <script src="<?= base_url($j); ?>"></script>
    <?php endforeach; ?>

     <!-- AdminLTE App -->
     <script src="../../dist/js/adminlte.min.js"></script>

     <!-- extra js -->
     <?= $this->renderSection('extra_js'); ?> 
     
     <script>
        if($('#fullscreenModal').length){
            $('#fullscreenModal').modal({backdrop: 'static', keyboard: false});
            $('#fullscreenModal').on('hidden.bs.modal', function (e) {
                $.ajax({
                    url: "<?= base_url('notifications/closepopup') ?>", // Replace with your server endpoint
                    type: 'POST',
                    data: { closePopup: true },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Success:', response);
                    }                    
                });            
            });
        }            
    </script>
    
</body>

</html>
