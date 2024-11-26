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
            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal_group"><i class="fas fa-plus"></i></button>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                  <table id='group_table' class='table table-bordered table-hover'>
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
<div class="modal fade" id="modal_group" tabindex="-1" role="dialog" aria-labelledby="group_modal_label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="group_modal_label">Groups</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='form_group'>
                <div class="modal-body">
                    <input type='hidden' id='group_id' name='group_id'> 
                    <div class="form-group">
                        <label for="permission_name">Group Name</label>
                        <input type="text" class="form-control" id="group_name" name='group_name' placeholder="Enter group name">
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.</small> -->
                    </div>
                    <div class="form-group">
                        <label for="group_description">Group Description</label>
                        <input type="text" class="form-control" id="group_description" name='group_description' placeholder="Enter group description">
                    </div>
                    <div class="form-group">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Permission</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody id='tbody_permissions'>
                                <!-- <tr>
                                    <td>1.</td>
                                    <td>Update software</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                                        </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
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
    window.table = $('#group_table').DataTable({
        "autoWidth": false,
        'responsive': true,
        'searching': false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': "<?= base_url('groups/getall'); ?>",
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
        order:[[1, 'ASC']],
        lengthChange: false,
        autoWidth: false,
        // dom: 'Blfrtip',
        // buttons: [
        //     'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
        // ]
    });
    window.table.buttons().container().appendTo('#group_table_wrapper .col-md-6:eq(0)');


    $('#modal_group').on('shown.bs.modal', function (e) {
        data={};
        if($('#group_id').val() !=''){
            data.group_id = $('#group_id').val();
        }
        $('#tbody_permissions').empty();
        $.ajax({
            method: "POST",
            url: "<?= base_url('groups/getpermissions'); ?>",
            data: data
        }).done(function (msg) {
            let template = `<tr>
                                <td>@order</td>
                                <td>@permission_name</td>
                                <td><input @checked type="checkbox" class="" name='checkbox_permissions[]' value=@permission_id></td>
                            </tr>`;
            $('#tbody_permissions').empty();
            let order = 1;                
            for(let cb of msg){
                let t = template;
                t = t.replace('@order', order);
                t = t.replace('@permission_name', cb.name);
                t = t.replace('@permission_id', cb.id);
                let checked = '';
                if(cb.inGroup){
                    checked = 'checked';
                }
                t = t.replace('@checked', checked);                
                $('#tbody_permissions').append(t);
                order++;
            }
        });
    });

    $('#modal_group').on('hidden.bs.modal', function (e) {
        $('#group_id').val('');
        $("#form_group").trigger("reset");
    });

    /**
     * .
     */
    function update(id){
        if(id==undefined) return;

        $.ajax({
            method: "POST",
            url: "<?= base_url('groups/getone'); ?>",
            data: {group_id: id}
        }).done(function( msg ) {
            $('#group_id').val(msg.id)
            $('#group_name').val(msg.name);
            $('#group_description').val(msg.description);
            $('#modal_group').modal('show');
        });
    }

     /**
     * .
     */
    function remove(id){
        if(id==undefined) return;

        $.ajax({
            method: "POST",
            url: "<?= base_url('groups/delete'); ?>",
            data: {group_id: id}
        }).done(function( msg ) {
            Swal.fire({
                title: 'Remove group',
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
        for(let inp of $('#form_group').serializeArray()){
            if(inp.name=='checkbox_permissions[]') continue;
            data[inp.name] = inp.value;
        }
        data.permission_ids = [];
        let searchIDs = $("#tbody_permissions input:checkbox:checked").map(function(){
            return $(this).val();
        });
        for(let pId of searchIDs.get()){
            data.permission_ids.push(pId)
        }

        if(data.group_id == ''){ 
            //add new permission
            // $("#form_group").trigger("reset");
            $.ajax({
                method: "POST",
                url: "<?= base_url('groups/add'); ?>",
                data: data
            }).done(function( msg ) {
                Swal.fire({
                    title: 'Add new group',
                    text: msg.messages,
                    icon: msg.status ? 'success' : 'error',
                    confirmButtonText: 'OK',
                    didClose:function(){
                        window.table.draw();
                        $('#modal_group').modal('hide');
                    }
                });
            });
        }else{
            //update existing permission
            $.ajax({
                method: "POST",
                url: "<?= base_url('groups/update'); ?>",
                data: data
            }).done(function( msg ) {
                Swal.fire({
                    title: 'Update group',
                    text: msg.messages,
                    icon: msg.status ? 'success' : 'error',
                    confirmButtonText: 'OK',
                    didClose:function(){
                        window.table.draw();
                        $('#modal_group').modal('hide');
                    }
                });             
            });
        }
        
    }
</script>
<?= $this->endSection(); ?>