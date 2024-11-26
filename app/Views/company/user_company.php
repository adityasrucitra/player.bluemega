<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 class="m-0">Users Of <?= $companyName ?> <?= "({$timezone})"; ?></h1>   
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <button type="button" onclick="addUser()" class="btn btn-primary float-right">Add User</button>
            </div>
            <div class="card-body">
              <table id="tbl_users" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                </thead>      
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="modalUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUserLabel">Select User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="select2users">Select User</label>
          <select class="form-control select2" id="select2users" style="width: 100%;">
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="addUser()" class="btn btn-primary" id="submitBtn">Submit</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>
   window.table = $('#tbl_users').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 700,
        ajax: {
          url: "<?= base_url('companies/getuserscompany'); ?>",
          type: 'POST',
          data: function (d){
            d.company_id = <?= $companyId ?>
          }
        }
    });

    $('#select2users').select2({
      ajax: {
        url: '<?=base_url('companies/userlist')?>',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        cache: true,
        data: function (params) {
            return {
                term: params.term,
                company_id: <?= $companyId ?>
            };
        },
      },
      placeholder: 'Select a user',
      dropdownParent: $('#modalUser')
    });

    $('#modalUser').on('hidden.bs.modal', function (e) {
        $('#select2users').val(null).trigger('change');      
    })
    
    /**
     * .
     */
    function remove(id) {
      if (id == undefined) return;


      Swal.fire({
        title: 'Remove company',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            method: "POST",
            url: "<?= base_url('companies/deleteusercompany'); ?>",
            data: {
              'id': id
            }
          }).done(function (msg) {
            if (msg.status == true) {
              Swal.fire('Deleted!', '', 'success');
              window.table.draw();
            }
          });
        } else if (result.isDenied) {
          Swal.fire('Delete canceled', '', 'info')
        }
      });      
    }

    /**
     * .
     */
    function addUser(){
        let selectedUser = $('#select2users').select2('data');
        let userId = undefined;
        if(selectedUser.length > 0){
            userId = selectedUser[0]['id'];
        }else{
            $('#modalUser').modal('show');
            return;
        }

        $.ajax({
            url: '<?=base_url('companies/addusercompany')?>',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: userId,
                company_id: <?= $companyId ?>
            },
            success: function (response) {
                if(response.status == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'Close'
                    }).then((result) => {
                      if (result.isConfirmed) {
                        $('#modalUser').modal('hide');  
                        window.table.draw();                           
                      }
                    });
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Add user fail!"
                }).then((result) => {
                  if (result.isConfirmed) {
                    $('#modalUser').modal('hide');  
                    window.table.draw();                           
                  }
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>