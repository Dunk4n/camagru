    <!-- <video id="player" autoplay></video>
    <div id="pick-image">
        <label>Video is not supported. Pick an Image instead</label>
        <input type="file" accept="image/*" id="image-picker">
    </div>
    <button class="btn btn-primary" id="capture-btn">Capture</button>
    <script src="script.js"></script>
    -->

<div class="main">
    <?php include 'process_image.php' ?>

    <?php if(!empty($msg)): ?>
        <div class="alert <?php echo $css_class; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profileImage">Profile Image</label>
            <input type="file" name="profileImage" id="profileImage" class="form-control">
        </div>
        <div class="form-group">
            <button type="submit" name="save-user" class="btn btn-primary">Save img</button>
        </div>
    </form>
</div>
