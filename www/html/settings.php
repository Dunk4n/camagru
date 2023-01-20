<?php
require_once 'controllers/authController.php';

if (!isset($_SESSION['id']))
{
    header('location: login.php');
    exit();
}
else
{
    updateUserInfo();
}

if(!$_SESSION['verified'])
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
        <link rel="stylesheet" href="app_style.css">

        <title>Settings</title>
    </head>

    <body class="camagru">
        <?php require 'header.php' ?>
        <main>
            <?php if(isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $_SESSION['alert-class']; ?> alert-over">
                <?php
                 echo $_SESSION['message'];
                 unset($_SESSION['message']);
                 unset($_SESSION['alert-class']);
                 ?>
            </div>
            <?php endif; ?>

            <form class="setting" action="settings.php" method="post">
                <div class="setting-field">
                    <label for=username>Username</label>
                    <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" class="form-control form-control-lg">
                </div>

                <div class="setting-field">
                    <label for=email>Email</label>
                    <input type="email" name="email" value="<?php echo $_SESSION['email']; ?>" class="form-control form-control-lg">
                </div>

                <div>
                    <input type="checkbox" name="receve-email-for-comment" class="form-check-input" <?php if ($_SESSION['emailForMessage'] == 1) { echo "checked"; } ?>>
                    <label for=receve-email-for-comment>Receve email for comment</label>
                </div>

                <div class="setting-field">
                    <button type="submit" name="submit-setting" class="btn btn-primary btn-lg">submit</button>
                </div>
            </form>
            <form class="setting" action="settings.php" method="post">
                <div>
                    <div>
                        <label for=password>new Password</label>
                        <input type="password" name="password" class="form-control form-control-lg">
                    </div>

                    <div class="form-group">
                        <label for=passwordConf>Confirm new Password</label>
                        <input type="password" name="passwordConf" class="form-control form-control-lg">
                    </div>
                </div>

                <div class="setting-field">
                    <button type="submit" name="submit-setting-password" class="btn btn-primary btn-lg">submit</button>
                </div>
            </form>
        </main>
        <footer>
            footer
        </footer>
    </body>
</html>
