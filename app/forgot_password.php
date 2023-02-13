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

        <title>Forgot password</title>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4 form-div login">
                    <form action="forgot_password.php" method="post">
                        <h3 class="text-center">Recover your password</h3>
                        <p>
                            Please enter your email address you used to sign up on this site
                            and we will assist you in recovering your password.
                        </p>

                        <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error;; ?></li>
                            <?php endforeach; ?>
                        </div>
                        <?php endif ?>

                        <div class="form-group">
                            <input type="email" name="email" class="form-control form-control-lg">
                        </div>

                        <div class="form-group d-grid">
                            <button type="submit" name="forgot-password" class="btn">
                                Recover your password
                            </button>
                        </div>
                    </form>

                    <a href="login.php">login</a>
                </div>
            </div>
        </div>
    </body>
</html>
