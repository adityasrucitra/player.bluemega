<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 class="m-0"><?= $subTitle; ?></h1>   
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <a href='databases/create' type="button" class="btn btn-primary float-right">New</a>
            </div>
            <div class="card-body">
              <table id="tbl_databases" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Host</th>
                    <th>Port</th>
                    <th>Database</th>
                    <th>Username</th>
                    <th>Password</th>
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

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>
   window.table = $('#tbl_databases').DataTable({
        searching: false,  
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 700,
        ajax: {
          url: "<?= base_url('databases/getall'); ?>",
          type: 'POST'
        },
    });
    
    /**
     * .
     */
    function remove(id) {
      if (id == undefined) return;

      Swal.fire({
        title: 'Remove database',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            method: "POST",
            url: "<?= base_url('databases/delete'); ?>",
            data: {
              id: id
            }
          }).done(function (msg) {
            if (msg.status == true) {
              Swal.fire('Deleted!', '', 'success');
              window.table.draw();
            }else{
              Swal.fire(msg.errors[0], '', 'info');
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