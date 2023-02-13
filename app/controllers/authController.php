<?php

session_start();

require 'config/db.php';
require 'emailController.php';

$errors = array();
$username = "";
$email = "";

// if user clicks on the sign up button
if (isset($_POST['signup-btn']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConf = $_POST['passwordConf'];

    // validation
    if (empty($username))
    {
        $errors['username'] = "Username required";
    }
    if (strlen($username) > 20 || strlen($username) == 0)
    {
        $errors['username'] = "Username wrong size";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors['email'] = "Email address is invalid";
    }
    if (empty($email))
    {
        $errors['email'] = "Email required";
    }
    if (empty($password))
    {
        $errors['password'] = "Password required";
    }
    if ($password !== $passwordConf)
    {
        $errors['password'] = "The two password do not match";
    }
    if (strlen($password) >= 200)
    {
        $errors['password'] = "Password too long";
    }
    if (strlen($password) < 8)
    {
        $errors['password'] = "Password too small";
    }
    if (count($errors) == 0)
    {
        $password_number_present = false;
        $password_upper_present = false;
        $password_special_present = false;
        $cnt = 0;
        while ($cnt < strlen($password))
        {
            if ($password[$cnt] >= '0' && $password[$cnt] <= '9')
                $password_number_present = true;
            if ($password[$cnt] >= 'A' && $password[$cnt] <= 'Z')
                $password_upper_present = true;
            if (($password[$cnt] < 'a' || $password[$cnt] > 'z') && ($password[$cnt] < 'A' || $password[$cnt] > 'Z') && ($password[$cnt] < '0' || $password[$cnt] > '9'))
                $password_special_present = true;

            $cnt = $cnt + 1;
        }

        if ($password_number_present != true || $password_upper_present != true || $password_special_present != true)
        {
            $errors['password'] = "Password must have at least 8 charcters and at least 1 upper case, numeric, and special character";
        }
    }


    $emailQuery = "SELECT * FROM users WHERE email=? OR username=? LIMIT 1";
    $stmt = $conn->prepare($emailQuery);
    if($stmt != false)
    {
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $userCount = $result->num_rows;
        $stmt->close();

        if ($userCount > 0)
        {
            $errors['email'] = "Email or username already exists";
        }

        if (count($errors) == 0)
        {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(50));
            $verified = 0;

            $sql = "INSERT INTO users (username, email, verified, token, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('ssdss', $username, $email, $verified, $token, $password);

                if ($stmt->execute())
                {
                    //login user
                    $user_id = $conn->insert_id;
                    $_SESSION['id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['verified'] = $verified;
                    $_SESSION['emailForMessage'] = 1;

                    sendVerificationEmail($email, $token);

                    // set flash message
                    $_SESSION['message'] = "You are now logged in!";
                    $_SESSION['alert-class'] = "alert-success";
                    header('location: index.php');
                    exit();
                }
                else
                {
                    $errors['db_error'] = "Database error: failed to register";
                }
            }
        }
    }
}

// if user clicks on the login button
if (isset($_POST['login-btn']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    // validation
    if (empty($username))
    {
        $errors['username'] = "Username required";
    }
    if (strlen($username) > 20 || strlen($username) == 0)
    {
        $errors['username'] = "Username wrong size";
    }
    if (empty($password))
    {
        $errors['password'] = "Password required";
    }

    if (count($errors) == 0)
    {
        $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if($stmt != false)
        {
            $stmt->bind_param('s', $username);
            if ($stmt->execute())
            {
                $result = $stmt->get_result();
                $userCount = $result->num_rows;
                if($userCount > 0)
                {
                    $user = $result->fetch_assoc();

                    if (password_verify($password, $user['password']))
                    {
                        //login success
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['verified'] = $user['verified'];
                        $_SESSION['emailForMessage'] = $user['emailForMessage'];
                        // set flash message
                        $_SESSION['message'] = "You are now logged in!";
                        $_SESSION['alert-class'] = "alert-success";

                        header('location: index.php');
                        exit();
                    }
                    else
                    {
                        $errors['login_fail'] = "Wrong credentials";
                    }
                }
                else
                {
                    $errors['login_fail'] = "Wrong username";
                }
            }
            else
            {
                $errors['db'] = "database failed";
            }
        }
    }
}

// logout user
if (isset($_GET['logout']))
{
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['verified']);
    unset($_SESSION['emailForMessage']);
    header('location: login.php');
    exit();
}

