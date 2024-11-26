<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="../../dist/css/adminlte.min.css?v=3.2.0">
    
</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="<?= base_url(); ?>">
                    <img src="<?= base_url('asset/img/bm-logo.png'); ?>" alt="BM Logo" class="brand-image" style="max-width:100%; opacity: .8">
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="<?= url_to('login'); ?>" method="post">
                    <?= csrf_field(); ?>
                    
                    <?php if ($config->validFields === ['email']): ?>
                    <div class="input-group mb-3">
                        <input type="email" name="login"  class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.email'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <div class="invalid-feedback">
                            <?= session('errors.login'); ?>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="input-group mb-3">
                        <input type="text" name="login" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.emailOrUsername'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <div class="invalid-feedback">
							<?= session('errors.login'); ?>
                        </div>
                    </div>
                    <?php endif;
                    ?>    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control <?php if (session('errors.password')) : ?>is-invalid<?php endif; ?>" placeholder="<?=lang('Auth.password'); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <div class="invalid-feedback">
							<?= session('errors.password'); ?>
                        </div>
                    </div>
                    <?php if ($config->allowRemembering): ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')) : ?> checked <?php endif; ?>>
                            <?=lang('Auth.rememberMe'); ?>
                        </label>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>

                <?php if ($config->activeResetter): ?>
					<!-- <p class="mb-1"><a href="<?= ''//url_to('forgot');?>"><?= ''//lang('Auth.forgotYourPassword'); ?></a></p> -->
                <?php endif; ?>
                <?php if ($config->allowRegistration) : ?>
					<!-- <p class="mb-1"><a href="<?= ''//url_to('register'); ?>"><?=''//lang('Auth.needAnAccount'); ?></a></p> -->
                <?php endif; ?>
            </div>

        </div>

    </div>


    <script src="../../plugins/jquery/jquery.min.js"></script>

    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
</body>

</html>
