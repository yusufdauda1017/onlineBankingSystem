<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <link rel="icon" href="..\img\logo\logo.svg" type="image/svg">
    <title>login || Trustpoint</title>
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">
<style>
.bg {
  background-size: cover;
  background-position: center;
  position: relative;
  height: 100vh; /* Adjust as needed */
}

.bg .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Adjust opacity and color */
  z-index: 1;
}




</style>
    <title>Login </title>
  </head>
  <body>
  
    <!-- Loader Container -->
    <div class="loader-container">
        <img src="../img/logo/logo.svg" alt="Trustpoint Logo" class="logoees">
          hhhhhhhhhhhhhhhhh
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
              <form action="#" method="post" id = "signinForm">
                <div class="form-group first">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" placeholder="your-email@gmail.com" id="email">
                </div>
                <div class="form-group last mb-3">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Your Password" id="password">
                </div>

                <div class="d-flex mb-5 align-items-center">
                  <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                    <input type="checkbox" checked="checked" />
                    <div class="control__indicator"></div>
                  </label>
                  <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>
                </div>
                <input type="submit" value="Log In" class="btn btn-block">
                <p class="mt-3 " style="font-size: 0.8rem;">Please, if you have not created an account, <a href="../signup.php">click here to sign up</a>.</p>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./script/script.js"></script>
  </body>
</html>