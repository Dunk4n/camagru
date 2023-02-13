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

    // check if this user is connected and verified
    if (isset($_SESSION['id']) && $_SESSION['verified'])
    {
        if ($image['userId'] == $_SESSION['id'] && isset($_POST['delete-btn']))
        {
            $sql = "DELETE FROM likes WHERE imageId=?";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('d', $id);

                $stmt->execute();
            }

            $sql = "DELETE FROM comments WHERE imageId=?";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('d', $id);

                $stmt->execute();
            }

            $sql = "DELETE FROM images WHERE id=?";
            $stmt = $conn->prepare($sql);
            if($stmt != false)
            {
                $stmt->bind_param('d', $id);

                $stmt->execute();

                header('location: index.php');
                exit();
            }
        }

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
                        if(!sendEmailToImageCreator($image, $_SESSION['username']))
                        {
                        }
                    }
                    else
                    {
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

                            $stmt->execute();
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

                $stmt->execute();
            }
        }

        if (isset($_POST['remove-comment-btn']))
        {
            $commentId = $_POST['comment-id'];
            if (!deleteCommentsFromId($commentId, $_SESSION['id']))
            {
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

        <link rel="stylesheet" href="style.css">

        <title>Homepage</title>
    </head>

    <body>
        <?php require 'header.php' ?>
        <main>
            <div class="picture-global-display">
                <img class="picture" src="<?php echo IMAGE_DIRECTORY . $image['fileName'] ?>"></img>
                <div>
                    <p><?php echo "created at: " . $image['uploadedOn'] ?></p>
                    <p><?php echo "created by: " . htmlspecialchars($imageUser['username']) ?></p>
                    <p><?php echo "with: " . $likesNum . " like" ?></p>
                </div>

                <div class="picture-action">
                    <?php if(isset($_SESSION['id']) && $_SESSION['verified']): ?>
                        <form action="picture.php?id=<?php echo $id ?>" method="post">
                            <?php if($liked == true): ?>
                                <button type="submit" name="unlike-btn" class="btn">UNLIKE</button>
                            <?php else: ?>
                                <button type="submit" name="like-btn" class="btn">LIKE</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>" data-show-count="false">Tweet</a>

                    <?php if(isset($_SESSION['id']) && $_SESSION['verified'] && $image['userId'] == $_SESSION['id']): ?>
                        <form action="picture.php?id=<?php echo $id ?>" method="post">
                            <button type="submit" name="delete-btn" class="btn btn-danger">delete image</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php if(isset($_SESSION['id']) && $_SESSION['verified']): ?>
                    <div class="container">
                        <form action="picture.php?id=<?php echo $id ?>" method="post">
                            <div class="comment-input">
                                <label for=comment>Comment:</label>
                                <textarea name="comment" required minlength="1" maxlength="255" rows="3"></textarea>
                            </div>
                            <button type="submit" name="comment-btn" class="btn">comment</button>
                        </form>
                    </div>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>comment</th>
                            <th>date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($comments)
                        {
                            foreach ($comments as $comment)
                            {
                                $commentUser = getUserFromId($comment['userId']);
                                echo "<tr>";
                                echo "    <td>" . htmlspecialchars($commentUser['username']) . "</td>";
                                echo "    <td>" . htmlspecialchars($comment['content']) . "</td>";
                                echo "    <td>" . $comment['uploadedOn'] . "</td>";
                                if($comment['userId'] == $_SESSION['id'] && $_SESSION['verified'])
                                {
                                    echo "<td><form action=\"picture.php?id=" . $id . "\" method=\"post\">
                                    <input type='hidden' name='comment-id' value='". $comment['id'] ."'>
                                    <button type=\"submit\" name=\"remove-comment-btn\" class=\"btn\">delete</button>
                                </form></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </main>
        <?php require 'footer.php' ?>
    </body>
</html>
