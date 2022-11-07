window.onload = () => {
    let medias_input = document.getElementById('medias');
    let input_submit = document.getElementById('input_submit');
    medias_input.addEventListener('change', (e) => {
        let media_label = document.getElementById('medias_label');
        media_label.style.backgroundImage = `url(${URL.createObjectURL(e.target.files[0])})`;
        // get height of first media
        let img = new Image();
        img.src = URL.createObjectURL(e.target.files[0]);
        img.onload = () => {
            //calculate ratio
            let ratio = img.height / img.width;
            //set height of label
            media_label.style.height = `${media_label.offsetWidth * ratio}px`;
        }
        input_submit.disabled = false;
    });
}
