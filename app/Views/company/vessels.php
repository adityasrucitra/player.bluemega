<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class='float-left'><?= $company['company_name']." ({$timezone})"; ?></h3>
              <a href='<?= base_url('companies'); ?>' type="button" class="btn btn-primary float-right">Back to companies</a>
            </div>
            <div class="card-body">
              <input type='hidden' id='companyId' value='<?= $company['id']; ?>'>  
              <table id="tbl_contract" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Vessel Name</th>
                    <th>IMO</th>
                    <th>MMSI</th>
                    <th>Contract Start</th>
                    <th>Contract End</th>
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

   window.table = $('#tbl_contract').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searchDelay: 700,
        ajax: {
          url: "<?= base_url('companies/getvessels'); ?>",
          type: 'POST',
          data:{
            company_id: $('#companyId').val()
          }
        }
    });
</script>
<?= $this->endSection(); ?>