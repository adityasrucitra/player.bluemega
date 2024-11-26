<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Registration Page (v2)</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="../../dist/css/adminlte.min.css?v=3.2.0">
    
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="<?= base_url(); ?>">
                    <img src="<?= base_url('asset/img/bm-logo.png'); ?>" alt="BM Logo" class="brand-image" style="max-width:100%; opacity: .8">
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new membership</p>

                <?= view('Myth\Auth\Views\_message_block'); ?>

                <form action="<?= url_to('register'); ?>" method="post">
                    <?= csrf_field(); ?>   

                    <div class="input-group mb-3">
                        <input type="email" class="form-control <?php if (session('errors.email')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.email'); ?>"
                            name="email" value="<?= old('email'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control <?php if (session('errors.username')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.username'); ?>"
                            name="username" value="<?= old('username'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.password'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="pass_confirm" class="form-control <?php if (session('errors.pass_confirm')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.repeatPassword'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="first_name" class="form-control <?php if (session('errors.first_name')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.firstName'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-spell-check"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="last_name" class="form-control <?php if (session('errors.last_name')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.lastName'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-spell-check"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="phone_number" class="form-control <?php if (session('errors.phone_number')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.phone'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="citizen_number" class="form-control <?php if (session('errors.citizen_number')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.citizenNumber'); ?>"
                            autocomplete="off" >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>

                    </div>
                </form>
                <a href="<?= url_to('login'); ?>" class="text-center"><?=lang('Auth.alreadyRegistered').lang('Auth.signIn'); ?></a>
            </div>

        </div>
    </div>


    <script src="../../plugins/jquery/jquery.min.js"></script>

    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
</body>

</html>
