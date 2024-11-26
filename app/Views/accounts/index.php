  <?= $this->extend('layout'); ?>
  
  <?= $this->section('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Users <?= "({$timezone})" ?></h1>
            </div>
            <div class="col-sm-6">
              <a href="<?= base_url('accounts/create'); ?>" class='btn btn-success float-right'><i class="fas fa-plus"> <?= lang('App.new'); ?></i></a>              
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="card card-solid">
          <div class="card-body pb-0">
            <div class="row">
              <?php foreach ($users as $user): ?>

              <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">
                  <div class="card-header text-muted border-bottom-0">
                    <!-- Digital Strategist -->
                  </div>
                  <div class="card-body pt-0">
                    <div class="row">
                      <div class="col-7">
                        <h2 class="lead"><b><?= $user['first_name'].' '.$user['last_name']; ?></b></h2>
                        <!-- <p class="text-muted text-sm"><b>About: </b> Web Designer / UX / Graphic Artist / Coffee Lover
                        </p> -->
                        <ul class="ml-4 mb-0 fa-ul text-muted">
                          <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> 
                          <?= $user['city'].', '.$user['country']; ?> </li>
                          <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> 
                          <?= $user['phone_number']; ?></li>
                          <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> 
                          <?= $user['email']; ?></li>
                          <li class="small"><span class="fa-li"><i class="fas fa-lg fa-user"></i></span> 
                          <?php $isActive = $user['active'] == 1 ? 'Active' : 'Inactive'; ?></li>
                          <?php if ($isActive == 'Active'):?>
                            <li class="small text-success"><span class="fa-li"><i class="fas fa-lg fa-user"></i></span><?= $isActive; ?></li> 
                          <?php else:?>
                            <li class="small text-danger"><span class="fa-li"><i class="fas fa-lg fa-user"></i></span><?= $isActive; ?></li>  
                          <?php endif; ?> 
                          <li class="small text-danger"><span class="fa-li"><i class="fas fa-lg fa-user-lock"></i></span><?= 'Role: '.$user['group_name']; ?></li>
                          <li class="small text-info"><span class="fa-li"><i class="fas fa-user-clock"></i></span><?= 'Timezone: '.$user['timezone']; ?></li>  
                          <?php?>    
                        </ul>
                      </div>
                      <div class="col-5 text-center">
                        <img src="<?= !is_null($user['profile_image']) ? base_url($user['profile_image']) : base_url('dist/img/default-avatar.png'); ?>" alt="user-avatar" class="img-fluid">
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                      
                    <div class="row">
                      <div class="col text-left">
                        <a href="<?= base_url('profile/'.$user['user_id']); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                      </div>
                      <div class="col text-right">
                        <a href="<?= base_url('accounts/companies/'.$user['user_id']); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-building"></i> Companies
                        </a>
                      </div>
                    </div>
                     
                  </div>
                </div>
              </div>

              <?php endforeach; ?>
                        
            </div>
          </div>

        </div>

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?=  $this->endSection(); ?>
