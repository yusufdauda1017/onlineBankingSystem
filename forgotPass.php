<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="icon" href="../asset/img/logo/logo.svg" type="image/svg">
 <title>Reset Password</title>
    <style>
        :root {
            --primary-color: #006400;
            --primary-hover-color: #008000;
            --background-color: #f8f9fa;
            --text-color: #333;
        }

        body {
            background: var(--background-color);
            height: 100vh;

            font-family: 'Arial', sans-serif;
        }
.wrapper{
/* Centering the card inside a wrapper instead of body */

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;


}
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
            .card h3{
                color: var(--primary-hover-color);
                padding: 1rem;
            }
        .form-control {
            border: 1px solid var(--primary-color);
        }

        .form-control:focus {
            border-color: var(--primary-hover-color);
            box-shadow: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            transition: background 0.3s ease;
        }
        .button-reset {
            background-color: var(--primary-color);
            border: none;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }

        .error {
            color: red;
            font-size: 14px;
        }



  /* The logo inside the spinner */

  .loader-container{
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: none; /* Initially hidden */
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.3); /* Semi-transparent background */
  z-index: 9999;
}

.show-loader{
  display: flex;
}



.logoees {
  width: 250px;
  height: 250px;
  animation: zoomInOut 2s ease-in-out infinite, ribbonRotate 3s ease-in-out infinite;
}

/* Zoom in/out effect on the logo */
@keyframes zoomInOut {
  0% {
      transform: scale(1);
  }
  50% {
      transform: scale(1.2); /* Zoom in */
  }
  100% {
      transform: scale(1); /* Zoom out */
  }
}

/* Ribbon-like rotation effect (spinning with 3D effect) */
@keyframes ribbonRotate {
  0% {
      transform: rotate(0deg) rotateX(0deg) rotateY(0deg);
  }
  25% {
      transform: rotate(90deg) rotateX(20deg) rotateY(10deg); /* Add slight perspective for 3D effect */
  }
  50% {
      transform: rotate(180deg) rotateX(0deg) rotateY(0deg);
  }
  75% {
      transform: rotate(270deg) rotateX(-20deg) rotateY(-10deg);
  }
  100% {
      transform: rotate(360deg) rotateX(0deg) rotateY(0deg);
  }
}


  /* Keyframes for smooth left-right shake (Error) */
  @keyframes shakeLeftRight {
    0%, 100% { transform: translateX(0); }
    15% { transform: translateX(-5px); }
    30% { transform: translateX(5px); }
    45% { transform: translateX(-4px); }
    60% { transform: translateX(4px); }
    75% { transform: translateX(-3px); }
    90% { transform: translateX(3px); }
}

/* Apply shake animation when error toast appears */
.shake-error {
    animation: shakeLeftRight 0.5s ease-in-out;
}

/* Custom Styling for Error Toast */
.custom-toast-error {
    background: #f8d7da !important;  /* Light red background */
    color: #721c24 !important;       /* Dark red text */
    border-left: 5px solid #dc3545 !important; /* Red accent */
    font-family: "Poppins", sans-serif !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    padding: 10px 15px !important;
    border-radius: 8px !important;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15) !important;
    text-align: center !important;
}

/* Keyframes for smooth left-right shake (Success) */
@keyframes shakeLeftRightSuccess {
    0%, 100% { transform: translateX(0); }
    15% { transform: translateX(-3px); }
    30% { transform: translateX(3px); }
    45% { transform: translateX(-2px); }
    60% { transform: translateX(2px); }
    75% { transform: translateX(-1px); }
    90% { transform: translateX(1px); }
}

/* Apply shake animation when success toast appears */
.shake-success {
    animation: shakeLeftRightSuccess 0.4s ease-in-out;
}

/* Custom Styling for Success Toast */
.custom-toast-success {
    background: #d4edda !important;  /* Light green background */
    color: #155724 !important;       /* Dark green text */
    border-left: 5px solid #28a745 !important; /* Green accent */
    font-family: "Poppins", sans-serif !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    padding: 10px 15px !important;
    border-radius: 8px !important;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15) !important;
    text-align: center !important;
}
    </style>
</head>
<body>

<!-- Loader Container -->
<div class="loader-container">
        <img src="./asset/img/logo/logo.svg" alt="Trustpoint Logo" class="logoees">
    </div>
<div class="wrapper">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                        <div class="card position-relative">
                        
                <!-- Back Arrow -->
                <a href="./login.php" class="text-success position-absolute" style="top: 50px; left: 30px;">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>

                <!-- Reset Password Header -->
                <h3>Reset Password</h3>

                <label for="email" class="form-label text-start">Enter your email</label>
                <input type="email" id="email" class="form-control py-3 mb-2" placeholder="example@mail.com">
                <div class="mt-2 error" id="errorDiv"></div>
                <button class="btn btn-success button-reset w-100 mt-3" onclick="sendResetLink()">Send Reset Link</button>
            </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>


function loaderShow() {
    const loader = document.querySelector(".loader-container"); // Ensure correct class
    if (loader) {
        loader.classList.add('show-loader');
    }
}

function loaderHidden() {
    const loader = document.querySelector(".loader-container"); // Ensure correct class
    if (loader) {
        loader.classList.remove('show-loader');
    }
}


function sendResetLink() {
    let emailInput = document.getElementById("email");
    let email = emailInput.value.trim();
    let errorDiv = document.getElementById("errorDiv");

    // Email Validation using RegEx
    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!email) {
        errorDiv.innerText = "Please enter your email.";
        errorDiv.style.color = "red";
        return;
    }

    if (!emailPattern.test(email)) {
        errorDiv.innerText = "Invalid email format. Please enter a valid email.";
        errorDiv.style.color = "red";
        return;
    }

    // Clear error message if email is valid
    errorDiv.innerText = "";

    loaderShow();

    fetch("./includes/send_reset_link.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        loaderHidden();

        if (data.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.classList.add('shake-error');
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'custom-toast-success',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
            });

            emailInput.value = ''; // Clear email input on success
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.classList.add('shake-error');
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'custom-toast-error',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
            });
        }
    })
    .catch(() => {
        loaderHidden();
        errorDiv.innerText = "An error occurred. Please try again.";
        errorDiv.style.color = "red";
    });
}

</script>

</body>
</html>
