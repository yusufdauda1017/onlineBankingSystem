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
        document.getElementById('signinForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberMeInput = document.querySelector('#rememberMeCheckbox'); // Select checkbox

            let email = emailInput.value.trim();
            let password = passwordInput.value.trim();
            let requestBody = { email, password }; // Base request body

            if (rememberMeInput.checked) {
                requestBody.rememberMe = true; // ✅ Add only if checked
            }

            let isValid = true;

            // Validation Functions
            const showError = (element, message) => {
                element.classList.add('is-invalid');
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

            const clearError = (element) => {
                element.classList.remove('is-invalid');
                let errorDiv = element.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            };

            const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            if (!validateEmail(email)) {
                showError(emailInput, 'Please enter a valid email address.');
                isValid = false;
            } else {
                clearError(emailInput);
            }

            if (password.length < 4) {
                showError(passwordInput, 'Password must be at least 4 characters long.');
                isValid = false;
            } else {
                clearError(passwordInput);
            }

            if (isValid) {
                loaderShow();

                fetch('../login-form/login.php', {
                    method: 'POST',
                    body: JSON.stringify(requestBody), // ✅ Use requestBody instead
                    headers: { 'Content-Type': 'application/json' },
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
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'An error occurred.' });
                        emailInput.value = '';
                        passwordInput.value = '';
                    }
                })
                .catch((error) => {
                    loaderHidden();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request. Please try again.',
                    });
                    emailInput.value = '';
                    passwordInput.value = '';
                });
            }
        });
    });
}
collectForm();

