$(function() {
    var $maxlengthInput = $(".input-maxlength");
    if($maxlengthInput.length) {
        $maxlengthInput.each(function() {
    	    $(this).maxlength({
    		    warningClass : "text-info text-opacity-50 fs-10",
    		    limitReachedClass : "text-warning fs-10",
    		    separator : " out of ",
    		    preText : "You typed ",
    		    postText : " chars available.",
    		    validate : true,
    		    threshold : +this.getAttribute("maxlength")
    	    });
    	});
    }

    tinymce.init({
        selector: "textarea.tinymce",
        menubar:false,
        branding: false,
        plugins: 'link preview wordcount code autolink table toc image fullscreen',
        toolbar1: 'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | undo redo | table image link |code preview fullscreen',
        autoresize_min_height: 200,
        relative_urls : false,
        skin: 'oxide-dark',
        content_css: 'dark'
    });
});

function showPreviewImageModal(elementId){
    var modal = document.getElementById('modal-' + elementId);
    var img = document.getElementById(elementId);
    var modalImg = document.getElementById('modal-img-' + elementId);
    modal.style.display = "block";
    modalImg.src = img.src;
}

function closePhotoModal(elementId){
    var modal = document.getElementById('modal-' + elementId);
    modal.style.display = "none";
}


function previewImageUpload(event, elementId) {
    const selectedImage = document.getElementById(elementId);
    if(selectedImage){
        const fileInput = event.target;
        if (event.target.files && event.target.files[0]) {
            if(event.target.files[0].size > 2 * 1024 * 1024){
                alert("File size should be less than 2MB");
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                selectedImage.src = e.target.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    }
}
