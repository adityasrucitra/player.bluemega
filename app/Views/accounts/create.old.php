<?= $this->extend('layout'); ?>

  <?= $this->section('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <!-- <h1>Create New User</h1> -->
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Blank Page</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <form action='create' method='post'>
          <div class="container-fluid">          
            <div class="row">
              <div class="col-md-6">
                <div class="card card-primary">
                  <div class="card-header">
                    <h1 class="card-title">Add New Account</h1>
                  </div>
                  <form action='create' method='post'>
                    <div class="card-body">
                      <div class="form-group">
                        <label for="input_username">Username</label>
                        <input type="text" class="form-control <?= isset($error['username']) ? 'is-invalid' : 'is-valid'; ?>" id="input_username" name='username' placeholder="Enter username"
                            value="<?= $old['username']; ?>">
                        <?php if (isset($error['username'])): ?>
                          <div class="invalid-feedback"><?= $error['username']; ?></div>
                        <?php endif; ?>  
                      </div>
                      <div class="form-group">
                        <label for="input_email">Email address</label>
                        <input type="email" class="form-control <?= isset($error['email']) ? 'is-invalid' : 'is-valid'; ?>" id="input_email" name='email' placeholder="Enter email"
                            value="<?= $old['email']; ?>">
                        <?php if (isset($error['email'])): ?>
                          <div class="invalid-feedback"><?= $error['email']; ?></div>
                        <?php endif; ?>      
                      </div>
                      <div class="form-group">
                        <label for="iput_password">Password</label>
                        <input type="password" class="form-control <?= isset($error['password']) ? 'is-invalid' : 'is-valid'; ?>" id="input_passsword" name='password' placeholder="Password"
                            value="<?= $old['password']; ?>">
                        <?php if (isset($error['password'])): ?>
                          <div class="invalid-feedback"><?= $error['password']; ?></div>
                        <?php endif; ?> 
                      </div>
                      <div class="form-group">
                        <label for="input_password_confirm">Re-type Password</label>
                        <input type="password" class="form-control <?=isset($error['pass_confirm']) ? 'is-invalid' : 'is-valid'; ?>" id="input_password_confirm" name='pass_confirm' placeholder="Password"
                            value="<?= $old['pass_confirm']; ?>">
                        <?php if (isset($error['pass_confirm'])): ?>
                          <div class="invalid-feedback"><?= $error['pass_confirm']; ?></div>
                        <?php endif; ?> 
                      </div>
                      <div class="form-group">
                        <label for="input_citizen_number">Citizen Number</label>
                        <input type="text" class="form-control <?= isset($error['citizen_number']) ? 'is-invalid' : 'is-valid'; ?>" id="input_citizen_number" name='citizen_number' placeholder="Citizen ID"
                            value="<?= $old['citizen_number']; ?>">
                        <?php if (isset($error['citizen_number'])): ?>
                          <div class="invalid-feedback"><?= $error['citizen_number']; ?></div>
                        <?php endif; ?>     
                      </div> 
                      <div class="form-group">
                        <input type='hidden' name='timezone' id='timezone'>
                        <label for="sel_timezone">Timezones</label>
                        <select id='sel_timezones'>
                          <option value='0'>- Search Timezones -</option>
                        </select>
                      </div>                    
                    </div>                  
                </div>
              </div> 
              
              <div class="col-md-6">
                <div class="card card-primary">
                  <div class="card-header">
                    <h1 class="card-title">Add New Account</h1>
                  </div>
                  <form action='create' method='post'>
                    <div class="card-body">
                      <div class="form-group">
                        <label for="input_username">First Name</label>
                        <input type="text" class="form-control <?= isset($error['first_name']) ? 'is-invalid' : 'is-valid'; ?>" id="input_first_name" name='first_name' placeholder="Enter first name"
                            value="<?= $old['first_name']; ?>">
                        <?php if (isset($error['first_name'])): ?>
                          <div class="invalid-feedback"><?= $error['first_name']; ?></div>
                        <?php endif; ?>  
                      </div>
                      <div class="form-group">
                        <label for="input_last_name">Last Name</label>
                        <input type="text" class="form-control <?= isset($error['last_name']) ? 'is-invalid' : 'is-valid'; ?>" id="input_last_name" name='last_name' placeholder="Enter last name"
                            value="<?= $old['last_name']; ?>">
                        <?php if (isset($error['last_name'])): ?>
                          <div class="invalid-feedback"><?= $error['last_name']; ?></div>
                        <?php endif; ?>      
                      </div>
                      <div class="form-group">
                        <label for="input_country">Country</label>
                        <select id="select2_countries" style="width: 100%"></select>

                        <!-- <input type="text" class="form-control <?= ''//isset($error['country']) ? 'is-invalid' : 'is-valid';?>" id="input_country" name='country' placeholder="Country"
                            value="<?= ''//$old['country'];?>">
                        <?php //if (isset($error['country'])):?>
                          <div class="invalid-feedback"><?= ''//$error['country'];?></div>
                        <?php //endif;?>  -->
                      </div>
                      <div class="form-group">
                        <label for="input_city">City</label>
                        <select id="select2_cities" style="width: 100%"></select>

                        <!-- <input type="text" class="form-control <?= ''//isset($error['city']) ? 'is-invalid' : 'is-valid';?>" id="input_city" name='city' placeholder="City"
                            value="<?= ''//$old['city'];?>">
                        <?php //if (isset($error['city'])):?>
                          <div class="invalid-feedback"><?= ''//$error['city'];?></div>
                        <?php //endif;?>  -->
                      </div>                      
                      <div class="form-group">
                        <label for="input_phone_number">Phone Number</label>
                        <input type="text" class="form-control <?= isset($error['phone_number']) ? 'is-invalid' : 'is-valid'; ?>" id="input_phone_number" name='phone_number' placeholder="Phone number"
                            value="<?= $old['phone_number']; ?>">
                        <?php if (isset($error['phone_number'])): ?>
                          <div class="invalid-feedback"><?= $error['phone_number']; ?></div>
                        <?php endif; ?>     
                      </div>
                      <div class="form-group">
                        <input type='hidden' name='input_role' id='input_role'>
                        <label for="sel_roles">Group</label>
                        <select id='sel_roles'>
                          <option value='0'>- Search role -</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="active" class='mr-3'>Status</label>
                        <input type="checkbox" id='active' name="active">                                   
                      </div>                        
                    </div>                  
                </div>
              </div>              
            </div>  
            
            <div class="row">
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <a href = "<?= base_url('accounts/'); ?>" class="btn btn-primary float-right ml-2" >Cancel</a>
                <button type="submit" class="btn btn-primary float-right">Submit</button>
              </div>
            </div>

          </div>
          </form>
        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?=  $this->endSection(); ?>



<?= $this->section('extra_js'); ?>
<script>
    //bootstrap switch checkbox
    $("[name='active']").bootstrapSwitch({
      onText: 'Enable',
      offText: 'Disable',
    });

    $("#sel_roles").select2({
    ajax: {
      url: "<?= base_url('accounts/getroles'); ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }
  });
  $('#sel_roles').on('select2:select', function (e) {
    var data = e.params.data;

    $('#input_role').val(data.id);
  });

  $('#select2_countries').select2({
    ajax: {
      url: '<?= base_url('accounts/getcountries') ?>',
      dataType: 'json',
      placeholder: 'Select a country',
      minimumInputLength: 1,
      type: 'POST',
      dataType: 'json'
    }
  });

  $('#select2_cities').select2({
    ajax: {
      url: '<?= base_url('accounts/getcities') ?>',
      dataType: 'json',
      placeholder: 'Select a city',
      minimumInputLength: 1,
      type: 'POST',
      dataType: 'json',
      data : (e) => {
        let cId = null;
        let el = $('#select2_countries').select2('data');
        if(el.length > 0){
          cId = el[0]['id']
        }
        return {
          term: e.searchTerm,
          countryId : cId
        }
      }
    }
  });
    

  $("#sel_timezones").select2({
    ajax: {
      url: "<?= base_url('profile/gettimezones'); ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          term: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }
  });

  $('#sel_timezones').on('select2:select', function (e) {
    var data = e.params.data;

    $('#timezone').val(data.text);
  });
</script>  
<?=  $this->endSection(); ?>
