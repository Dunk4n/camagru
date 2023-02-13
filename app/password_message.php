<?php
    require_once 'controllers/authController.php';

    if (isset($_SESSION['id']))
    {
        header('location: index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="style.css">

        <title>Password message</title>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4 form-div login">
                    <p>
                        An email has been sent to your email address to with a link to reset your password.
                    </p>
                    <a href="index.php">login</a>
                </div>
            </div>
        </div>
    </body>
</html>
