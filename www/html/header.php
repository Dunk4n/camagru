<header>
    <?php if(!isset($_SESSION['id'])): ?>
        <h3>Welcom</h3>
        <?php $filename = pathinfo($_SERVER['REQUEST_URI'])['filename']; ?>
        <?php if($filename == "picture"): ?>
            <a href="index.php?">Home</a>
        <?php else: ?>
            <a href="picture.php">picture</a>
        <?php endif; ?>
    <?php else: ?>
        <h3>Welcom, <?php echo $_SESSION['username']; ?></h3>
        <?php if($_SESSION['verified']): ?>
            <?php $filename = pathinfo($_SERVER['REQUEST_URI'])['filename']; ?>
            <?php if($filename == "settings"): ?>
                <a href="index.php?">Home</a>
            <?php else: ?>
                <a href="settings.php">settings</a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="picture.php">picture</a>
        <a href="index.php?logout=1" class="logout">logout</a>
    <?php endif; ?>
</header>
