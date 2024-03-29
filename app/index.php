<?php
require_once 'controllers/authController.php';

// verify the user using token
if (isset($_GET['token']))
{
    $token = $_GET['token'];
    verifyUser($token);
}

// verify the user using password-token
if (isset($_GET['password-token']))
{
    $passwordToken = $_GET['password-token'];
    resetPassword($passwordToken);
}

if (isset($_SESSION['id']))
{
    updateUserInfo();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="style.css">

        <title>Homepage</title>
    </head>

    <body>
        <?php require 'header.php' ?>
        <main>
            <?php if(isset($_SESSION['id'])): ?>
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert <?php echo $_SESSION['alert-class']; ?> alert-over">
                        <?php
                         echo $_SESSION['message'];
                         unset($_SESSION['message']);
                         unset($_SESSION['alert-class']);
                         ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="main-container">
                <?php require 'app_side.php' ?>
                <?php if(!isset($_SESSION['id'])): ?>
                    <div class="main">
                        <p class="text-center">Already a member? <a href="login.php">Sign In</a></p>
                        <p class="text-center">Not yet a member? <a href="signup.php">Sign up</a></p>
                    </div>
                <?php elseif(!$_SESSION['verified']): ?>
                    <?php require 'app_not_verified.php' ?>
                <?php else: ?>
                    <?php require 'app_main.php' ?>
                <?php endif; ?>
            </div>
        </main>
        <?php require 'footer.php' ?>
    </body>
</html>
