function filePreview(input, previewProfile) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(previewProfile).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}

// Example usage
// #profile = input type file
// #previewProfile = id img file receiver
// $("#profile").change(function() {
//     filePreview(this, '#previewProfile');
// });
