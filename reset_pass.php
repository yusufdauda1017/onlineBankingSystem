<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" href="./asset/img/logo/logo.svg" type="image/svg">
      <title>Create New Password</title>
    <style>
 :root {
            --primary-color: #006400;
            --primary-hover-color: #008000;
            --background-color: #f8f9fa;
            --text-color: #333;
        }

        body {
            background: var(--background-color);
            font-family: 'Arial', sans-serif;
            height: 100vh;
        }

        /* Centering the card inside a wrapper instead of body */
        .wrapper {
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
        }

        .card h3 {
            text-align: center;
            color: var(--primary-color);
            padding: 1rem;
        }

        .form-control {
            border: 1px solid var(--primary-color);
        }

        .form-control:focus {
            border-color: var(--primary-hover-color);
            box-shadow: none;
        }

        .btn-primary, .button-reset {
            background-color: var(--primary-color);
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover, .button-reset:hover {
            background-color: var(--primary-hover-color);
        }

        .password-toggle {
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
/* Loader Container */
.loader-container {
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

/* Show Loader */
.show-loader {
    display: flex;
}

/* Logo Animation */
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
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

/* Ribbon-like rotation effect */
@keyframes ribbonRotate {
    0% {
        transform: rotate(0deg) rotateX(0deg) rotateY(0deg);
    }
    25% {
        transform: rotate(90deg) rotateX(20deg) rotateY(10deg);
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

    </style>
</head>
<body>

<!-- Loader Container -->
<div class="loader-container">
        <img src="./asset/img/logo/logo.svg" alt="Trustpoint Logo" class="logoees">
    </div>


    <!-- Wrapper for Centering -->
    <div class="wrapper">
        <div class="container-sm">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card">
                        <h3>Create New Password</h3>

                        <input type="hidden" id="token">

                        <label for="new-password" class="form-label mt-2">New Password</label>
                        <div class="input-group">
                            <input type="password" id="new-password" class="form-control py-2" placeholder="Enter new password">
                            <span class="input-group-text password-toggle" onclick="togglePassword('new-password')">👁️</span>
                        </div>

                        <label for="confirm-password" class="form-label mt-3">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="confirm-password" class="form-control py-2" placeholder="Confirm new password">
                            <span class="input-group-text password-toggle" onclick="togglePassword('confirm-password')">👁️</span>
                        </div>

                        <div class="error" id="errorDiv"></div>

                        <button class="btn btn-success button-reset w-100 mt-3" onclick="resetPassword()">Reset Password</button>
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

    function togglePassword(id) {
        let input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }

    // Extract token from URL
    document.addEventListener("DOMContentLoaded", function() {
        let urlParams = new URLSearchParams(window.location.search);
        let token = urlParams.get("token");
        if (token) {
            document.getElementById("token").value = token;
        }
    });

    function resetPassword() {
    let token = document.getElementById("token").value;
    let newPassword = document.getElementById("new-password").value.trim();
    let confirmPassword = document.getElementById("confirm-password").value.trim();
    let errorDiv = document.getElementById("errorDiv");

    errorDiv.innerText = "";

    if (!newPassword || !confirmPassword) {
        errorDiv.innerText = "Please fill all fields.";
        return;
    }
    if (newPassword !== confirmPassword) {
        errorDiv.innerText = "Passwords do not match!";
        return;
    }
    loaderShow();
    fetch("./includes/reset_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `token=${encodeURIComponent(token)}&password=${encodeURIComponent(newPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        loaderHidden();
        if (data.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Successful',
                text: data.message,
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "./login.php";
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'An error occurred while resetting the password.',
            });
        }
    })
    .catch(() => {
        loaderHidden();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.',
        });
    });
}

</script>

</body>
</html>
