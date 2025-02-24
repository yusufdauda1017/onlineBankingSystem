document.getElementById("profilePic").addEventListener("change", function (event) {
    let file = event.target.files[0];
    let uploadButton = document.getElementById("uploadPic");

    if (!file) {
        uploadButton.disabled = true;
        return;
    }

    // Validate file type (ensure it's an image)
    if (!file.type.startsWith("image/")) {
        Swal.fire({
            icon: "error",
            title: "Invalid File Type",
            text: "Please upload a valid image file (JPG, PNG, GIF, etc.)."
        });
        event.target.value = ""; // Clear the file input
        uploadButton.disabled = true;
        return;
    }

    // Show preview before uploading
    let reader = new FileReader();
    reader.onload = function () {
        document.getElementById("profilePreview").style.opacity = "0.5";
        setTimeout(() => {
            document.getElementById("profilePreview").src = reader.result;
            document.getElementById("profilePreview").style.opacity = "1";
        }, 300);
    };
    reader.readAsDataURL(file);

    uploadButton.disabled = false; // Enable the upload button
});

document.getElementById("uploadPic").addEventListener("click", function () {
    let fileInput = document.getElementById("profilePic").files[0];
    let uploadButton = document.getElementById("uploadPic");
    let uploadText = document.getElementById("uploadText");
    let loader = document.getElementById("uploadLoader");
    let previewImage = document.getElementById("profilePreview");

    if (!fileInput) {
        Swal.fire({
            icon: "warning",
            title: "No File Selected",
            text: "Please select an image to upload."
        });
        return;
    }

    let formData = new FormData();
    formData.append("profilePic", fileInput);

    // Disable button, show loading animation
    uploadButton.disabled = true;
    loader.style.display = "inline-block";
    uploadText.innerHTML = "Uploading...";

    Swal.fire({
        title: "Uploading...",
        text: "Please wait while your image is being uploaded.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("./upload.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Smooth image update animation
            previewImage.style.opacity = "0.5";
            setTimeout(() => {
                previewImage.src = data.filepath;
                previewImage.style.opacity = "1";
            }, 500);

            Swal.fire({
                icon: "success",
                title: "Upload Successful!",
                text: "Your profile picture has been updated.",
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Upload Failed!",
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: "error",
            title: "Error Occurred!",
            text: "An error occurred. Please try again."
        });
    })
    .finally(() => {
        uploadButton.disabled = false;
        loader.style.display = "none"; // Hide loader after upload
        uploadText.innerHTML = "Upload";
    });
});