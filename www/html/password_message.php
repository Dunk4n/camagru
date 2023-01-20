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
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="style.css">

        <title>Password message</title>
    </head>

    <body style="background-color: grey;">
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
