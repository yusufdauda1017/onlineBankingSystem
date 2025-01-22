function colletForm() {
        document.addEventListener('DOMContentLoaded', () => {
    // Handle form submission
    document.getElementById('signupForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Collect form data
        const firstName = document.getElementById('firstName').value.trim();
        const surname = document.getElementById('surName').value.trim();
        const otherName = document.getElementById('otherName').value.trim();
        const phoneNumber = document.getElementById('phoneNumber').value.trim();
        const email = document.getElementById('email').value.trim();
        const dob = document.getElementById('dob').value;
        const gender = document.getElementById('gender').value;

        let isValid = true; // Track overall form validity

        // Helper function to show errors
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

        // Validation checks
        if (firstName.length < 2) {
            showError(document.getElementById('firstName'), 'First name must be at least 2 characters long.');
            isValid = false;
        }
        if (surname.length < 2) {
            showError(document.getElementById('surName'), 'Surname must be at least 2 characters long.');
            isValid = false;
        }
        if (!/^[0-9]{10,15}$/.test(phoneNumber)) {
            showError(document.getElementById('phoneNumber'), 'Phone number must be 10 to 15 digits.');
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

        // If all validations pass, show confirmation modal
        if (isValid) {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('displayNumber').textContent = phoneNumber;
            document.getElementById('displayEmail').textContent = email;
            modal.show();
        }
    });

    // Confirm button event listener
});
}
colletForm();

function handleVerify() {


    document.getElementById('confirmBtn').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default behavior
        const formData = new FormData(document.getElementById('signupForm'));

        // Debugging: Log form data for verification
        console.log("Sending form data:", [...formData.entries()]);

        fetch('verification.php', {
            method: 'POST',
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Parsed response data:", data);

                if (data.success) {
                    console.log("Request successful!");

                    // Close the modal properly
                    const modalElement = document.getElementById('confirmModal');
                    const modal = bootstrap.Modal.getInstance(modalElement); // Get the Bootstrap modal instance
                    modal.hide(); // Hide the modal
                    document.getElementById('formInput').classList.add('hidden');

                    // Proceed to the verification screen
                    document.getElementById('verification').classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred.',
                    });
                }
            })
            .catch((error) => {
                console.error("Fetch Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request. Please try again.',
                });
            });
    });

// Ensure inputs only accept numbers and auto-move focus for OTP inputs
 const inputs = document.querySelectorAll('.verify-box');

 inputs.forEach((input, index) => {
     input.addEventListener('input', (e) => {
         const value = e.target.value;

         // If non-digit character is entered, clear the input
         if (!/^\d$/.test(value)) {
             e.target.value = '';
             return;
         }

         // Move to the next input if there's a next one
         if (index < inputs.length - 1) {
             inputs[index + 1].focus();
         }
     });

     input.addEventListener('keydown', (e) => {
         // Handle backspace key to move focus to previous input
         if (e.key === 'Backspace' && !input.value && index > 0) {
             inputs[index - 1].focus();
         }
     });
 });
}
handleVerify();


function creatingPassWord (){

    // Password toggle functionality
  const togglePasswords = document.querySelectorAll('.toggle');
  const passwords = document.querySelectorAll('.form-control');


  togglePasswords.forEach((togglePassword, index) => {
  togglePassword.addEventListener('click', () => {
  const password = passwords[index];

  const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
  password.setAttribute('type', type);

  // Toggle the icon class
  togglePassword.classList.toggle('fa-eye-slash');
  togglePassword.classList.toggle('fa-eye');
  });
  });

  // Form Validation
  document.getElementById("createForm").addEventListener("submit", function (event) {
  event.preventDefault(); // Prevent form submission

  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const passwordError = document.getElementById("passwordError");
  const confirmPasswordError = document.getElementById("confirmPasswordError");
  const termsCheckbox = document.getElementById("termsCheckbox");
  const errorMessage = document.getElementById("error-message");

  passwordError.textContent = "";
  confirmPasswordError.textContent = "";

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
  // Proceed with form submission (or you can enable actual submission here)
  }
  });
  }

creatingPassWord();