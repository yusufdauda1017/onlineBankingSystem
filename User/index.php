<?php
session_start();

// Define timeout duration in seconds (1 minute = 60 seconds)
$timeout_duration = 60;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If no session, redirect to login page
    header('Location: ../login-form/index.php');
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];

    // If the elapsed time exceeds the timeout duration
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();

        // Redirect to login page after timeout
        header('Location: ../login-form/index.php');
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();

// If session is still valid, continue with normal script
// (You can add additional logic here if necessary)
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- FontAwesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <script
      src="https://kit.fontawesome.com/1d6525ef6a.js"
      crossorigin="anonymous"
    ></script>
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"
  />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="..\img\logo\logo.svg" type="image/svg">
    <title>User Dashboard || Trustpoint</title>
  </head>
  <body>
    <header class="header d-flex justify-content-end align-items-center">
      <div class="d-flex align-items-center justify-content-end">
        <button
          class="btn position-relative me-3 text-light"
          aria-label="Zoom Full View"
          style="border: none; padding: 0; background-color: transparent"
        >
          <i class="icon-header fas fa-comment"></i>
        </button>
        <!-- Notifications -->
        <button
          class="btn position-relative me-3 text-light"
          aria-label="Notifications"
        >
          <i class="icon-header fas fa-bell"></i>
        </button>
        <button
          class="btn position-relative me-3 text-light"
          aria-label="Zoom Full View"
          style="border: none; padding: 0; background-color: transparent"
        >
          <i class="icon-header fas fa-expand"></i>
        </button>

        <!-- User Profile -->
        <div class="dropdown col-sm-8">
          <button
            class="btn dropdown-toggle col-sm-12 profile-img"
            type="button"
            id="userMenu"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img
              src="../img/testimonial/jidda.jpg"
              alt="Profile Picture"
              class="rounded-circle me-2"
              style="height: 35px; width: 35px; padding: 2px"
            />
            Hi Zayyad
          </button>
          <ul
            class="dropdown-menu dropdown-menu-end"
            aria-labelledby="userMenu"
          >
            <li><a class="dropdown-item" href="#profile">View Profile</a></li>
            <li><a class="dropdown-item" href="#settings">Settings</a></li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <a class="dropdown-item text-danger" href="#logout">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <!-- Sidebar -->
    <div id="sidebar">
      <div class="brand mb-5">
        <img src="../img/logo/logo-1.png" alt="" />
      </div>
      <ul>
        <li>
          <a href="#dashboard"
            ><i
              ><img
                src="grid_view_24dp_FFFF_FILL0_wght400_GRAD0_opsz24.svg"
                alt=""
                srcset=""
                class=""
            /></i>
            <span class="text">Home</span></a
          >
        </li>
        <li>
          <a href="#beneficiaries"
            ><i class="fas fa-user-plus"></i>
            <span class="text">Beneficiaries</span></a
          >
        </li>
        <li>
          <a href="#transaction"
            ><i class="fas fa-solid fa-piggy-bank"></i>
            <span class="text">Transactions</span></a
          >
        </li>
        <li>
          <a href="#services"
            ><svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="white"
              width="24px"
              height="24px"
            >
              <text x="0" y="20" font-size="20" font-family="Arial">₦</text>
            </svg>

            <span class="text">Services</span></a
          >
        </li>
        <li>
          <a href="#loans"
            ><i class="fas fa-hand-holding-usd"></i>
            <span class="text">Loans</span></a
          >
        </li>
        <li>
          <a href="#commission"
            ><i class="fas fa-cogs"></i>
            <span class="text">Profile Setting</span></a
          >
        </li>
        <li>
          <a href="#referral"
            ><i class="fas fa-star"></i> <span class="text">Upgrade</span></a
          >
        </li>
        <li>
          <a href="#" id="logoutBtn" class="text-danger"
            ><i class="fas fa-sign-out-alt"></i>
            <span class="text" >Logout</span></a
          >
        </li>
      </ul>
    </div>

    <section class="main-content active section" id="dashboard">
      <div
        class="d-flex justify-content-between align-items-center header-name px-3 py-2"
      >
        <div class="name">
          <h3>
            Hello, <span class="board-name">Yusuf</span>
            <img src="waving-hand-svgrepo-com.svg" alt="Waving hand icon" />
          </h3>
        </div>

        <div class="location-name"><a href="../index.php">Home</a>/user</div>
      </div>

      <div
        class="account-container d-flex justify-content-between align-items-center"
      >
        <div class="account-details">
          <h3 class="account-title">Account</h3>
          <span class="account-number">*******440</span>
          <h3 class="account-balance">
            Total Balance <i class="fas fa-eye-slash"></i>
          </h3>
          <div class="account-balance">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="24px"
              height="24px"
            >
              <text x="0" y="20" font-size="20" font-family="Arial">₦</text>
            </svg>
            <span class="balance-amount">300,000</span>
          </div>
        </div>
        <div
          class="account-actions d-flex justify-content-center align-items-center"
        >
          <!-- <button class="action-button transfer">Transfer</button>
    <button class="action-button withdraw">Withdraw</button> -->
          <button class="action-button deposit">Add money</button>
        </div>
      </div>
      <div
        class="payment-container d-flex flex-column p-4 bg-light rounded shadow-sm"
      >
        <!-- Header -->
        <h3 class="mb-2">Make Payment</h3>
        <div
          class="d-flex flex-row justify-content-around align-items-center p-4"
        >
          <!-- Transfer to Bank -->
          <a href="#" class="payment-option d-flex align-items-center ">
            <i class="fas fa-university me-3 text-primary"></i>
            <span>Transfer to Bank</span>
          </a>

          <!-- Transfer to Same Bank -->
          <a href="#" class="payment-option d-flex align-items-center ">
            <i class="fas fa-user-friends me-3 text-success"></i>
            <span>Transfer to TrustPoint</span>
          </a>

          <!-- Top-Up Airtime -->
          <a href="#" class="payment-option d-flex align-items-center ">
            <i class="fas fa-mobile-alt me-3 text-warning"></i>
            <span>Top-Up Airtime</span></a
          >

          <!-- Pay Bills -->
          <a href="#" class="payment-option d-flex align-items-center ">
            <i class="fas fa-file-invoice-dollar me-3 text-danger"></i>
            <span>Pay Bills</span>
          </a>
        </div>
      </div>

      <div class="prequent-container">
        <h3>Most Frequent Transfers</h3>

        <!-- Swiper -->
        <div class="swiper">
          <div class="swiper-wrapper">
            <!-- Transfer 1 -->
            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span>
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>

            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span >
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>

            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span>
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>

            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span>
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>

            <!-- Transfer 2 -->
            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span>
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>

            <!-- Transfer 3 -->
            <div class="swiper-slide">
              <a href="#" class="prequent">
                <img src="logo.svg" alt="User Logo">
                <span>
                  Yusuf Yakubu Dauda
                  <p>9122190440</p>
                </span>
              </a>
            </div>
          </div>
          <!-- Next Button -->

        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>

      <!-- Real-Time Transactions Table -->
      <div class="table-responsive mt-5 mb-3">
        <div class="mt-3 mb-3 d-flex flex-row justify-content-between align-items-center">
        <h3 >Transaction History </h3>
