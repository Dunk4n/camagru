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

        <title>Register</title>
    </head>

    <body style="background-color: grey;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4 form-div">
                    <form action="signup.php" method="post">
                        <h3 class="text-center">Register</h3>

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
                            <label for=email>Email</label>
                            <input type="email" name="email" value="<?php echo $email; ?>" class="form-control form-control-lg">
                        </div>

                        <div class="form-group">
                            <label for=password>Password</label>
                            <input type="password" name="password" class="form-control form-control-lg">
                        </div>

                        <div class="form-group">
                            <label for=passwordConf>Confirm Password</label>
                            <input type="password" name="passwordConf" class="form-control form-control-lg">
                        </div>

                        <div class="form-group d-grid">
                            <button type="submit" name="signup-btn" class="btn btn-primary btn-block btn-lg">Sig Up</button>
                        </div>

                        <p class="text-center">Already a member? <a href="login.php">Sign In</a></p>
                        <p class="text-center">Enter as a visitor <a href="index.php">Visitor</a></p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
