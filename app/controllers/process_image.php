<?php

require_once 'controllers/authController.php';
require_once 'config/define.php';

$msg = "";
$css_class = "";


function checkImageValidity($image, $msg, &$info)
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

    if (($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG))
    {
        $msg = "Not a jpeg/png";
        return (false);
    }

    return (true);
}

function mergeAndSaveImage($file, $superposableImageName, &$msg)
{
    global $conn;
    $imageName = time() . '_' . substr($file['name'], 0, 30);

    $info = null;
    if(checkImageValidity($file, $msg, $info) == false)
        return (false);

    if($info[2] == IMAGETYPE_JPEG)
        $image = imagecreatefromjpeg($file['tmp_name']);
    else if($info[2] == IMAGETYPE_PNG)
        $image = imagecreatefrompng($file['tmp_name']);
    else
        return (false);

    if(!$image)
        return (false);

    if(imagesavealpha($image, true) == false)
        return (false);

    unlink($file['tmp_name']);

    $superposableImage = imagecreatefrompng("selection_image/" . $superposableImageName);
    if(!$superposableImage)
    {
        imagedestroy($image);
        return (false);
    }

    if(!imagecopy($image, $superposableImage, 0, 0, 0, 0, imagesx($superposableImage), imagesy($superposableImage)))
    {
        $msg = "Failed to upload image";
        imagedestroy($superposableImage);
        imagedestroy($image);
        return (false);
    }

    $target = IMAGE_PATH . $imageName;

    $ret = false;
    if($info[2] == IMAGETYPE_JPEG)
        $ret = imagejpeg($image, $target);
    else if($info[2] == IMAGETYPE_PNG)
        $ret = imagepng($image, $target);

    if(!$ret)
    {
        $msg = "Failed to upload image";
        imagedestroy($superposableImage);
        imagedestroy($image);
        return (false);
    }

    $sql = "INSERT INTO images (userId, fileName, uploadedOn) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if($stmt != false)
    {
        $stmt->bind_param('dss', $_SESSION['id'], $imageName, date('Y-m-d H:i:s'));
        if (!$stmt->execute())
        {
            $msg = "Failed to upload image";
            imagedestroy($superposableImage);
            imagedestroy($image);
            return (false);
        }
        else
        {
            $msg = "Image uploaded and saved to database";
            imagedestroy($superposableImage);
            imagedestroy($image);
            return (true);
        }
    }
    imagedestroy($superposableImage);
    imagedestroy($image);
    return (false);
}

if (isset($_POST['submitImage']))
{
    if(isset($_GET['image']) && isset($_FILES['inputImage']))
    {
        if(strlen($_GET['image']) > 50)
            exit();
        if(!mergeAndSaveImage($_FILES['inputImage'], basename($_GET['image']), $msg))
            $css_class = "alert-danger";
        else
            $css_class = "alert-success";
        header('location: index.php');
        exit();
    }
}

?>
