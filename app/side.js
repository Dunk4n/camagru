const container = document.querySelector('.side');

let img_pos = 0;
function loadImages(numImages = 10)
{
    let i=0;
    while(i < numImages)
    {
        const link = document.createElement('a');
        link.href = '';
        link.id = 'image-link-' + img_pos;
        const img = document.createElement('img');
        img.src = '';
        img.id = 'image-' + img_pos;
        img.className = 'image-side';

        link.appendChild(img);
        container.appendChild(link);

        fetch('getImage.php?id=' + img_pos)
            .then(response => {
                if (!response.ok)
                    throw new Error('Something went wrong');
                return response.json();
            })
            .then(data=>{
                if(`${data.success}` != 1)
                {
                    img_pos--;
                    link.remove();
                    img.remove();
                    return;
                }
                else
                {
                    link.href = 'picture.php?id=' + `${data.imageId}`;
                    img.src = `${data.image}`;
                }
            }).catch(err => {
                img_pos--;
                link.remove();
                img.remove();
                });
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
