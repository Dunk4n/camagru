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
}

function sendPasswordResetLink($userEmail, $username, $token)
{
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
            hello ' . htmlspecialchars($username) . ',

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
}

function sendEmailToImageCreator($image, $username)
{
    $user = getUserFromId($image['userId']);
    if(!$user)
        return (false);
    if(!$user['emailForMessage'])
        return (true);
    if(!$user['verified'])
        return (true);

    $FROM_EMAIL = $_ENV["FROM_EMAIL"];

    $to      = $user['email'];
    $subject = 'You got new comment';
    $message = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You got new comment</title>
</head>
<body>
    <div class="wrapper">
        <p>
        You got new comment on this <a href="http://localhost:8080/picture.php?id=' . $image['id'] . '">picture</a>
        from ' . htmlspecialchars($username) . '
        </p>
    </div>
</body>
</html>';
    $headers = 'From: ' . $FROM_EMAIL . "\r\n" .
               'Content-type: text/html;' . "\r\n";

    $success = mail($to, $subject, $message, $headers);
    if (!$success)
        return (false);
    return (true);
}

?>
