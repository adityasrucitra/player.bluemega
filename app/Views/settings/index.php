<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Settings</h3>  
        </div>
        <div class="col-sm-6">
          <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#modal_setting"><i class="fas fa-plus"></i></button>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
   <div class="container-fluid">
    <div class="row">
        <div class="col lg-12">
        <table id="table_settings" class="table table-bordered table-hover dataTable dtr-inline" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Setting Name</th>
                <th>Value</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
        </div>    
    </div>
   </div> 
  </section>
</div>

<!-- MODAL -->

<div id="modal_setting" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id='form_setting'>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label for="setting_name">Setting Name</label>
              <input type="text" class="form-control" id="setting_name" name="setting_name" placeholder="Setting name ...">
            </div>
            <div class="form-group">
              <label for="value">Value</label>
              <input type="text" class="form-control" id="value" name="value" placeholder="Value ...">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="save()">Save changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>



<?= $this->endSection(); ?>
<!-- /.content -->


<!-- page script -->
<?= $this->section('extra_js'); ?>
<script>

    window.tableSettings = $('#table_settings').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 1000,
        ajax:{
            url: '<?= 'settings/findall'; ?>',
            type: 'POST'
        } 
    });

    /**
     * .
     */
    function save(){    
        let form = $('#form_setting').serializeArray();
        let data={};
        for(let n of form){
          data[n.name] = n.value;
        }
        
        $.ajax({
          method: "POST",
          url: "<?= base_url('settings/add'); ?>",
          data: data
        }).done(function( msg ) {
            Swal.fire({
              toast: false,
              position: 'top-end',
              icon: msg.status ? 'success' : 'error',
              title: msg.message,
              showConfirmButton: false,
              timer: 1500
            }).then(function() {
              window.tableSettings.draw();
              $('#modal_setting').modal('hide'); 
            })                      
        });
    }

    /**
     * .
     */
    function edit(id){
      $.ajax({
          method: "POST",
          url: "<?= base_url('settings/findone'); ?>",
          data: {setting_id: id}
        }).done(function( msg ) {
            $("#form_setting").trigger("reset");
            if ($("#setting_id").length){
              $("#setting_id").remove()              
            }
            $('#form_setting').prepend(
              `<input type="hidden" id="setting_id" name="setting_id" value=${msg.setting.id}>`
            );
            $('#setting_name').val(msg.setting.setting_name);     
            $('#value').val(msg.setting.value);   
            $('#modal_setting').modal('show');
        });
    }

    /**
     * .
     */
    function remove(id){
      $.ajax({
          method: "POST",
          url: "<?= base_url('settings/delete'); ?>",
          data: {setting_id: id}
        }).done(function( msg ) {

            

            if(msg.status){
              window.tableSettings.draw();
            }

            Swal.fire({
              toast: false,
              position: 'top-end',
              icon: msg.status ? 'success' : 'error',
              title: msg.message,
              showConfirmButton: false,
              timer: 1500
            }).then(function() {
              window.tableSettings.draw();
              $('#modal_setting').modal('hide'); 
              if(msg.status){
                window.tableSettings.draw();
              }
            });   
        });
    }

</script>
<?= $this->endSection(); ?>
