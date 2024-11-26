<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Profile </h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
             <!-- <form> -->
            <?= form_open_multipart('profile/update', ['id' => 'form_profile']); ?>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="card card-primary">
                           
                            <input type="hidden" class="form-control" id="targetUrl" value='<?= $targetUrl; ?>'>
                            <input type="hidden" class="form-control" id="profileId" name='profile_id' data-base-url="<?= base_url(); ?>">
                            <input type="hidden" class="form-control" id="userId" name='user_id'>
                            <input type="hidden" class="form-control" id="username" name='username'>
                            <input type="hidden" class="form-control" id="email" name='email'>
                            <div class="card-body">
                                <div class="form-group">
                                    <!-- <label for="inputVesselImage">User Image</label>  -->
                                    <input type="file" size="20" class="form-control-file" id="inputProfileImage" placeholder="User image" name='profile_image'
                                        onchange="showPreview(event);" >
                                    <img class="img-thumbnail mx-auto d-block" alt="Photo" id='profileThumbnail' style="display:none">    
                                </div>
                                <div class="form-group">
                                    <label for="inputFirstName">First Name</label>
                                    <input type="text" class="form-control" id="inputFirstName"
                                        name="first_name"
                                        placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label for="inputLastName">Last Name</label>
                                    <input type="text" class="form-control" id="inputLastName"
                                        name="last_name"
                                        placeholder="Enter last name">
                                </div>                                
                                <div class="form-group">
                                    <label for="country">Country</label>    
                                    <input type="hidden" name="country_id" id="country_id">                               
                                    <select class="form-control <?= session()->get('validation_errors.country_id') ? 'is-invalid' : '' ?>" id='select2_countries'></select>
                                    <?php if (session()->get('validation_errors.country_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->get('validation_errors.country_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="state">State</label>    
                                    <input type="hidden" name="state_id" id="state_id">                               
                                    <select class="form-control <?= session()->get('validation_errors.state_id') ? 'is-invalid' : '' ?>" id='select2_states'></select>
                                    <?php if (session()->get('validation_errors.state_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->get('validation_errors.state_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="country">City</label>   
                                    <input type="hidden" name="city_id" id="city_id">                                
                                    <select class="form-control <?= session()->get('validation_errors.city_id') ? 'is-invalid' : '' ?>" id='select2_cities'></select>
                                    <?php if (session()->get('validation_errors.city_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->get('validation_errors.city_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="inputPhoneNumber">Phone Number</label>
                                    <input type="text" class="form-control" id="inputPhoneNumber"
                                        name="phone_number"
                                        placeholder="Enter phone number">
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword1">Password</label>
                                    <input type="password" class="form-control" id="inputPassword1"
                                        name="password1"
                                        placeholder="Enter password">
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword2">Re-type Password</label>
                                    <input type="password" class="form-control" id="inputPassword2"
                                        name="password2"
                                        placeholder="Re-enter password">
                                </div>
                                <div class="form-group">
                                    <label for="input_timezone">Timezone</label>
                                    <input type="hidden" id="input_timezone" name="timezone">
                                    <select id='sel_timezones' style="width: 50%">
                                        <option value='0'>- Search timezone -</option>
                                    </select>
                                </div>
                                <?php if ($authorize->hasPermission('management.account.edit', user()->id)): ?>
                                <div class="form-group">
                                    <input type='hidden' name='input_role' id='input_role'>
                                    <label for="sel_roles">Group</label>
                                    <select id='sel_roles' style="width: 50%">
                                        <option value='0'>- Search role -</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <input type="checkbox" id='active' name="active">                                   
                                </div>  
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-group" id='qr_code'></div>
                            <div class="form-group">
                                <label for="sm_telegram"><i class="fab fa-telegram-plane"></i> Telegram</label>                                
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input type="text" class="form-control" id="sm_telegram" name="sm_telegram" placeholder="Enter telegram username">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        </div> 
                    </div>       
                </div>
            </div>
            </form>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>

<!-- Popup Page -->
<?= $this->section('popup'); ?>
<?= isset($popup) ? $popup : ''; ?>
<?= $this->endSection(); ?>

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
    $('#sel_roles').on('select2:select', function (e) {
        var data = e.params.data;

        $('#input_role').val(data.id);
    });

    $("#select2_countries").select2({
        ajax: {
            url: "<?= base_url('profile/getcountries'); ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            cache: true
        }
    });
    $('#select2_countries').on('select2:select', function (e) {
        var data = e.params.data;
        $('#country_id').val(data.id);

        $('#select2_states').val(null).trigger('change');
        $('#state_id').val('');

        $('#select2_cities').val(null).trigger('change');
        $('#city_id').val('');
    });

    $("#select2_states").select2({
        ajax: {
            url: "<?= base_url('profile/getstates'); ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                let el = $('#select2_countries').select2('data');
                let cId = null;
                if(el.length > 0){
                    cId = el[0]['id']
                }
                return {
                    term: params.term,
                    countryId : cId
                };
            },
            cache: true
        }
    });
    $('#select2_states').on('select2:select', function (e) {
        var data = e.params.data;
        $('#state_id').val(data.id);

        $('#select2_cities').val(null).trigger('change');
        $('#city_id').val('');
    });

    $("#select2_cities").select2({        
        ajax: {
            url: "<?= base_url('profile/getcities'); ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                let el = $('#select2_states').select2('data');
                let sId = null;
                if(el.length > 0){
                    sId = el[0]['id']
                }
                return {
                    term: params.term,
                    stateId : sId
                };
            },
            cache: true
        }
    });
    $('#select2_cities').on('select2:select', function (e) {
        var data = e.params.data;
        $('#city_id').val(data.id);
    });

    $("#sel_timezones").select2({
        ajax: {
            url: "<?= base_url('profile/gettimezones'); ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            cache: true
        }
    });

    $('#sel_timezones').on('select2:select', function (e) {
        var data = e.params.data;

        $('#input_timezone').val(data.text);
    });
    
</script>  
<?=  $this->endSection(); ?>