// resend verification email
if (isset($_GET['resend']))
{
    $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt != false)
    {
        $stmt->bind_param('s', $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userCount = $result->num_rows;
        $user = $result->fetch_assoc();

        if ($userCount == 0)
        {
            header('location: index.php');
            exit();
        }

        sendVerificationEmail($_SESSION['email'], $user['token']);
        header('location: index.php');
        exit();
    }
}

// verify user by token
function verifyUser($token)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE token=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('s', $token);
    $stmt->execute();

    $result = $stmt->get_result();
    $userCount = $result->num_rows;

    if ($userCount > 0)
    {
        $user = $result->fetch_assoc();
        $update_query = "UPDATE users SET verified=1 WHERE token=?";
        $stmt = $conn->prepare($update_query);
        if($stmt == false)
            return (false);
        $stmt->bind_param('s', $token);

        if ($stmt->execute())
        {
            // log user in
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified'] = 1;
            $_SESSION['emailForMessage'] = $user['emailForMessage'];
            // set flash message
            $_SESSION['message'] = "Your email address was successfully verified!";
            $_SESSION['alert-class'] = "alert-success";

            header('location: index.php');
            exit();
        }
    }
}

// update the session user info from the database
function updateUserInfo()
{
    global $conn;
    $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('s', $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->num_rows;

    if ($userCount > 0)
    {
        $user = $result->fetch_assoc();
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['verified'] = $user['verified'];
        $_SESSION['emailForMessage'] = $user['emailForMessage'];
    }
    else
    {
        echo 'User not found';
    }
}

// if user clicks on the forgot password button
if (isset($_POST['forgot-password']))
{
    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors['email'] = "Email address is invalid";
    }
    if (empty($email))
    {
        $errors['email'] = "Email required";
    }

    if (count($errors) == 0)
    {
        $sql = "SELECT * FROM users WHERE email=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if($stmt != false)
        {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $userCount = $result->num_rows;
            if ($userCount == 1)
            {
                $user = $result->fetch_assoc();
                if ($user['verified'])
                {
                    sendPasswordResetLink($email, $user['username'], $user['token']);
                    header('location: password_message.php');
                    exit(0);
                }
                else
                {
                    $errors['email'] = "Email not verified";
                }
            }
        }
    }
}

// if user clicked on the reset password button
if (isset($_POST['reset-password-btn']))
{
    $password = $_POST['password'];
    $passwordConf = $_POST['passwordConf'];

    if(updatePassword($_SESSION['id'], $password, $passwordConf, $errors))
    {
        header('location: login.php');
        exit(0);
    }
}

function resetPassword($token)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE token=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('s', $token);

    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->num_rows;
    if ($userCount > 0)
    {
        $user = $result->fetch_assoc();
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['verified'] = $user['verified'];
        $_SESSION['emailForMessage'] = $user['emailForMessage'];
        header('location: reset_password.php');
        exit(0);
    }

    header('location: forgot_password.php');
    exit(0);
}

