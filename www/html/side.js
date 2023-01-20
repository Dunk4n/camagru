const container = document.querySelector('.side');

let img_pos = 1;
function loadImages(numImages = 10)
{
    let i=0;
    while(i < numImages)
    {
        fetch('http://localhost:8080/getImage.php?id=' + img_pos)
            .then(response=>response.json())
            .then(data=>{
                const link =  document.createElement('a');
                link.href = 'http://localhost:8080/picture.php?id=' + `${data.imageId}`;
                const img =  document.createElement('img');
                img.src = 'http://localhost:8080/' + `${data.image}`;
                img.className = 'image-side';

                link.appendChild(img);
                container.appendChild(link);
            })
        img_pos++;
        i++;
    }
}

loadImages();

let lastScrollTop = 0;
container.onscroll = (e)=>{
    //if (container.scrollTop < lastScrollTop)
    //{
    //    return;
    //}
    lastScrollTop = container.scrollTop <= 0 ? 0 : container.scrollTop;
    if (container.scrollTop + container.offsetHeight >= container.scrollHeight)
    {
        loadImages(3);
    }
}
