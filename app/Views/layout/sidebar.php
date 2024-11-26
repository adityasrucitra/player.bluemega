      <!-- Main Sidebar Container -->
      <!--<aside class="main-sidebar sidebar-bg-dark sidebar-color-primary shadow">-->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= base_url() ?>" class="brand-link">
          <img src="../../dist/img/logo_imt_small.png" alt="IMT Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
          <span class="brand-text font-weight-light">IMT</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <!-- <div class="image">
                        <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div> -->
            <div class="info">
              <a href="<?= base_url('profile') ?>" class="d-block"><?= user()->username ?></a>
            </div>
          </div>

          <!-- SidebarSearch Form -->
          <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar">
                  <i class="fas fa-search fa-fw"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <!-- Add icons to the links using the .nav-icon class
                            with font-awesome or any other icon font library -->
              <li class="nav-item menu-open">
                <a href="<?= base_url('logout') ?>" class="nav-link">
                  <i class="fas fa-sign-out-alt"></i>
                  <p> Logout </p>
                </a>
              </li>
              <li class='nav-item'>
                <a href="#" class="nav-link">
                  <i class="fas fa-tasks"></i>
                  <p>
                    Management
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: block;">
                  <li class="nav-item">
                    <a href="<?= base_url('vessels') ?>" class="nav-link">
                      <i class="nav-icon fas fa-ship"></i>
                      <p>Vessels</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('accounts') ?>" class="nav-link">
                      <i class="nav-icon fas fa-user-circle"></i>
                      <p>Accounts</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('emsquotas') ?>" class="nav-link">
                      <i class="nav-icon fas fa-compress-alt"></i>
                      <p>Quota</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class='nav-item'>
                <a href="#" class="nav-link">
                  <i class="fas fa-tasks"></i>
                  <p>
                    Log
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: block;">
                  <li class="nav-item">
                    <a href="<?= base_url('filling') ?>" class="nav-link">
                      <i class="nav-icon fas fa-gas-pump"></i>
                      <p>Filling</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('tracking') ?>" class="nav-link">
                      <i class="nav-icon fas fa-route"></i>
                      <p>Tracking</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>