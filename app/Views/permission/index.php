<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal_permission"><i class="fas fa-plus"></i></button>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                  <table id='permission_table' class='table table-bordered table-hover'>
                      <thead>
                          <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Description</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                  </table>
              </div>
          </div>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_permission" tabindex="-1" role="dialog" aria-labelledby="permission_modal_label"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permission_modal_label">Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='form_permission'>
                <div class="modal-body">
                    <input type='hidden' id='permission_id' name='permission_id'> 
                    <div class="form-group">
                        <label for="permission_name">Permission Name</label>
                        <input type="text" class="form-control" id="permission_name" name='permission_name' placeholder="Enter permission name">
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.</small> -->
                    </div>
                    <div class="form-group">
                        <label for="permission_description">Permission Description</label>
                        <input type="text" class="form-control" id="permission_description" name='permission_description' placeholder="Enter permission description">
                    </div>   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id='btn_save' onclick="save()">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>
    window.table = $('#permission_table').DataTable({
        "autoWidth": false,
        'responsive': true,
        'searching': false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': "<?= base_url('permissions/getall'); ?>",
        },
        "drawCallback": function(settings) {
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'name'
            },
            {
                data: 'description'
            },
            {
                data: 'action'
            }
        ],
        order:[[0, 'desc']],
        lengthChange: false,
        autoWidth: false,
        // dom: 'Blfrtip',
        // buttons: [
        //     'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        // ]
    });
    window.table.buttons().container().appendTo('#history_table_wrapper .col-md-6:eq(0)');


    /**
     * .
     */
    function update(id){
        if(id==undefined) return;

        $.ajax({
            method: "POST",
            url: "<?= base_url('permissions/getone'); ?>",
            data: {permission_id: id}
        }).done(function( msg ) {
            $('#permission_id').val(msg.id)
            $('#permission_name').val(msg.name);
            $('#permission_description').val(msg.description);
            $('#modal_permission').modal('show');
        });
    }

     /**
     * .
     */
    function remove(id){
        if(id==undefined) return;

        $.ajax({
            method: "POST",
            url: "<?= base_url('permissions/delete'); ?>",
            data: {permission_id: id}
        }).done(function( msg ) {
            Swal.fire({
                title: 'Delete permission',
                text: msg.messages,
                icon: msg.status ? 'success' : 'error',
                confirmButtonText: 'OK',
                didClose:function(){
                    window.table.draw();
                }
            });
        });
    }

    /**
     * .
     */
    function save(){
        let data={};
        for(let inp of $('#form_permission').serializeArray()){
            data[inp.name] = inp.value;
        }

        
        if(data.permission_id == ''){ 
            //add new permission
            $.ajax({
                method: "POST",
                url: "<?= base_url('permissions/add'); ?>",
                data: data
            }).done(function( msg ) {
                Swal.fire({
                    title: 'Add new permission',
                    text: msg.messages,
                    icon: msg.status ? 'success' : 'error',
                    confirmButtonText: 'OK',
                    didClose:function(){
                        window.table.draw();
                        $('#modal_permission').modal('hide');
                    }
                });

            });
        }else{
            //update existing permission
            $.ajax({
                method: "POST",
                url: "<?= base_url('permissions/update'); ?>",
                data: data
            }).done(function( msg ) {
                Swal.fire({
                    title: 'Update permission',
                    text: msg.messages,
                    icon: msg.status ? 'success' : 'error',
                    confirmButtonText: 'OK',
                    didClose:function(){
                        window.table.draw();
                        $('#modal_permission').modal('hide');
                    }
                });
            });
        }
        
    }
</script>
<?= $this->endSection(); ?>