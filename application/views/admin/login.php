<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login V9</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('assets/css/login.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('assets/css/util.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('assets/css/icons.css'); ?>" rel="stylesheet" type="text/css"/>

</head>
<body>
    
    
    <div class="container-login100" style="background-image: url('assets/images/bg-01.jpg');">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-80 p-b-30">
            <form class="login100-form validate-form"  action='' method='POST' enctype="multipart/form-data">
                <span class="login100-form-title p-b-37">
                    LOGIN
                </span>
                <?php 
                if (isset($error)) {
                    echo "
                    <div style='color: red; margin: 0 0 20px 0; border: solid 1px #f0a8b5; background: rgba(230,117,136,0.15); padding: 10px; border-radius: 5px'>
                    $error
                    </div>";
                }
                ?>
                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter username or email">
                    <input class="input100 form-control" type="text" name="email" placeholder="Email">
                    <span class="focus-input100"></span>
                </div>
                <div class="wrap-input100 validate-input m-b-25" data-validate = "Enter password">
                    <input class="input100 form-control" type="password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                </div>
                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" name='cmd' value="submit">
                        Login
                    </button>
                </div>
                <div class="text-center">
                    <a href="#" class="txt2 hov1">
                        Sign Up
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/login.js'); ?>"></script>

</body>
</html>