<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 class="m-0">Companies <?= "({$timezone})"; ?></h1>   
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <a href='companies/create' type="button" class="btn btn-primary float-right">New</a>
            </div>
            <div class="card-body">
              <table id="tbl_companies" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Company name</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                    <th>Phone number</th>
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
          url: "<?= base_url('companies/getall'); ?>",
          type: 'POST'
        }
    });
    
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
            url: "<?= base_url('companies/delete'); ?>",
            data: {
              company_id: id
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