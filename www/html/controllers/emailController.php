<?php

function sendVerificationEmail($userEmail, $token)
{
    $FROM_EMAIL = $_ENV["FROM_EMAIL"];

    $to      = $userEmail;
    $subject = 'Verify your email address';
    $message = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify email</title>
</head>
<body>
    <div class="wrapper">
        <p>
        Thank you for signing up on our website. Please click on the link below
        too verify your email.
        </p>
        <a href="http://localhost:8080/index.php?token=' . $token . '">
        Verify your email address
        </a>
    </div>
</body>
</html>';
    $headers = 'From: ' . $FROM_EMAIL . "\r\n" .
               'Content-type: text/html;' . "\r\n";

    $success = mail($to, $subject, $message, $headers);
    if (!$success)
    {
        //TODO
        error_log("ERROR [mail failed] FIN ERR", 0);
    }
}

function sendPasswordResetLink($userEmail, $username, $token)
{
    //TODO maybe set it as constant in php from .env
    $FROM_EMAIL = $_ENV["FROM_EMAIL"];

    $to      = $userEmail;
    $subject = 'Reset your password';
    $message = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify email</title>
</head>
<body>
    <div class="wrapper">
        <p>
            hello ' . $username . ',

            Please click on the link below to reset your password.
        </p>
        <a href="http://localhost:8080/index.php?password-token=' . $token . '">
        Reset your password
        </a>
    </div>
</body>
</html>';
    $headers = 'From: ' . $FROM_EMAIL . "\r\n" .
               'Content-type: text/html;' . "\r\n";

    $success = mail($to, $subject, $message, $headers);
    if (!$success)
    {
        //TODO
        error_log("ERROR [mail failed] FIN ERR", 0);
    }
}

?>
