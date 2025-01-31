function loaderShow() {
    const loader = document.querySelector(".loaderr-containerrr");
    loader.classList.add('show-loader');
}

function loaderHidden() {
    const loader = document.querySelector(".loaderr-containerrr");
    loader.classList.remove('show-loader');
}

(function collectForm() {
    document.addEventListener('DOMContentLoaded', () => {
        let inactivityTimeout;

        const resetForm = () => {
            localStorage.removeItem('formData');
            document.getElementById('signupForm').reset();
           showStep(1);
            Swal.fire({
                icon: 'info',
                title: 'Session Expired',
                text: 'Your session has expired due to inactivity. Please start over.',
            });
        };

        const resetInactivityTimer = () => {
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(resetForm, 180000); // 3 minutes
        };

        const activityEvents = ['mousemove', 'keydown', 'click', 'scroll'];
        activityEvents.forEach(event => {
            document.addEventListener(event, resetInactivityTimer, { passive: true });
        });

        resetInactivityTimer();

        const showStep = (step) => {
            document.getElementById('formInput').classList.add('hidden');
            document.getElementById('verification').classList.add('hidden');
            document.getElementById('createPass').classList.add('hidden');

            switch (step) {
                case 1:
                    document.getElementById('formInput').classList.remove('hidden');
                    break;
                case 2:
                    document.getElementById('verification').classList.remove('hidden');
                    break;
                case 3:
                    document.getElementById('createPass').classList.remove('hidden');
                    break;
                default:
                    document.getElementById('formInput').classList.remove('hidden');
            }
        };

        const urlParams = new URLSearchParams(window.location.search);
        const currentStep = parseInt(urlParams.get('step')) || 1;
        showStep(currentStep);

        window.onpopstate = function (event) {
            if (event.state) {
                const step = event.state.step;
                showStep(step);
            }
        };

        const saveFormData = () => {
            const formData = {
                firstName: document.getElementById('firstName').value.trim(),
                surname: document.getElementById('surName').value.trim(),
                otherName: document.getElementById('otherName').value.trim(),
                phoneNumber: document.getElementById('phoneNumber').value.trim(),
                email: document.getElementById('email').value.trim(),
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
            };
            localStorage.setItem('formData', JSON.stringify(formData));
        };

        const repopulateForm = () => {
            const savedData = localStorage.getItem('formData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                document.getElementById('firstName').value = formData.firstName;
                document.getElementById('surName').value = formData.surname;
                document.getElementById('otherName').value = formData.otherName;
                document.getElementById('phoneNumber').value = formData.phoneNumber;
                document.getElementById('email').value = formData.email;
                document.getElementById('dob').value = formData.dob;
                document.getElementById('gender').value = formData.gender;
            }
        };

        repopulateForm();

        document.getElementById('signupForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const firstName = document.getElementById('firstName').value.trim();
            const surname = document.getElementById('surName').value.trim();
            const otherName = document.getElementById('otherName').value.trim();
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const email = document.getElementById('email').value.trim();
            const dob = document.getElementById('dob').value;
            const gender = document.getElementById('gender').value;

            let isValid = true;

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
                    element.classList.remove('is-invalid');
                    errorDiv.style.display = 'none';
                }, 5000);
            };

            if (firstName.length < 2) {
                showError(document.getElementById('firstName'), 'First name must be at least 2 characters long.');
                isValid = false;
            }
            if (surname.length < 2) {
                showError(document.getElementById('surName'), 'Surname must be at least 2 characters long.');
                isValid = false;
            }
            if (!/^[0-9]{10,11}$/.test(phoneNumber)) {
                showError(document.getElementById('phoneNumber'), 'Phone number must be 10 to 11 digits.');
                isValid = false;
            }
            const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            if (!validateEmail(email)) {
                showError(document.getElementById('email'), 'Please enter a valid email address.');
                isValid = false;
            }
            if (!dob) {
                showError(document.getElementById('dob'), 'Please enter your date of birth.');
                isValid = false;
            }
            if (!gender) {
                showError(document.getElementById('gender'), 'Please select a gender.');
                isValid = false;
            }

            if (isValid) {
                saveFormData();
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('displayNumber').textContent = phoneNumber;
                document.getElementById('displayEmail').textContent = email;
                modal.show();
                history.pushState({ step: 1 }, 'Step 1', '?step=1');
            }
        });
    });
})();

