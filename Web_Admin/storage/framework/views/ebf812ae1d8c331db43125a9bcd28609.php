<!doctype html>
<html lang="<?php echo e(app()->getLocale()); ?>" <?php if(app()->getLocale() === 'ar'): ?> dir="rtl" <?php endif; ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fav icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('web/favIcon.png?v=2')); ?>">
    <!-- custome css -->
    <link rel="stylesheet" href="<?php echo e(asset('web/css/login.css')); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('web/css/bootstrap.css')); ?>">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(asset('web/css/rtl.css?v=8')); ?>" type="text/css">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <title><?php echo e(__('Log In')); ?></title>
</head>
<style>
    .terms {
        font-size: 0.675rem;
        margin-top: 0.5rem;
    }

    .terms a {
        margin: 0 4px;
        text-decoration: none;
    }

    .terms a:hover {
        text-decoration: underline;
    }

    .demo-credential-card {
        border: 1px solid rgba(72, 123, 233, 0.18);
        background: #f8fbff;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 18px;
    }

    .demo-credential-title {
        font-size: 0.875rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .demo-credential-text {
        font-size: 0.875rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .demo-credential-fill {
        width: 42px;
        height: 42px;
        border: 1px solid rgba(72, 123, 233, 0.25);
        border-radius: 10px;
        background: #fff;
        color: var(--theme-color) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s ease-in-out;
    }

    .demo-credential-fill:hover {
        background: var(--theme-color);
        color: #fff !important;
    }
</style>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-7 col-lg-6 login-form-section">
                <div class="login">
                    <?php
                        $showDemoCredentials = config('app.show_demo_credentials')
                            && app()->environment(['local', 'demo'])
                            && !app()->environment('production')
                            && filled(config('app.demo_admin_email'))
                            && filled(config('app.demo_admin_password'));
                        $demoMode = config('services.demo_mode');
                    ?>

                    <form role="form" class="pui-form" id="loginform" method="POST" action="<?php echo e(route('login.submit', ['redirect' => $redirect ?? null])); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="header text-center">
                            <?php
                                $websetting = App\Models\WebSetting::first() ?? '';

                            ?>
                            
                            <img src="<?php echo e($websetting->websiteLogoPath ?? asset('web/logo.png?v=3')); ?>" alt="not found"
                                height="120">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['error'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <?php echo e($message); ?> 79789
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <h3><?php echo e(__('Admin Login')); ?></h3>
                            <p><?php echo e(__('This is a secure system and you will need to provide your login details to access the site')); ?></p>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('password')): ?>
                            <div class="bg-danger p-2 mb-1">
                                <span style="color: #fff"><?php echo e(session('password')); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="inputBox">
                            <input type="text" id="email" name="email"
                                class="form-control inputfield <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Phone_number')); ?>" inputmode="numeric" autocomplete="username">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="inputBox">
                            <div class="input w-100 position-relative">
                                <input type="password" id="password" name="password"
                                    class="form-control inputfield <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="<?php echo e(__('Password')); ?>">
                                <span class="eye" onclick="showHidePassword()">
                                    <i class="fas fa-eye-slash fa-eye" id="togglePassword"></i>
                                </span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <button type="submit" class="btn btncustom w-100"><?php echo e(__('Login')); ?></button>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($demoMode): ?>
                            <div class="demo-credential-card d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <h6 class="demo-credential-title"><?php echo e(__('Demo Admin Credentials')); ?></h6>
                                    <p class="demo-credential-text mb-0">
                                        <?php echo e(__('Email')); ?>: <?php echo e(config('app.demo_admin_email')); ?><br>
                                        <?php echo e(__('Password')); ?>: <?php echo e(config('app.demo_admin_password')); ?>

                                    </p>
                                </div>

                                <button
                                    class="demo-credential-fill flex-shrink-0"
                                    id="demoAdminLoginTrigger"
                                    type="button"
                                    title="<?php echo e(__('Fill and login')); ?>"
                                    aria-label="<?php echo e(__('Fill demo admin credentials and login')); ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </form>
                </div>

            </div>

            <div class="col-12 col-md-6 d-none d-md-block"
                style="background: url(<?php echo e(asset('web/bg/login.jpg?v=2')); ?>);overflow: hidden;
            background-size: cover;
            background-position: center;">
            </div>
        </div>
    </div>

    <script>
        function showHidePassword() {
            const toggle = document.getElementById("togglePassword");
            const password = document.getElementById("password");

            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the icon
            toggle.classList.toggle("fa-eye-slash");
        }

        const demoAdminEmail = <?php echo json_encode(config('app.demo_admin_email'), 15, 512) ?>;
        const demoAdminPassword = <?php echo json_encode(config('app.demo_admin_password'), 15, 512) ?>;
        const demoAdminLoginTrigger = document.getElementById("demoAdminLoginTrigger");

        if (demoAdminLoginTrigger) {
            demoAdminLoginTrigger.addEventListener("click", function() {
                const form = document.getElementById("loginform");
                const email = document.getElementById("email");
                const password = document.getElementById("password");

                if (!form || !email || !password) {
                    return;
                }

                email.value = demoAdminEmail || "";
                password.value = demoAdminPassword || "";

                email.dispatchEvent(new Event("input", {
                    bubbles: true
                }));
                password.dispatchEvent(new Event("input", {
                    bubbles: true
                }));
                email.dispatchEvent(new Event("change", {
                    bubbles: true
                }));
                password.dispatchEvent(new Event("change", {
                    bubbles: true
                }));

                // if (typeof form.requestSubmit === "function") {
                //     form.requestSubmit();
                //     return;
                // }

                // form.submit();
            });
        }
    </script>



</body>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\auth\login.blade.php ENDPATH**/ ?>