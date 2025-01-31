<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1d6525ef6a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="asset/bootstrap-5.0.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./asset/css/signup.css">
    <link rel="icon" href="./img/logo/logo.svg" type="image/svg">
    <title>Signup || Trustpoint</title>
</head>
<body>


    <!-- Loader Container -->
    <div class="loaderr-containerrr">
        <img src="./img/logo/logo.svg" alt="Trustpoint Logo" class="logoees">
    </div>
<section id="formInput">
<!-- Header Section -->
<header id="header">
        <div class="container d-flex align-items-center justify-content-between">
            <img src="img/logo/logo-1.png" alt="Trustpoint Logo" class="img-fluid" style="max-width: 150px;">
            <div class="text-end">
                <p>Already have an account? <a href="./login-form/index.php" class="btn btn-success px-4 mx-3">Log In</a></p>
            </div>
        </div>
    </header>

 <div class="container content step1" >
        <div class="row w-100">
            <!-- Left side with text -->
            <div class="col-sm-6 text-left logo-container">
                <p>STEP 1 of 4</p>
                <h2>Welcome, Let’s get you started</h2>
                <p class="inner-text">In this step, we'll guide you through setting up your account. Please follow the instructions carefully.</p>
            </div>

            <!-- Right side with Form -->
            <div class="col-sm-6 form-logo">
                <div class="form-container w-100">
                    <div class="form-group">
                        <form action="#" id="signupForm">
                            <!-- Surname Input -->
                            <div class="mb-3 col-sm-12">
                                <label for="sname" class="form-label mb-2">Surname: </label>
                                <input type="text" class="form-control py-3" id="surName" placeholder="Enter your Surname" name="sName" maxlength="20" title="Surname must not exceed 20 characters">
                                <div class="error-message"></div>
                            </div>

                            <div class="row">
                                <!-- First Name Input -->
                                <div class="mb-3 col-sm-6">
                                    <label for="fname" class="form-label mb-2">First name: </label>
                                    <input type="text" id="firstName" class="form-control py-3" placeholder="Enter your first name" name="fName" maxlength="20" title="First name must not exceed 20 characters">
                                    <div class="error-message"></div>
                                </div>

                                <!-- Other Name Input (Optional) -->
                                <div class="mb-3 col-sm-6">
                                    <label for="othername" class="form-label mb-2">Other Name: (Optional) </label>
                                    <input type="text" id="otherName" class="form-control py-3" placeholder="Enter your Other Name" name="otherName" maxlength="50" title="Middle name must not exceed 50 characters">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Phone Number Input -->
                                <div class="mb-3 col-sm-12">
                                    <label for="phone" class="form-label mb-2">Phone Number: </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <img src="./img/logo/ng.png" alt="Nigeria Flag" style="width: 30px; height: 18px;">
                                            &nbsp;+234
                                        </span>
                                        <input id="phoneNumber" class="form-control py-3" type="tel" name="phoneNumber" placeholder="xxxxxxxxxxx" required>
                                    </div>
                                    <div class="error-message"></div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Gender Select -->
                                <div class="mb-3 col-sm-6">
                                    <label for="gender" class="form-label mb-2">Gender:</label>
                                    <select id="gender" name="gender" class="form-select py-3">
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="error-message"></div>
                                </div>

                                <!-- Date of Birth Input -->
                                <div class="mb-3 col-sm-6">
                                    <label for="dob" class="form-label mb-2">Date of Birth:</label>
                                    <input type="date" id="dob" name="dob" class="form-control py-3">
                                    <div class="error-message"></div>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="mb-3 col-sm-12">
                                <label for="email" class="form-label mb-2">Email Address: </label>
                                <input type="email" class="form-control py-3" id="email" placeholder="Enter your email address" name="email" required>
                                <div class="error-message"></div>
                            </div>

                            <!-- Referral Code Input -->
                            <div class="mb-3 col-sm-12">
                                <label for="rcode" class="form-label mb-2">Referral Code (if any): </label>
                                <input type="text" class="form-control py-3" placeholder="Enter your referral code">
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4 col-sm-12 row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4"></div>
                                <div class="mt-5 col-sm-4 submit-btn">
                                    <button type="submit" class="btn w-100">Sign Up</button>
                                </div>
                            </div>

                            <p class="inner-text text-center mt-3">Or you have an account <a href="./login-form/index.php" class="text-success">sign in</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Main Content Section -->


    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Your Email and Phone Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <strong>You entered: <span id="displayNumber"></span></strong><br>
                    <strong><span id="displayEmail"></span></strong>
                    <p>If it's not correct, please edit it because we will send you an OTP for verification.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Edit</button>
                    <button type="submit" class="btn" id="confirmBtn" style="background-color: #559403;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <section class="hidden cover-hight" id="verification">
    <div class="container verify-content">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="mt-3 mb-3 text-success">Step 2 of 4</h5>
                <h3 class="btn-success text-center mb-0 py-3 verify-header">Verification</h3>
                <div class="verify-container text-center mt-0">
                    <p class = "text-success">Enter the 4-digit code sent to your Email</p>
                    <div class="code-inputs">
                        <input type="text" maxlength="1" class="verify-box" />
                        <input type="text" maxlength="1" class="verify-box" />
                        <input type="text" maxlength="1" class="verify-box" />
                        <input type="text" maxlength="1" class="verify-box" />
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-5">
                        <p id="timer" class="text-dark">2:00</p>
                        <a href="#" id="resendOtp" class="resend-link text-success">Resend OTP</a>
                    </div>
                    <button class="btn btn-success mt-3 px-4" id="verifyButton" name="verify_otp">Verify</button>
                </div>
            </div>
        </div>
    </div>
</section>
    <section id="createPass" class="hidden">
        <div class="container create-content">
            <div class="col-sm-6 px-4">
                <h5 class="lighter-black mb-3">Step 3 of 4</h5>
            </div>
            <div class="create-container col-sm-6">
                <h3 class="btn-success text-center px-4 py-3 create-header">Create password</h3>
                <p class="text-center py-3 text-dark">Please create a password</p>
                <form id="createForm">
                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <div class="input-group position-relative">
                            <i class="fas fa-lock position-absolute top-50 start-0 translate-middle-y ms-2"></i>
                            <input type="password" id="password" class="form-control creatingPasswordss py-3 pe-5 ps-5" placeholder="Enter your password" name="password" minlength="6" />
                            <i class="fas fa-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 togglepass" id="togglePassword" style="cursor: pointer;"></i>
                        </div>
                        <div class="error-message" id="passwordError"></div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Confirmed Password:</label>
                        <div class="input-group">
                            <i class="fas fa-lock position-absolute top-50 start-0 translate-middle-y ms-2"></i>
                            <input type="password" id="confirmPassword" class="form-control creatingPasswordss py-5 pe-5 password" placeholder="Enter your password" name="password" minlength="6" />
                            <i class="fas fa-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 togglepass" id="togglePassword" style="cursor: pointer;"></i>
                        </div>
                        <div class="error-message" id="confirmPasswordError"></div>
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input type="checkbox" id="termsCheckbox" class="form-check-input me-2">
                        <label for="termsCheckbox" class="form-check-label text-dark">
                            I accept the
                            <a href="terms.html" target="_blank" class="text-primary fw-bold">Terms & Conditions</a>
                        </label>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message" class="text-danger mt-2"></div>

                    <button type="submit" class="btn btn-success mt-4 mb-3 w-100" id="creatingPass">Submit</button>
                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./asset/script/signup.js"></script>
    <script src="asset/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>