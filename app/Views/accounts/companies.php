<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <!-- <h1 class="m-0"><?= $subTitle; ?></h1>    -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class='float-left'><?= $user['first_name'].' '.$user['last_name'].' Company List'; ?></h3>
              <a href='<?= base_url('accounts'); ?>' type="button" class="btn btn-primary float-right">Back to accounts</a>
              <input type="hidden" id='userId' value="<?= $user['user_id']; ?>">
              <button onclick="showModal()" type="button" class="btn btn-primary float-right mr-2"><i class="fas fa-plus"></i> New</a>
            </div>
            <div class="card-body">
              <input type='hidden' id='userId' value='<?= $user['user_id']; ?>'>  
              <table id="tbl_companies" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Address</th>
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

  <!-- Modal -->
  <div class="modal fade" id="modalAddCompany" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Add Company</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-6">
                <form>
                  <div class="form-group">
                    <label for="companySelect" class="col-form-label">Company:</label>
                    <input id='companyId' type="hidden" value="">
                    <select class="form-control" id="companySelect">
                      <option value=0>Select Companies</option>
                    </select>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" onclick="addCompany()" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>

   window.table = $('#tbl_companies').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 700,
        ajax: {
          url: "<?= base_url('accounts/getallcompanies'); ?>",
          type: 'POST',
          data:{
            user_id: $('#userId').val()
          }
        }
    });



    /**
     * .
     */
    function showModal() {
      $('#companySelect').select2({
        dropdownParent: $('#modalAddCompany'),
        ajax: {
          url: '<?= base_url('accounts/companies/getcompanies'); ?>',
          type: 'POST',
          data: function (params) {
            var query = {
              term: params.term,
              user_id: $('#userId').val()
            }
            return query;
          }
        }
      });

      $('#companySelect').on('select2:select', function(e){
        $("#companyId").val(e.params.data.id);
      });

      $('#modalAddCompany').modal({
        show: true
      });
    }

    /**
     * .
     */
    function addCompany() {
      $.ajax({
          method: "POST",
          url: "<?= base_url('accounts/companies/addcompany'); ?>",
          data: {
            user_id: $('#userId').val(),
            company_id: $('#companyId').val()
          }
        })
        .done(function (msg) {
          if (msg.status == true) {
            Swal.fire('New company added!', '', 'success');
            window.table.draw();
            $('#modalAddCompany').modal('hide');
          }                      
        });
    }


     /**
     * .
     */
    function remove(id) {
      if (id == undefined) return;


      Swal.fire({
        title: 'Remove companies',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            method: "POST",
            url: "<?= base_url('accounts/companies/delete'); ?>",
            data: {
              uc_id: id
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

</script>
<?= $this->endSection(); ?>