function handleVerify() {
    const confirmBtn = document.getElementById('confirmBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function (event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('signupForm'));
            formData.append('type', 'generate_otp');
            const modalElement = document.getElementById('confirmModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            loaderShow();

            fetch('http://localhost/onlineBankingSystem/verification.php', {
                method: 'POST',
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                loaderHidden();
                if (data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "success",
                        title: "OTP Sent Successful"
                      });
                    document.getElementById('formInput').classList.add('hidden');
                    document.getElementById('verification').classList.remove('hidden');
                    document.getElementById('header').classList.add('hidden');
                    history.pushState({ step: 2 }, 'Step 2', '?step=2');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred.',
                    });
                }
            })
            .catch((error) => {
                loaderHidden();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request. Please try again.',
                });
            });
        });
    }
}
handleVerify();

let timerInterval;
const MAX_RESEND_ATTEMPTS = 3; // Maximum number of OTP resend attempts
let resendAttempts = parseInt(localStorage.getItem('resendAttempts')) || MAX_RESEND_ATTEMPTS; // Load resend attempts from localStorage
let remainingTime = parseInt(localStorage.getItem('remainingTime')) || 120; // Load remaining time from localStorage

// Function to start or resume the timer
function startTimer(duration, display) {
    let timer = duration, minutes, seconds;
    timerInterval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        // Save the remaining time to localStorage
        localStorage.setItem('remainingTime', timer);

        if (--timer < 0) {
            clearInterval(timerInterval);
            localStorage.removeItem('remainingTime'); // Clear the timer from localStorage
            document.getElementById('resendOtp').style.display = 'inline'; // Show the resend link
        }
    }, 1000);
}

// Function to handle OTP resend
function handleResendOtp() {
    if (resendAttempts <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Limit Exceeded',
            text: 'You have exceeded the maximum number of OTP resend attempts.',
        });
        return;
    }

    const formData = new FormData(document.getElementById('signupForm'));
    formData.append('type', 'resend_otp');

    fetch('http://localhost/onlineBankingSystem/verification.php', {
        method: 'POST',
        body: formData,
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'OTP Resent',
                text: 'A new OTP has been sent to your email.',
            });

            // Decrease the resend attempts and save to localStorage
            resendAttempts--;
            localStorage.setItem('resendAttempts', resendAttempts);

            // Reset the timer
            clearInterval(timerInterval);
            remainingTime = 120;
            localStorage.setItem('remainingTime', remainingTime);
            startTimer(remainingTime, document.getElementById('timer'));

            // Hide the resend link until the timer expires
            document.getElementById('resendOtp').style.display = 'none';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to resend OTP.',
            });
        }
    })
    .catch((error) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while resending OTP.',
        });
    });
}

// Event listener for the resend OTP link
document.getElementById('resendOtp').addEventListener('click', function (event) {
    event.preventDefault();
    handleResendOtp();
});

// Initialize the timer when the page loads
window.addEventListener('load', function () {
    const timerDisplay = document.getElementById('timer');
    if (remainingTime > 0) {
        startTimer(remainingTime, timerDisplay);
        document.getElementById('resendOtp').style.display = 'none'; // Hide resend link if timer is still running
    } else {
        timerDisplay.textContent = "00:00";
        document.getElementById('resendOtp').style.display = 'inline'; // Show resend link if timer has expired
    }

    // Disable resend link if no attempts left
    if (resendAttempts <= 0) {
        document.getElementById('resendOtp').style.pointerEvents = 'none';
        document.getElementById('resendOtp').style.color = 'gray'; // Visual indication that resend is disabled
    }
});
function verifyOTP() {
    const inputs = document.querySelectorAll('.verify-box');
    let otp = '';

    inputs.forEach((input) => {
        otp += input.value.trim();
    });

    if (otp.length < inputs.length) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please complete the OTP before submitting.',
        });
        return;
    }

    // Get the form data from localStorage
    const formData = localStorage.getItem('formData') ? JSON.parse(localStorage.getItem('formData')) : null;
    loaderShow();
    fetch('http://localhost/onlineBankingSystem/verification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            type: 'verify_otp',
            otp: otp,
            email: formData ? formData.email : null,
            formData: formData // Send the entire form data to store in the session
        }),
    })
    .then(response => response.json())
    .then(data => {
        loaderHidden();
        if (data.success) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                  toast.onmouseenter = Swal.stopTimer;
                  toast.onmouseleave = Swal.resumeTimer;
                }
              });
              Toast.fire({
                icon: "success",
                title: "OTP Verify Successful"
              });
            document.getElementById('verification').classList.add('hidden');
            document.getElementById('createPass').classList.remove('hidden');
            document.getElementById('header').classList.add('hidden');
            history.pushState({ step: 3 }, 'Step 3', '?step=3');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'An error occurred.',
            });
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
    });
}

