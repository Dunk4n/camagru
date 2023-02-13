<div class="main">

    <?php if(!empty($msg)): ?>
        <div class="alert <?php echo $css_class; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="preview-image">
        <canvas id="mergeImagePreview" hidden></canvas>
        <form action="index.php" name="formSubmitImage" id="formSubmitImage" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="inputImage" id="inputImage" class="input-file" hidden>
            </div>
            <div class="form-group">
                <button type="submit" name="submitImage" id="submitImage" class="btn" disabled>Save img</button>
            </div>
        </form>
    </div>
    <div name="selection-images" id="selection-images" class="selection-images">
        <button class="selection-image-button" id="1">
            <img id="selection-image-1" class="selection-image" src="selection_image/transparent_cat.png">
        </button>
        <button class="selection-image-button" id="2">
            <img id="selection-image-2" class="selection-image" src="selection_image/baby-cat-png-12.png">
        </button>
        <button class="selection-image-button" id="3">
            <img id="selection-image-3" class="selection-image" src="selection_image/transparent_cat3.png">
        </button>
        <button class="selection-image-button" id="4">
            <img id="selection-image-4" class="selection-image" src="selection_image/transparent_cat4.png">
        </button>
        <button class="selection-image-button" id="5">
            <img id="selection-image-5" class="selection-image" src="selection_image/check-mark.png">
        </button>
        <button class="selection-image-button" id="6">
            <img id="selection-image-6" class="selection-image" src="selection_image/cat.png">
        </button>
    </div>
    <script src="script.js"></script>
</div>
