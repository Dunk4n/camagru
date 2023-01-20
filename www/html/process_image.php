<?php

require_once 'controllers/authController.php';
require_once 'config/define.php';

$msg = "";
$css_class = "";


function checkImageValidity($image, $msg)
{
    if($image['error'] !== UPLOAD_ERR_OK)
    {
        $msg = "Failed to upload image";
        return (false);
    }

    if($image['size'] >= IMAGE_SIZE_MAX)
    {
        $msg = "Image too large";
        return (false);
    }

    $info = getimagesize($image['tmp_name']);
    if ($info === FALSE)
    {
        $msg = "Unable to determine image type of uploaded file";
        return (false);
    }

    if ($info[0] == 0 || $info[1] == 0)
    {
        $msg = "not a valid image";
        return (false);
    }

    if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG))
    {
        $msg = "Not a gif/jpeg/png";
        return (false);
    }

    return (true);
}

if (isset($_POST['save-user']))
{
    echo "<pre>", print_r($_FILES['profileImage']), "</pre>";

    $imageName = time() . '_' . substr($_FILES['profileImage']['name'], 0, 30);

    if(checkImageValidity($_FILES['profileImage'], $msg) == false)
    {
        $css_class = "alert-danger";
    }
    else
    {
        $target = IMAGE_PATH . $imageName;

        if (!move_uploaded_file($_FILES['profileImage']['tmp_name'], $target))
        {
            $msg = "Failed to upload image";
            $css_class = "alert-danger";
        }
        else
        {
            $sql = "INSERT INTO images (userId, fileName, uploadedOn) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('dss', $_SESSION['id'], $imageName, date('Y-m-d H:i:s'));
                if (!$stmt->execute())
                {
                    $msg = "Failed to upload image";
                    $css_class = "alert-danger";
                }
                else
                {
                    $msg = "Image uploaded and saved to database";
                    $css_class = "alert-success";
                }
            }
        }
    }
}

?>
