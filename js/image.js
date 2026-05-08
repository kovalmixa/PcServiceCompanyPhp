function previewImage(input, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imgElement = document.getElementById(imgId);
            imgElement.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}