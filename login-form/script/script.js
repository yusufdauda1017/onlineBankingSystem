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

function collectForm() {
    document.addEventListener('DOMContentLoaded', () => {
        // Handle form submission
        document.getElementById('signinForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Collect form data
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            let email = emailInput.value.trim();
            let password = passwordInput.value.trim();

            let isValid = true; // Track overall form validity

            // Helper function to show errors and clear input
            const showError = (element, message) => {
                element.classList.add('is-invalid');
                element.value = ''; // Clear input field on error
                let errorDiv = element.nextElementSibling;

                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    element.parentNode.appendChild(errorDiv);
                }

                errorDiv.textContent = message;
                errorDiv.style.display = 'block';

                setTimeout(() => {
                    errorDiv.style.display = 'none';
                    element.classList.remove('is-invalid');
                }, 5000);
            };

            // Clear previous errors
            const clearError = (element) => {
                element.classList.remove('is-invalid');
                let errorDiv = element.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            };

            // Email validation
            const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            if (!validateEmail(email)) {
                showError(emailInput, 'Please enter a valid email address.');
                isValid = false;
            } else {
                clearError(emailInput);
            }

            // Password validation
            if (password.length < 4) {
                showError(passwordInput, 'Password must be at least 4 characters long.');
                isValid = false;
            } else {
                clearError(passwordInput);
            }

            // If all validations pass, process form submission
            if (isValid) {
                loaderShow();
                fetch('../login.php', {
                    method: 'POST',
                    body: JSON.stringify({ email, password }), // Directly pass email and password
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    loaderHidden();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'Redirecting to your dashboard...',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = data.redirect;


                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'An error occurred.',
                            timer: 1500,
                            timerProgressBar: true,
                        });

                        // Clear fields on server-side error
                        emailInput.value = '';
                        passwordInput.value = '';
                    }
                })
                .catch((error) => {
                    loaderHidden();
                    console.error('Fetch Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request. Please try again.',
                    });

                    // Clear fields on network error
                    emailInput.value = '';
                    passwordInput.value = '';
                });
            }

        });
    });
}

// Call the function
collectForm();
