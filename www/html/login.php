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

        <title>Login</title>
    </head>

    <body style="background-color: grey;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4 form-div login">
                    <form action="login.php" method="post">
                        <h3 class="text-center">Login</h3>

                        <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error;; ?></li>
                            <?php endforeach; ?>
                        </div>
                        <?php endif ?>

                        <div class="form-group">
                            <label for=username>Username</label>
                            <input type="text" name="username" value="<?php echo $username; ?>" class="form-control form-control-lg">
                        </div>

                        <div class="form-group">
                            <label for=password>Password</label>
                            <input type="password" name="password" class="form-control form-control-lg">
                        </div>

                        <div class="form-group d-grid">
                            <button type="submit" name="login-btn" class="btn btn-primary btn-lg">Login</button>
                        </div>

                        <p class="text-center">Not yet a member? <a href="signup.php">Sign up</a></p>
                        <p class="text-center">Enter as a visitor <a href="index.php">Visitor</a></p>
                        <div style="font-size: 0.8em; text-align: center;"><a href="forgot_password.php">Forgot your password</a></div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