if (isset($_POST['submit-setting']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    if ($_POST['receve-email-for-comment'] == 'on')
        $receveEmailForComment = 1;
    else
        $receveEmailForComment = 0;
    $error = false;

    // validation
    if (empty($username))
    {
        $_SESSION['message'] = "Username required";
        $_SESSION['alert-class'] = "alert-danger";
        $error = true;
    }
    if (strlen($username) > 20 || strlen($username) == 0)
    {
        $_SESSION['message'] = "Username wrong size";
        $_SESSION['alert-class'] = "alert-danger";
        $error = true;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $_SESSION['message'] = "Email address is invalid";
        $_SESSION['alert-class'] = "alert-danger";
        $error = true;
    }
    if (empty($email))
    {
        $_SESSION['message'] = "Email required";
        $_SESSION['alert-class'] = "alert-danger";
        $error = true;
    }

    if ($error == false && ($_SESSION['username'] != $username || $_SESSION['email'] != $email || $_SESSION['emailForMessage'] != $receveEmailForComment))
    {
        $token = bin2hex(random_bytes(50));
        if($_SESSION['email'] != $email)
        {
            $update_query = "UPDATE users SET username=?, email=?, token=?, verified=0, emailForMessage=? WHERE id=?";
        }
        else
        {
            $update_query = "UPDATE users SET username=?, email=?, token=?, emailForMessage=? WHERE id=?";
        }

        $stmt = $conn->prepare($update_query);
        if($stmt != false)
        {
            $stmt->bind_param('sssdd', $username, $email, $token, $receveEmailForComment, $_SESSION['id']);

            if ($stmt->execute())
            {
                if($_SESSION['email'] != $email)
                    sendVerificationEmail($email, $token);

                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['emailForMessage'] = $receveEmailForComment;
                if($_SESSION['email'] != $email)
                {
                    $_SESSION['verified'] = 0;
                }

                $_SESSION['message'] = "User value was successfully modified";
                $_SESSION['alert-class'] = "alert-success";
            }
            else
            {
                $_SESSION['message'] = "Failed to modify user value";
                $_SESSION['alert-class'] = "alert-danger";
            }
        }
    }
}

if (isset($_POST['submit-setting-password']))
{
    $password = $_POST['password'];
    $passwordConf = $_POST['passwordConf'];

    if(updatePassword($_SESSION['id'], $password, $passwordConf, $errors))
    {
        $_SESSION['message'] = "Password successfully modified";
        $_SESSION['alert-class'] = "alert-success";
    }
}

function updatePassword($id, $password, $passwordConf, $errors)
{
    if (empty($password) || empty($passwordConf))
    {
        $errors['password'] = "Password required";
        return (false);
    }
    if ($password !== $passwordConf)
    {
        $errors['password'] = "The two password do not match";
        return (false);
    }
    if (strlen($password) >= 200)
    {
        $errors['password'] = "Password too long";
        return (false);
    }
    if (strlen($password) < 8)
    {
        $errors['password'] = "Password too small";
    }
    if (count($errors) == 0)
    {
        $password_number_present = false;
        $password_upper_present = false;
        $password_special_present = false;
        $cnt = 0;
        while ($cnt < strlen($password))
        {
            if ($password[$cnt] >= '0' && $password[$cnt] <= '9')
                $password_number_present = true;
            if ($password[$cnt] >= 'A' && $password[$cnt] <= 'Z')
                $password_upper_present = true;
            if (($password[$cnt] < 'a' || $password[$cnt] > 'z') && ($password[$cnt] < 'A' || $password[$cnt] > 'Z') && ($password[$cnt] < '0' || $password[$cnt] > '9'))
                $password_special_present = true;

            $cnt = $cnt + 1;
        }

        if ($password_number_present != true || $password_upper_present != true || $password_special_present != true)
        {
            $errors['password'] = "Password must have at least 8 charcters and at least 1 upper case, numeric, and special character";
        }
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    if (count($errors) == 0)
    {
        global $conn;

        $token = bin2hex(random_bytes(50));
        $sql = "UPDATE users SET password=?, token=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if($stmt == false)
            return (false);
        $stmt->bind_param('ssd', $password, $token, $id);

        if ($stmt->execute())
        {
            return (true);
        }
    }
    return (false);
}

function getImageFromId($id)
{
    global $conn;

    $sql = "SELECT * FROM images WHERE id=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
        return (false);

    $result = $stmt->get_result();
    $count = $result->num_rows;
    if($count == 0)
        return (false);

    $image = $result->fetch_assoc();
    return ($image);
}

function getUserFromId($id)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE id=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
    {
        return (false);
    }

    $result = $stmt->get_result();
    $userCount = $result->num_rows;
    if($userCount == 0)
        return (false);

    $user = $result->fetch_assoc();
    return ($user);
}

function getLikesNumberFromImageId($id)
{
    global $conn;

    $sql = "SELECT * FROM likes WHERE imageId=?";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
    {
        return (false);
    }

    $result = $stmt->get_result();
    $count = $result->num_rows;
    return ($count);
}

function getcommentsFromImageId($id)
{
    global $conn;

    $sql = "SELECT * FROM comments WHERE imageId=?";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
    {
        return (false);
    }

    $result = $stmt->get_result();
    $count = $result->num_rows;
    if($count == 0)
        return (false);

    $comments = Array();
    $cnt = 0;
    while ($comment = $result->fetch_assoc())
    {
        $comments[$cnt] = $comment;
        $cnt++;
    }
    return ($comments);
}

function getCommentFromId($id)
{
    global $conn;

    $sql = "SELECT * FROM comments WHERE id=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
        return (false);

    $result = $stmt->get_result();
    $count = $result->num_rows;
    if($count == 0)
        return (false);

    $comment = $result->fetch_assoc();
    return ($comment);
}

function deleteCommentsFromId($id, $userId)
{
    if($id < 1)
        return (false);

    global $conn;

    $comment = getCommentFromId($id);
    if(!$comment)
        return (false);
    if($comment['userId'] != $userId)
        return (false);

    $sql = "DELETE FROM comments WHERE id=?";
    $stmt = $conn->prepare($sql);
    if($stmt == false)
        return (false);
    $stmt->bind_param('d', $id);
    if (!$stmt->execute())
    {
        return (false);
    }
    return (true);
}

require_once "controllers/process_image.php"
?>
