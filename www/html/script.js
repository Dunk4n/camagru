const videoPlayer = document.querySelector("#player");
const imagePickerArea = document.querySelector("#pick-image");

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
      videoPlayer.srcObject = stream;
      videoPlayer.style.display = "flex";
      imagePickerArea.style.display = "none";
    })
    .catch(err => {
      imagePickerArea.style.display = "flex";
      videoPlayer.style.display = "none";
    });
};

window.addEventListener("load", event => startMedia());
