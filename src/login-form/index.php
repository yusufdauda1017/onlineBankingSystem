<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="icon" href="./images/logo.svg" type="image/svg">
    <link rel="stylesheet" href="css/style.css">
    <title>login || Trustpoint</title>
    <title>Login </title>
  </head>
  <body>

    <!-- Loader Container -->
    <div class="loader-container">
        <img src="./images/logo.svg" alt="Trustpoint Logo" class="logoees">
    </div>

    <div class="d-lg-flex half">
      <div class="bg order-2 order-md-1"
           style="background-image: url('images/person-choosing-where-they-work-from-hybrid-working-model.jpg'); position: relative;">
        <!-- Overlay for reducing contrast -->
        <div class="overlay"></div>
      </div>

    <div class="contents order-1 order-md-2">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-7">
                    <h3>Login to <strong>Trustpoint</strong></h3>
                    <p class="mb-4">Manage your finances securely from anywhere. Log in to view your account, transfer funds, and much more.</p>
                    <form action="#" method="post" id="signinForm">
                        <div class="form-group first mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" placeholder="your-email@gmail.com" id="email">
                        </div>
                        <div class="form-group last mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" placeholder="Your Password" id="password">
                        </div>
                        <div class="d-flex mb-5 align-items-center justify-content-between">
                        <label class="control control--checkbox mb-0">
                            <span class="caption">Remember me</span>
                            <input type="checkbox" id="rememberMeCheckbox" />
                            <div class="control__indicator"></div>
                        </label>
                            <span class="ml-auto"><a href="../forgotPass/forgotPass.php" class="forgot-pass">Forgot Password</a></span>
                        </div>
                        <input type="submit" value="Log In" class="btn btn-block w-100">
                        <p class="mt-3 text-center" style="font-size: 0.7rem; ">Please, if you have not created an account, <a href="../loginup/signup.php">click here to sign up</a>.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 4 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script/script.js"></script>
  </body>
</html>