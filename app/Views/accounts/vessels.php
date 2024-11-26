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
              <h3 class='float-left'><?= $user['first_name'] . ' ' . $user['last_name'] . ' Vessel List'; ?></h3>
              <a href='<?= base_url('accounts'); ?>' type="button" class="btn btn-primary float-right">Back to accounts</a>
              <input type="hidden" id='userId' value="<?= $user['user_id']; ?>">
              <input type="hidden" id='companyId' value="<?= $companyId; ?>">
            </div>
            <div class="card-body">
              <table id="tbl_vessels" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Vessel Name</th>
                    <th>Visible</th>
                  </tr>
                </thead>      
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>

   window.table = $('#tbl_vessels').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 700,
        ajax: {
          url: "<?= base_url('accounts/companies/vessels'); ?>",
          type: 'POST',
          data:{
            user_id: $('#userId').val(),
            company_id: $('#companyId').val(),
          }
        },
        drawCallback: function(settings, json) {
          $('.checkbox-vessel').change(function() {
            let checkboxData = {
              user_id: $(this).data('user-id'),          
              company_id: $(this).data('company-id'),        
              vessel_id: $(this).data('vessel-id'),
              visible: $(this).is(':checked')
            };

            $.ajax({
                url: '<?=  base_url('accounts/vessels/toggle'); ?>',   
                type: 'POST',
                data: checkboxData,
                success: function(response) {
                    if(response.status == true){
                      window.table.draw();
                      Swal.fire({
                          toast: true,
                          position: 'top-end', 
                          icon: 'success',
                          title: 'Vessel visibility successfully changed!',
                          showConfirmButton: false,
                          timer: 3000,
                          timerProgressBar: true,
                          didOpen: (toast) => {
                              toast.addEventListener('mouseenter', Swal.stopTimer)
                              toast.addEventListener('mouseleave', Swal.resumeTimer)
                          }
                      });
                    }
                }
            });
          });
        }
    });   

</script>
<?= $this->endSection(); ?>