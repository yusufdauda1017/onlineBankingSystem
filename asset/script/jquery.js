$(document).ready(function() {
    let currentStep = 0;
    const steps = $(".form-step");
    
    function showStep(stepIndex) {
        steps.removeClass("active");
        steps.eq(stepIndex).addClass("active");
    }
    
    $("#nextStep").click(function() {
        var name = $("#name").val().trim();
        var email = $("#email").val().trim();
    
        if (name && email) {
            currentStep = 1;
            showStep(currentStep);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Details',
                text: 'Please fill in all required fields before proceeding.'
            });
        }
    });
    
    $("#prevStep").click(function() {
        currentStep = 0;
        showStep(currentStep);
    });
    
    $("#submitForm").click(function(e) {
        e.preventDefault();
    
        var name = $("#name").val().trim();
        var email = $("#email").val().trim();
        var subject = $("#subject").val().trim();
        var message = $("#message").val().trim();
    
        if (name && email && subject && message) {
            var formData = {
                name: name,
                email: email,
                subject: subject,
                message: message
            };
    
            // Add Loader (Spinner) inside the Button
            $("#submitForm")
                .html('<i class="fa fa-spinner fa-spin"></i> Sending...')
                .prop("disabled", true);
    
            $.ajax({
                url: "https://trustpoint.wuaze.com/asset/script/process_form.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Message Sent!",
                            text: "Your message has been sent successfully.",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $("#contactForm")[0].reset();
                        showStep(0); // Reset to first step
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: response.message || "Something went wrong. Please try again."
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred while sending your message. Please try again."
                    });
                },
                complete: function() {
                    // Reset Button after AJAX completes
                    $("#submitForm")
                        .html("Submit")
                        .prop("disabled", false);
                }
            });
        } else {
            Swal.fire({
                icon: "warning",
                title: "Missing Fields",
                text: "Please fill in all fields before submitting."
            });
        }
    });
    
    showStep(currentStep);
    });



