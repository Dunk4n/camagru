<?php
require_once 'controllers/authController.php';
require_once 'config/define.php';

if (isset($_SESSION['id']))
{
    updateUserInfo();
}

if (isset($_GET['id']))
{
    // get the actual image id
    $id = $_GET['id'];

    $image = getImageFromId($id);
    if($image == false)
    {
        header('location: index.php');
        exit();
    }

    // check if this user is connected
    if (isset($_SESSION['id']))
    {
        // get and set in db the comment
        if (isset($_POST['comment-btn']))
        {
            $commentContent = $_POST['comment'];
            if(!empty($commentContent))
            {

                $sql = "INSERT INTO comments (imageId, userId, content, uploadedOn) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if($stmt != false)
                {
                    $stmt->bind_param('ddss', $id, $_SESSION['id'], $commentContent, date('Y-m-d H:i:s'));

                    if ($stmt->execute())
                    {
                        //TODO add message comment succeeded
                    }
                    else
                    {
                        //TODO add error cannot comment
                    }
                }
            }
        }

        // get and set the user like in db
        if (isset($_POST['like-btn']))
        {
            $sql = "SELECT * FROM likes WHERE imageId=? AND userId=? LIMIT 1";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('dd', $id, $_SESSION['id']);
                if ($stmt->execute())
                {
                    // check if the image is not already liked
                    if($stmt->get_result()->num_rows == 0)
                    {
                        $sql = "INSERT INTO likes (imageId, userId) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        if($stmt != false)
                        {
                            $stmt->bind_param('dd', $id, $_SESSION['id']);

                            if ($stmt->execute())
                            {
                                //TODO add message like succeeded
                            }
                            else
                            {
                                //TODO add error cannot like
                            }
                        }
                    }
                }
            }
        }

        // get and set the user unlike in db
        if (isset($_POST['unlike-btn']))
        {
            $sql = "DELETE FROM likes WHERE imageId=? AND userId=?";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('dd', $id, $_SESSION['id']);

                if ($stmt->execute())
                {
                    //TODO add message unlike succeeded
                }
                else
                {
                    //TODO add error cannot unlike
                }
            }
        }
    }

    $imageUser = getUserFromId($image['userId']);
    if($imageUser == false)
    {
        header('location: index.php');
        exit();
    }

    $likesNum = getLikesNumberFromImageId($id);
    $comments = getcommentsFromImageId($id);
}
else
{
    header('location: index.php');
    exit();
}

// check if this user is connected
if (isset($_SESSION['id']))
{
    $liked = false;
    $sql = "SELECT * FROM likes WHERE imageId=? AND userId=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if($stmt != false)
    {
        $stmt->bind_param('dd', $id, $_SESSION['id']);
        if ($stmt->execute())
        {
            // check if the image is not already liked
            if($stmt->get_result()->num_rows > 0)
                $liked = true;
        }
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="app_style.css">

        <title>Homepage</title>
    </head>

    <body>
        <?php require 'header.php' ?>
        <main>
            <div class="main-container">
            <img src="<?php echo 'http://localhost:8080/' . IMAGE_DIRECTORY . $image['fileName'] ?>"></img>
            <p><?php echo "created at: " . $image['uploadedOn'] ?></p>
            <p><?php echo "created by: " . $imageUser['username'] ?></p>
            <p><?php echo "with: " . $likesNum . " like" ?></p>

            <?php if(isset($_SESSION['id'])): ?>
                <div class="container">
                    <form action="picture.php?id=<?php echo $id ?>" method="post">
                        <div class="form-group">
                            <label for=comment>Comment</label>
                            <textarea name="comment" required minlength="1" maxlength="255" rows="3" class="form-control form-control-lg"></textarea>
                        </div>
                        <button type="submit" name="comment-btn" class="btn btn-primary btn-lg">comment</button>
                    </form>
                </div>

                <form action="picture.php?id=<?php echo $id ?>" method="post">
                <?php if($liked == true): ?>
                    <button type="submit" name="unlike-btn" class="btn btn-primary btn-lg">UNLIKE</button>
                <?php else: ?>
                    <button type="submit" name="like-btn" class="btn btn-primary btn-lg">LIKE</button>
                <?php endif; ?>
                </form>
            <?php endif; ?>
            <!-- add form add likes or rm -->
            <table>
                <thead>
                    <tr>
                        <th colspan="3">Comments</th>
                    </tr>
                    <tr>
                        <th>User</th>
                        <th>comment</th>
                        <th>date</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- loop over all comments -->
                    <tr>
                        <td>test</td>
                        <td>shit</td>
                        <td>3h</td>
                    </tr>
                </tbody>
            </table>

            </div>
        </main>
        <?php require 'footer.php' ?>
    </body>
</html>
