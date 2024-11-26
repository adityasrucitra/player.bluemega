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
        <form action='<?= base_url('databases/create'); ?>' method="post">

            <div class="container-fluid">
                <?= isset($action) && ($action == 'update') ? '<input type="hidden" id="id" name="id" value="'.$database['id'].'">' : ''; ?>
                <div class="row">
                    <div class="col-4">
                       
                        <div class="form-group">
                            <label for="host">Host</label>
                            <input type="text" class="form-control" id="host" name="host" placeholder="Enter host" value="<?= old('host'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="port">Port</label>
                            <input type="text" class="form-control" id="port" name="port" placeholder="Enter port" value="<?= old('port'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="database">Database</label>
                            <input type="text" class="form-control" id="database" name='database' placeholder="Enter database" value="<?= old('database'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name='username' placeholder="Enter username" value="<?= old('username'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" id="password" name='password' placeholder="Enter password" value="<?= old('password'); ?>">
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type='submit' class="btn btn-primary"> Save</button>
                        <a href="<?= base_url('databases'); ?>" class="btn btn-danger"> Cancel</a>
                    </div>
                </div>

        </form>
</div>
</section>

</div>

<?= $this->endSection(); ?>


<?= $this->section('extra_js'); ?>
<script>       
    if ($('#id').length) {
        $.ajax({
            url: "<?= base_url('databases/getone'); ?>",
            method: "POST",
            data: {
                id: $('#id').val()
            },
            dataType: "json"
        }).done((msg) => {
            if(msg.status == true){            
                $("#host").val(msg.database.host);
                $("#port").val(msg.database.port);
                $("#database").val(msg.database.database_name);
                $("#username").val(msg.database.username);
                $("#password").val(msg.database.password);
            }
        });
    }
 
</script>
<?= $this->endSection(); ?>