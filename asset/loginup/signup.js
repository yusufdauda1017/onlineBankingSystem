let interval; // Declare interval globally to clear it when needed

function startTimer() {
    let display = document.getElementById("timer");
    let resendOtpBtn = document.getElementById("resendOtp");
    let currentEmail = document.getElementById("email").value; // Get the current email input

    resendOtpBtn.style.display = "none";

    let storedEmail = localStorage.getItem("otpEmail");
    let otpSentTime = localStorage.getItem("otpSentTime");
    let currentTime = Date.now(); 

    let cooldown = 120 * 1000; // 2 minutes in milliseconds

    // Stop any existing timer before starting a new one
    if (interval) {
        clearInterval(interval);
    }

    // If the email has changed, reset the timer and store new email
    if (storedEmail !== currentEmail) {
        localStorage.setItem("otpEmail", currentEmail);
        localStorage.setItem("otpSentTime", currentTime.toString());
        otpSentTime = currentTime.toString();
    }

    if (!otpSentTime) {
        display.innerHTML = "";
        resendOtpBtn.style.display = "block";
        return;
    }

    let elapsedTime = currentTime - parseInt(otpSentTime);
    let remainingTime = cooldown - elapsedTime;

    if (remainingTime <= 0) {
        display.innerHTML = "";
        resendOtpBtn.style.display = "block";
        return;
    }

    function updateTimer() {
        if (remainingTime <= 0) {
            clearInterval(interval);
            display.innerHTML = "";
            resendOtpBtn.style.display = "block";
            return;
        }

        let minutes = Math.floor(remainingTime / 60000);
        let seconds = Math.floor((remainingTime % 60000) / 1000);
        display.innerHTML = `Time Remaining: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

        remainingTime -= 1000;
    }

    interval = setInterval(updateTimer, 1000);
    updateTimer();
}

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

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'info',
                title: 'Session Expired',

                customClass: {
                    popup: 'custom-toast', // Uses the toast styling
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
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
            event.preventDefault(); // Prevent form submission

            // Get input values
            const firstName = document.getElementById('firstName').value.trim();
            const surname = document.getElementById('surName').value.trim();
            const otherName = document.getElementById('otherName').value.trim();
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const email = document.getElementById('email').value.trim();
            const dob = document.getElementById('dob').value;
            const gender = document.getElementById('gender').value.trim();

            let isValid = true;

            // Function to show errors
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

                // Clear error when input is corrected
                element.addEventListener('input', () => {
                    element.classList.remove('is-invalid');
                    errorDiv.style.display = 'none';
                });
            };

            // Validate First Name
            if (firstName.length < 2) {
                showError(document.getElementById('firstName'), 'First name must be at least 2 characters long.');
                isValid = false;
            }

            // Validate Surname
            if (surname.length < 2) {
                showError(document.getElementById('surName'), 'Surname must be at least 2 characters long.');
                isValid = false;
            }

          // Validate Phone Number
            if (!/^(\+234|0)[789][01]\d{8}$/.test(phoneNumber)) {
                showError(document.getElementById('phoneNumber'), 'Enter a valid phone number');
                isValid = false;
            }
            // Validate Email
            const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            if (!validateEmail(email)) {
                showError(document.getElementById('email'), 'Please enter a valid email address.');
                isValid = false;
            }

            // Validate Date of Birth
            if (!dob) {
                showError(document.getElementById('dob'), 'Please enter your date of birth.');
                isValid = false;
            } else {
                const dobDate = new Date(dob);
                const today = new Date();

                if (isNaN(dobDate.getTime())) {
                    showError(document.getElementById('dob'), 'Invalid date format.');
                    isValid = false;
                } else if (dobDate > today) {
                    showError(document.getElementById('dob'), 'Date of birth cannot be in the future.');
                    isValid = false;
                } else {
                    let age = today.getFullYear() - dobDate.getFullYear();
                    const monthDifference = today.getMonth() - dobDate.getMonth();
                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dobDate.getDate())) {
                        age--;
                    }

                    if (age < 16) {
                        showError(document.getElementById('dob'), 'You must be at least 16 years old to register.');
                        isValid = false;
                    }
                }
            }

            // Validate Gender
            if (!gender || gender === "Select Gender") {
                showError(document.getElementById('gender'), 'Please select a gender.');
                isValid = false;
            }

            // If all fields are valid, proceed
            if (isValid) {
                saveFormData(); // Ensure saveFormData() function is defined

                // Show confirmation modal
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('displayNumber').textContent = phoneNumber;
                document.getElementById('displayEmail').textContent = email;
                modal.show();

                // Update history state
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

            const form = document.getElementById('signupForm');
            if (!form) {
                console.error("Form element not found!");
                return;
            }

            const formData = new FormData(form);
            formData.append('type', 'generate_otp');

            const modalElement = document.getElementById('confirmModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }

            loaderShow();

            fetch('./includes/verification.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                loaderHidden();

                if (data.success) {

                    handleOTPSuccess(data.otpSentTime);

                    
                } else {
                   loaderHidden();
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
            .catch(error => {
                loaderHidden();
                

                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something went wrong!',
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
            });
        });
    }
}

// Call the function after defining it
handleVerify();

function handleOTPSuccess(otpSentTime) {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'OTP Sent Successfully!',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-success');
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        customClass: {
            popup: 'custom-toast-success',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
    });

    // Ensure elements exist before modifying them
    const formInput = document.getElementById('formInput');
    const verification = document.getElementById('verification');
    const header = document.getElementById('header');

    if (formInput) formInput.classList.add('hidden');
    if (verification) verification.classList.remove('hidden');
    if (header) header.classList.add('hidden');

    history.pushState({ step: 2 }, 'Step 2', '?step=2');
// Store email and OTP time
    localStorage.setItem("otpEmail", email);
    localStorage.setItem("otpSentTime", Date.now().toString());

    startTimer();
}

window.onload = function () {
    if (localStorage.getItem("otpSentTime")) {
        startTimer();
    }
};

function handleResendOtp() {
    const resendAttempts = parseInt(localStorage.getItem('resendAttempts') || 3);

    if (resendAttempts <= 0) {
        // ❌ ERROR Toast (with shake)
        const errorToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.classList.add('shake-error'); // Apply left-right shake effect
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        errorToast.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Limit Exceeded!',
            customClass: {
                popup: 'custom-toast-error',
                title: 'custom-toast-title',
                icon: 'custom-toast-icon'
            }
        });

        return;
    }

    const formData = new FormData(document.getElementById('signupForm'));
    formData.append('type', 'resend_otp');

    loaderShow();

    fetch('./includes/verification.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        loaderHidden();

        if (data.success) {
            // Success Toast
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'OTP Resent Successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.classList.add('shake-success'); // Apply left-right shake effect
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'custom-toast-success',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
            });

            localStorage.setItem('resendAttempts', (resendAttempts - 1).toString());

            // Store new OTP time and restart timer
            localStorage.setItem("otpEmail", email);
            localStorage.setItem("otpSentTime", Date.now().toString());
            startTimer();
        } else {
            // ❌ ERROR Toast (with shake)
            const errorToast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.classList.add('shake-error'); // Apply left-right shake effect
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            errorToast.fire({
                icon: 'error',
                title: 'Oops!',
                text: data.message || 'Something went wrong!',
                customClass: {
                    popup: 'custom-toast-error',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
            });
        }
    })
    .catch(error => {
        loaderHidden();
        const errorToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.classList.add('shake-error'); // Apply left-right shake effect
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        errorToast.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Something went wrong!',
            customClass: {
                popup: 'custom-toast-error',
                title: 'custom-toast-title',
                icon: 'custom-toast-icon'
            }
        });
    });
}

document.getElementById('resendOtp').addEventListener('click', function (event) {
    event.preventDefault();
    handleResendOtp();
});
















function verifyOTP() {
    const inputs = document.querySelectorAll('.verify-box');
    let otp = '';

    inputs.forEach((input) => {
        otp += input.value.trim();
    });

    if (otp.length < inputs.length) {
         // ❌ ERROR Toast (with shake)
    const errorToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-error'); // Apply left-right shake effect
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    errorToast.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Something went wrong!',
        customClass: {
            popup: 'custom-toast-error',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
    });


        return;
    }

    // Get the form data from localStorage
    const formData = localStorage.getItem('formData') ? JSON.parse(localStorage.getItem('formData')) : null;
    loaderShow();
    fetch('./includes/verification.php', {
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

            const successToast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.classList.add('shake-success'); // Apply left-right shake effect
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            successToast.fire({
                icon: 'success',
                title: 'Success!',
                text: 'OTP Verify Successful!',
                customClass: {
                    popup: 'custom-toast-success',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                }
            });
            document.getElementById('verification').classList.add('hidden');
            document.getElementById('createPass').classList.remove('hidden');
            document.getElementById('header').classList.add('hidden');
            history.pushState({ step: 3 }, 'Step 3', '?step=3');
        } else {
            // ❌ ERROR Toast (with shake)
    const errorToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-error'); // Apply left-right shake effect
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    errorToast.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Something went wrong!',
        customClass: {
            popup: 'custom-toast-error',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
    });


        }
    })
    .catch((error) => {
        loaderHidden();
         // ❌ ERROR Toast (with shake)
    const errorToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-error'); // Apply left-right shake effect
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    errorToast.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Something went wrong!',
        customClass: {
            popup: 'custom-toast-error',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
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
        loaderShow();
        fetch("./includes/verification.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type: "create_pass", password }),
        })
        .then(response => response.json())
        .then(data => {

            loaderHidden();
            if (data.success) {
                localStorage.removeItem('formData');
                const successToast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.classList.add('shake-success'); // Apply left-right shake effect
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                successToast.fire({
                    icon: 'success',
                    title: 'Congratulations',
                    text: 'You will be redirected to dashboard',
                    customClass: {
                        popup: 'custom-toast-success',
                        title: 'custom-toast-title',
                        icon: 'custom-toast-icon'
                    }
                }).then(() => {
                    // Redirect after toast disappears
                    window.location.href = '.././User/index.php';
                });

            } else {
                 // ❌ ERROR Toast (with shake)
    const errorToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-error'); // Apply left-right shake effect
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    errorToast.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Something went wrong!',
        customClass: {
            popup: 'custom-toast-error',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
    });


            }
        })
        .catch(error => {
            loaderHidden();
            // ❌ ERROR Toast (with shake)
    const errorToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.classList.add('shake-error'); // Apply left-right shake effect
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    errorToast.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Something went wrong!',
        customClass: {
            popup: 'custom-toast-error',
            title: 'custom-toast-title',
            icon: 'custom-toast-icon'
        }
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
    window.location.href = '/register.php';
}

function checkInactivityAndReset() {
    const inactivityTimeout = 20 * 60 * 1000; // 5 minutes (converted to milliseconds)
    let inactivityTimer;

    const resetTimer = () => {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            // Clear local storage
            localStorage.removeItem('formData');
            localStorage.removeItem('resendAttempts');
            localStorage.removeItem('remainingTime');

            // Redirect to login page
            window.location.href = '/register.php';
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