<a href="#transaction">View all</a>
        </div>

        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Transaction ID</th>
              <th>Type</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>TRX12345</td>
              <td>Debit</td>
              <td>2025-01-16</td>
              <td>₦10,000</td>
              <td><span class="badge bg-success">Successful</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>TRX12346</td>
              <td>Credit</td>
              <td>2025-01-15</td>
              <td>₦15,000</td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>TRX12347</td>
              <td>Debit</td>
              <td>2025-01-14</td>
              <td>₦8,000</td>
              <td><span class="badge bg-danger">Failed</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>


    <section class="transfer-content hidden section" id="beneficiaries">
      <div
        class="d-flex justify-content-between align-items-center header-name px-5 py-3"
      >
        <div class="name">
           <a href="#" class="back"><i class="fa-solid fa-greater-than"></i> Back</a>
        </div>

        <div class="location-name"><a href="../index.php">Home</a>/<a href="#">user</a>/Beneficiaries</div>
      </div>

      <div
      class="beneficiaries-container d-flex justify-content-between align-items-center "
    >
    <form action="#" method="post" class="account-details flex-grow-1">
      <div class="search-bar-container">
        <input
          type="search"
          name="Services-beneficiaries"
          id="Services-beneficiaries"
          class="form-control search-bar"
          placeholder=" "
        />
        <label for="Services-beneficiaries" class="form-label">Search Beneficiaries</label>
      </div>
    </form>

      <button class="action-button deposit">Add Beneficiary</button>
    </div>
    <div class="beneficiaries-container mt-5 mb-5">
      <h2 class="mb-4 fs-5">Beneficiaries</h2>
      <div id="user-list" class="user-list mt-3"></div>
    </div>
    </section>


    <section class=" transfer-content hidden section" id="transaction">

      <div
        class="d-flex justify-content-between align-items-center header-name px-5 py-3  mt-3"
      >
        <div class="name">
           <a href="#" class="back"><i class="fa-solid fa-greater-than"></i> Back</a>
        </div>

        <div class="location-name"><a href="../index.php">Home</a>/<a href="#">user</a>/Transaction</div>
      </div>

      <!-- Real-Time Transactions Table -->
      <div class="table-responsive mt-5">
        <div class="mt-3 mb-3 d-flex flex-row justify-content-between align-items-center">
        <h3 >Transaction History </h3>
        </div>

        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Transaction ID</th>
              <th>Type</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>TRX12345</td>
              <td>Debit</td>
              <td>2025-01-16</td>
              <td>₦10,000</td>
              <td><span class="badge bg-success">Successful</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>TRX12346</td>
              <td>Credit</td>
              <td>2025-01-15</td>
              <td>₦15,000</td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>TRX12347</td>
              <td>Debit</td>
              <td>2025-01-14</td>
              <td>₦8,000</td>
              <td><span class="badge bg-danger">Failed</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>1</td>
              <td>TRX12345</td>
              <td>Debit</td>
              <td>2025-01-16</td>
              <td>₦10,000</td>
              <td><span class="badge bg-success">Successful</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>TRX12346</td>
              <td>Credit</td>
              <td>2025-01-15</td>
              <td>₦15,000</td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>TRX12347</td>
              <td>Debit</td>
              <td>2025-01-14</td>
              <td>₦8,000</td>
              <td><span class="badge bg-danger">Failed</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>1</td>
              <td>TRX12345</td>
              <td>Debit</td>
              <td>2025-01-16</td>
              <td>₦10,000</td>
              <td><span class="badge bg-success">Successful</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>TRX12346</td>
              <td>Credit</td>
              <td>2025-01-15</td>
              <td>₦15,000</td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>TRX12347</td>
              <td>Debit</td>
              <td>2025-01-14</td>
              <td>₦8,000</td>
              <td><span class="badge bg-danger">Failed</span></td>
              <td>
                <button class="btn btn-primary btn-sm">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class=" transfer-content hidden section" id="services">

      <div
        class="d-flex justify-content-between align-items-center header-name px-5 py-3"
      >
        <div class="name">
           <a href="#" class="back"><i class="fa-solid fa-greater-than"></i> Back</a>
        </div>

        <div class="location-name"><a href="../index.php">Home</a>/<a href="#">user</a>/Services</div>
      </div>

      <div class="container mt-5">
        <div class="mt-3 d-flex flex-row justify-content-between align-items-center services-header">
          <h3>Services</h3>
        </div>
        <div class="services-container">
          <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="custom-col first-column d-flex flex-column align-items-center">
              <i class="fas fa-signal"></i>
              <span>Data</span>
            </div>
            <div class="custom-col second-column d-flex flex-column align-items-center">
              <i class="fas fa-mobile-alt"></i>
              <span>Airtime</span>
            </div>
            <div class="custom-col third-column d-flex flex-column align-items-center">
              <i class="fas fa-exchange-alt"></i>
              <span>(P2P)</span>
            </div>
            <div class="custom-col fourth-column d-flex flex-column align-items-center">
              <i class="fas fa-building"></i>
              <span>Transfers</span>
            </div>
          </div>

          <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="custom-col fifth-column d-flex flex-column align-items-center">
              <i class="fa-regular fa-credit-card"></i>
              <span>Withdrawal</span>
            </div>
            <div class="custom-col sixth-column d-flex flex-column align-items-center">
              <i class="fas fa-store-alt"></i>
              <span>Deposit</span>
            </div>
            <div class="custom-col seventh-column d-flex flex-column align-items-center">
              <i class="fas fa-shopping-cart"></i>
              <span>Shopping</span>
            </div>
            <div class="custom-col eighth-column d-flex flex-column align-items-center">
              <i class="fas fa-tv"></i>
              <span>TV Sub</span>
            </div>
          </div>

          <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="custom-col ninth-column d-flex flex-column align-items-center">
              <i class="fas fa-globe"></i>
              <span>Internet</span>
            </div>
            <div class="custom-col tenth-column d-flex flex-column align-items-center">
              <i class="fas fa-bolt"></i>
              <span>Utility Bill</span>
            </div>
            <div class="custom-col eleventh-column d-flex flex-column align-items-center">
              <i class="fas fa-qrcode"></i>
              <span>QR Code</span>
            </div>
            <div class="custom-col twelfth-column d-flex flex-column align-items-center">
              <i class="fas fa-credit-card"></i>
              <span>Merchant </span>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="custom-col ninth-column d-flex flex-column align-items-center">
              <i class="fas fa-cash-register"></i>
              <span>POS</span>
            </div>
            <div class="custom-col tenth-column d-flex flex-column align-items-center">
              <i class="fas fa-calculator"></i>
              <span>Bill Splitting</span>
            </div>
            <div class="custom-col eleventh-column d-flex flex-column align-items-center">
              <i class="fas fa-wallet"></i>
              <span>Budgeting</span>
            </div>
            <div class="custom-col twelfth-column d-flex flex-column align-items-center">
              <i class="fas fa-piggy-bank"></i>
              <span>Savings</span>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-between align-items-center">
            <div class="custom-col ninth-column d-flex flex-column align-items-center">
              <i class="fas fa-chart-line"></i>
              <span>Investment</span>
            </div>
            <div class="custom-col tenth-column d-flex flex-column align-items-center">
              <i class="fas fa-gift"></i>
              <span>Cashback</span>
            </div>
            <div class="custom-col eleventh-column d-flex flex-column align-items-center">
              <i class="fas fa-briefcase"></i>
              <span>Pension</span>
            </div>
            <div class="custom-col twelfth-column d-flex flex-column align-items-center">
              <i class="fas fa-graduation-cap"></i>
              <span>School Fee</span>
            </div>
          </div>
        </div>
      </div>

    </section>
    <footer class="footer d-flex justify-content-center align-items-center">
<small>&copy; 2025, Trustpoint. All Rights
  Reserved.</small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="script.js">
    </script>
    <!-- Include SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../logout.js"></script>
  </body>
</html>
