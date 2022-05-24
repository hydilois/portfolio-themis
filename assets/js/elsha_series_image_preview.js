function readURL(input, $preview) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $preview.css('background-image', 'url(' + e.target.result + ')');
            $preview.hide();
            $preview.fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}


$("#project_guardImageName").change(function () {
    readURL(this, $('#guardImagePreview'));
});

$("#project_illustrativeImageName").change(function () {
    readURL(this, $('#illustrationImagePreview'));
});