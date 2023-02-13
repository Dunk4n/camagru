videoPreview = document.createElement('video');

webcam = false;
imageInput = document.querySelector("#inputImage");
buttonImageSubmit = document.querySelector("#submitImage");
canvasImagePreview = document.querySelector("#mergeImagePreview");
canvasImagePreviewContext = canvasImagePreview.getContext("2d");
formSubmitImage = document.querySelector("#formSubmitImage");

var glbl_actualImageElement = null;
const selectionImagesElement = document.querySelector("#selection-images");

const startMedia = () => {
  if (!("mediaDevices" in navigator)) {
    navigator.mediaDevices = {};
  }

  if (!("getUserMedia" in navigator.mediaDevices)) {
    navigator.mediaDevices.getUserMedia = constraints => {
      const getUserMedia =
        navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

      if (!getUserMedia) {
        return Promise.reject(new Error("getUserMedia is not supported"));
      } else {
        return new Promise((resolve, reject) =>
          getUserMedia.call(navigator, constraints, resolve, reject)
        );
      }
    };
  }

  navigator.mediaDevices
    .getUserMedia({ video: true })
        .then(stream => {
            webcam = true;
            videoPreview.srcObject = stream;
            videoPreview.autoplay = true;

            imageInput.hidden = true;
            imageInput.disabled = true;

            canvasImagePreview.hidden = false;
        })
        .catch(err => {
            webcam = false;
            videoPreview.autoplay = false;

            imageInput.hidden = false;
            imageInput.disabled = false;

            imageInput.onchange = evt => {
                setImagePreview(imageInput);
            }
        });
};

if(formSubmitImage.attachEvent)
    formSubmitImage.attachEvent("submit", processForm);
else
    formSubmitImage.addEventListener("submit", processForm);

function processForm(form)
{
    if(form.preventDefault)
        form.preventDefault();

    if(webcam == true)
    {
        var canvas = document.createElement('canvas');
        canvas.width = videoPreview.videoWidth;
        canvas.height = videoPreview.videoHeight;
        canvas.getContext("2d").drawImage(videoPreview, 0, 0);
        file = null;
        var blob = canvas.toBlob(function(blob) {
            file = new File([blob], 'img.png', { type: 'image/png' });

            var data = new FormData()
            data.append('inputImage', file)
            data.append('submitImage', true)
            fetch(formSubmitImage.action, {
                method: 'POST',
                body: data
            }).then((data) => {
                loadImages(3);
            });
        }, 'image/png');
    }
    else
    {
        if(!imageInput.files[0])
            return (false);
        var data = new FormData();
        data.append('inputImage', imageInput.files[0]);
        data.append('submitImage', true);
        fetch(formSubmitImage.action, {
            method: 'POST',
            body: data
        }).then((data) => {
            loadImages(3);
            glbl_actualImageElement = null;
            canvasImagePreview.hidden = true;
            imageInput.value = "";
            buttonImageSubmit.disabled = true;
        });
    }

    return (false);
}

window.addEventListener("load", event => startMedia());

function mergeImage(canvas, first_image, second_image)
{
    canvas.width = first_image.width;
    canvas.height = first_image.height;
    canvasImagePreviewContext.drawImage(first_image, 0, 0);
    canvasImagePreviewContext.drawImage(second_image, 0, 0);
}

function setImage(canvas, image)
{
    canvas.width = image.width;
    canvas.height = image.height;
    canvasImagePreviewContext.drawImage(image, 0, 0);
}

function setImagePreview(imageInput)
{
    if(!canvasImagePreview || !imageInput)
        return;
    const [file] = imageInput.files;
    if (file && (file.type == "image/png" || file.type == "image/jpg" || file.type == "image/jpeg"))
    {
        let reader = new FileReader();
        var img = new Image;

        reader.onload = function(ev)
        {
            img.src = ev.target.result;
        }

        img.onload = function()
        {
            if (glbl_actualImageElement != null)
            {
                mergeImage(canvasImagePreview, img, glbl_actualImageElement);
                buttonImageSubmit.disabled = false;
            }
            else
            {
                setImage(canvasImagePreview, img);
                buttonImageSubmit.disabled = true;
            }
            canvasImagePreview.hidden = false;
        }

        reader.readAsDataURL(file);
    }
    else
    {
        canvasImagePreview.hidden = true;
        imageInput.value = "";
        buttonImageSubmit.disabled = true;
    }
}

function baseName(str)
{
   return (new String(str).substring(str.lastIndexOf('/') + 1));
}

function SetImageToMergeOnButtonActionInDiv(actualDiv)
{
    if (!actualDiv)
        return (false);
    const selectionImages = actualDiv.children;
    if (!selectionImages || selectionImages.length == 0)
        return (false);

    var cnt = 0;
    while (cnt < selectionImages.length)
    {
        selectionImages[cnt].onclick = function()
        {
            var image = this.children;
            if (!image || image.length == 0)
                return;
            if(formSubmitImage)
                formSubmitImage.action = "index.php?image=" + baseName(image[0].src);
            glbl_actualImageElement = image[0];
            if(webcam == false)
                setImagePreview(imageInput);
            else
                buttonImageSubmit.disabled = false;
        }
        cnt++;
    }
    return (true);
}

if(selectionImagesElement)
    SetImageToMergeOnButtonActionInDiv(selectionImagesElement);

lastTime = -1;
function draw() {
    var time = videoPreview.currentTime;
    if (time !== lastTime) {
        canvasImagePreview.width = videoPreview.videoWidth;
        canvasImagePreview.height = videoPreview.videoHeight;
        canvasImagePreviewContext.drawImage(videoPreview, 0, 0);
        if(glbl_actualImageElement)
            canvasImagePreviewContext.drawImage(glbl_actualImageElement, 0, 0);
        lastTime = time;
    }

    requestAnimationFrame(draw);
}

draw();