document.getElementById('verifyButton').addEventListener('click', verifyOTP);

const inputs = document.querySelectorAll('.verify-box');
inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value;

        if (!/^\d$/.test(value)) {
            e.target.value = '';
            return;
        }

        if (index < inputs.length - 1 && value !== '') {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasteData = e.clipboardData.getData('text').trim();
        if (/^\d{4}$/.test(pasteData)) {
            inputs.forEach((input, idx) => {
                input.value = pasteData[idx] || '';
            });
            inputs[inputs.length - 1].focus();
        }
    });
});

function creatingPassWord() {
    const togglePasswords = document.querySelectorAll('.togglepass');
    const passwords = document.querySelectorAll('.creatingPasswordss');

    togglePasswords.forEach((togglePassword, index) => {
        togglePassword.addEventListener('click', () => {
            const password = passwords[index];
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
            togglePassword.classList.toggle('fa-eye');
        });
    });

    document.getElementById("createForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const passwordError = document.getElementById("passwordError");
        const confirmPasswordError = document.getElementById("confirmPasswordError");
        const termsCheckbox = document.getElementById("termsCheckbox");
        const errorMessage = document.getElementById("error-message");
        const errorMessageAll = document.querySelectorAll(".error-message");

        passwordError.textContent = "";
        confirmPasswordError.textContent = "";
        errorMessageAll.forEach((errorMessage) => {
            errorMessage.style.display = "block";
        });

        let isValid = true;

        if (password.length < 6) {
            passwordError.textContent = "Password must be at least 6 characters.";
            isValid = false;
        }

        if (password !== confirmPassword) {
            confirmPasswordError.textContent = "Passwords do not match.";
            isValid = false;
        }

        if (!termsCheckbox.checked) {
            errorMessage.textContent = "You must accept the Terms & Conditions to proceed!";
            isValid = false;
        }

        if (isValid) {
            passwordError.textContent = "";
            confirmPasswordError.textContent = "";
            errorMessage.textContent = "";
            verified(password);
        }
    });

    function verified(password) {
        console.log("Sending data:", { type: "create_pass", password });
        loaderShow();
        fetch("http://localhost/onlineBankingSystem/verification.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type: "create_pass", password }),
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response data:", data);
            loaderHidden();
            if (data.success) {
                localStorage.removeItem('formData');
                Swal.fire({
                    icon: 'success',
                    title: 'Congratulations',
                    text: 'Welcome to Trustpoint',
                    timer: 2000,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.href = './User/index.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred.',
                });
            }
        })
        .catch(error => {
            loaderHidden();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error processing your request. Please try again',
            });
        });
    }
}
creatingPassWord();


function resetFormAndStorage() {
    // Clear local storage
    localStorage.removeItem('formData');
    localStorage.removeItem('resendAttempts');
    localStorage.removeItem('remainingTime');

    // Reset the form fields
    document.getElementById('signupForm').reset();

    // Redirect to the initial signup page
    window.location.href = 'http://localhost/onlineBankingSystem/signup.php';
}

function checkInactivityAndReset() {
    const inactivityTimeout = 60000; // 1 minute (matches PHP timeout)
    let inactivityTimer;

    const resetTimer = () => {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            // Clear local storage
            localStorage.removeItem('formData');
            localStorage.removeItem('resendAttempts');
            localStorage.removeItem('remainingTime');

            // Redirect to login page
            window.location.href = '../login-form/index.php';
        }, inactivityTimeout);
    };

    // Reset the timer on user activity
    const activityEvents = ['mousemove', 'keydown', 'click', 'scroll'];
    activityEvents.forEach(event => {
        document.addEventListener(event, resetTimer, { passive: true });
    });

    // Initialize the timer
    resetTimer();
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', checkInactivityAndReset);