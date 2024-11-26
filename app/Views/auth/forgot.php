<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Forgot Password (v2)</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="../../dist/css/adminlte.min.css?v=3.2.0">
   
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <h2 class='card-header'><?=lang('Auth.forgotPassword'); ?></h2>
            <div class="card-body">
                <?= view('Myth\Auth\Views\_message_block'); ?>
                <p class="login-box-msg"><?=lang('Auth.enterEmailForInstructions'); ?></p>
                <form action="<?= url_to('forgot'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="input-group mb-3">
                        <input type="email" name='email' class="form-control <?php if (session('errors.email')) : ?>is-invalid<?php endif; ?>" 
                            placeholder="<?=lang('Auth.emailAddress'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block"><?=lang('Auth.sendInstructions'); ?></button>
                        </div>

                    </div>
                </form>
                <p class="mt-3 mb-1">
                    <a href="<?= url_to('login'); ?>">Login</a>
                </p>
            </div>

        </div>
    </div>


    <script src="../../plugins/jquery/jquery.min.js"></script>

    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
</body>

</html>
