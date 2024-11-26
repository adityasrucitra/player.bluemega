<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 class="m-0">Add New Company</h1>   
  </section>

  <!-- Main content -->
  <section class="content">
      <form action='<?= base_url('companies/create'); ?>' method="post">

      <div class="container-fluid">
        <?= isset($action) && ($action == 'update') ? '<input type="hidden" id="company_id" name="company_id" value="'.$company_id.'">' : ''; ?>
          <div class="row">
              <div class="col-4">
                  <div class="form-group">
                      <label for="company_name">Company Name</label>
                      <input type="text" class="form-control <?= array_key_exists('company_name', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" id="company_name" name='company_name' 
                          placeholder="Enter company name" value="<?= isset($validation_errors) ? $company['company_name'] : ''; ?>">
                      <?= array_key_exists('company_name', $validation_errors) ? $validation_errors['company_name'] : ''; ?>  
                  </div>

                  <div class="form-group">
                      <label for="country">Country</label>
                      <input type="hidden" class="form-control <?= array_key_exists('country_id', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" id="country_id" name='country_id' 
                         placeholder="Enter country" value="<?= isset($validation_errors) ? $company['country_id'] : ''; ?>">
                      <input type="hidden" id="country_name" name='country_name' value="<?= isset($validation_errors) ? $company['country_name'] : ''; ?>">  
                      <select class="form-control" id='select2_countries'>
                        <?= isset($validation_errors) ? '<option value="'.$company['country_id'].'" selected="selected">'.$company['country_name'].'</option>' : ''; ?>
                      </select>
                      <?= array_key_exists('country_id', $validation_errors) ? $validation_errors['country_id'] : ''; ?>  
                  </div>

                  <div class="form-group">
                      <label for="state_id">State</label>
                      <input type="hidden" class="form-control <?= array_key_exists('state_id', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" id="state_id" name='state_id' 
                          placeholder="Enter state" value="<?= isset($validation_errors) ? $company['state_id'] : ''; ?>">
                      <input type="hidden" id="state_name" name='state_name' value="<?= isset($validation_errors) ? $company['state_name'] : ''; ?>">    
                      <select class="form-control" id='select2_states'>
                        <?= isset($validation_errors) ? '<option value="'.$company['state_id'].'" selected="selected">'.$company['state_name'].'</option>' : ''; ?>
                      </select>
                      <?= array_key_exists('state_id', $validation_errors) ? $validation_errors['state_id'] : ''; ?>  
                  </div>

                  <div class="form-group">
                      <label for="city">City</label>
                      <input type="hidden" class="form-control <?= array_key_exists('city_id', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>  " id="city_id" name='city_id' 
                          placeholder="Enter city" value="<?= isset($validation_errors) ? $company['city_id'] : ''; ?>">
                      <input type="hidden" id="city_name" name='city_name' value="<?= isset($validation_errors) ? $company['city_name'] : ''; ?>">  
                      <select class="form-control" id='select2_cities'>
                          <?= isset($validation_errors) ? '<option value="'.$company['city_id'].'" selected="selected">'.$company['city_name'].'</option>' : ''; ?>
                      </select>  
                      <?= array_key_exists('city_id', $validation_errors) ? $validation_errors['city_id'] : ''; ?>  
                    </div>

                  <div class="form-group">
                      <label for="phone_number">Phone Number</label>
                      <input type="text" class="form-control <?= array_key_exists('phone_number', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" id="phone_number" name='phone_number'
                          placeholder="Enter phone number" value="<?= isset($validation_errors) ? $company['phone_number'] : ''; ?>">
                      <?= array_key_exists('phone_number', $validation_errors) ? $validation_errors['phone_number'] : ''; ?>      
                  </div>

                  <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" class="form-control <?= array_key_exists('email', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" id="email" name='email' 
                          placeholder="Enter email" value="<?= isset($validation_errors) ? $company['email'] : ''; ?>">
                      <?= array_key_exists('email', $validation_errors) ? $validation_errors['email'] : ''; ?>  
                  </div>

                  <div class="form-group">
                      <label for="address">Address</label>
                      <textarea class="form-control <?= array_key_exists('address', $validation_errors) ? 'is-invalid' : 'is-valid'; ?>" rows='4' id="address" name='address' 
                          placeholder="Enter address"><?= isset($validation_errors) ? $company['address'] : ''; ?></textarea>
                      <?= array_key_exists('address', $validation_errors) ? $validation_errors['address'] : ''; ?>
                    </div>
              </div>
          </div>
          <div class="row">
            <div class="col-12">
                <button type='submit' class="btn btn-primary"> Save</button>
                <a href="<?= base_url('companies'); ?>" class="btn btn-danger"> Cancel</a>
            </div>
          </div>

        </form>

      </div>
  </section>

</div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>
    $('#select2_countries').select2({
        ajax: {
            url: '<?= base_url('companies/getcountries'); ?>',
            method: 'POST', 
            dataType: 'json'
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        }
    });
    $('#select2_countries').on('select2:select', function(e){
        $("#country_id").val(e.params.data.id);
        $("#country_name").val(e.params.data.text);
        $("#state_id").val('');
        $('#select2_states').val(null).trigger('change');
        $("#city_id").val('');
        $('#select2_cities').val(null).trigger('change');
    });

    $('#select2_states').select2({
        ajax: {
            url: '<?= base_url('companies/getstates'); ?>',
            method: 'POST', 
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term, // search term
                    countryId: $("#country_id").val(),
                };
            },
        }
    });
    $('#select2_states').on('select2:select', function(e){
        $("#state_id").val(e.params.data.id);
        $("#state_name").val(e.params.data.text);
        $("#city_id").val('');
        $('#select2_cities').val(null).trigger('change');
    });

    $('#select2_cities').select2({
        ajax: {
            url: '<?= base_url('companies/getcities'); ?>',
            method: 'POST', 
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term, // search term
                    countryId: $("#country_id").val(),
                    stateId: $("#state_id").val()
                };
            },
        }
    });
    $('#select2_cities').on('select2:select', function(e){
        $("#city_id").val(e.params.data.id);
        $("#city_name").val(e.params.data.text);
    });

    
    if ($('#company_id').length) {
        $.ajax({
            url: "<?= base_url('companies/getone'); ?>",
            method: "POST",
            data: {
                company_id: $('#company_id').val()
            },
            dataType: "json"
        }).done((msg) => {
            if(msg.status == true){
                $('#company_name').val(msg.company.company_name);

                $('#country_id').val(msg.company.country_id);
                newOption = new Option(msg.company.country_name, msg.company.country_id, false, false);
                $('#select2_countries').append(newOption).trigger('change');

                $('#state_id').val(msg.company.state_id);
                newOption = new Option(msg.company.state_name, msg.company.state_id, false, false);
                $('#select2_states').append(newOption).trigger('change');

                $('#city_id').val(msg.company.city_id);
                newOption = new Option(msg.company.city_name, msg.company.city_id, false, false);
                $('#select2_cities').append(newOption).trigger('change');

                $('#phone_number').val(msg.company.phone_number);
                $('#email').val(msg.company.email);

                $('#address').val(msg.company.address);
            }
        });
    }
   


</script>
<?= $this->endSection(); ?>