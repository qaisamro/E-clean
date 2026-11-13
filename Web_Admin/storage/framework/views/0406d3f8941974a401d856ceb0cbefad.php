<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP</title>
</head>
<body>
   <div style="text-align: center; padding: 20px">
    <h4> Hello, <?php echo e($user->name); ?> . Your reset password otp is- </h4>
    <h1><?php echo e($otp); ?></h1>
   </div>
</body>
</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\mail\sendMail.blade.php ENDPATH**/ ?>