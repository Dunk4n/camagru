<?php require_once 'controllers/authController.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="style.css">

        <title>Reset password</title>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 offset-md-4 form-div login">
                    <form action="reset_password.php" method="post">
                        <h3 class="text-center">Reset password</h3>

                        <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error;; ?></li>
                            <?php endforeach; ?>
                        </div>
                        <?php endif ?>

                        <div class="form-group">
                            <label for=password>Password</label>
                            <input type="password" name="password" class="form-control form-control-lg">
                        </div>

                        <div class="form-group">
                            <label for=password>Confirm Password</label>
                            <input type="password" name="passwordConf" class="form-control form-control-lg">
                        </div>

                        <div class="form-group d-grid">
                            <button type="submit" name="reset-password-btn" class="btn">Reset password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
