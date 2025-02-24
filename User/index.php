

<?php

require_once '../db/db_connect.php';
require_once __DIR__ . '/../includes/check_remember.php'; // Include remember functionality

$timeout_duration = 60;

// Check if session exists
if (!isset($_SESSION['user_id'])) {
    // If no session, check if "Remember Me" can re-authenticate
    if (!isset($_COOKIE['remember_token'])) {
        // If no session and no remember token → force login
        header('Location: ../login-form/index.php');
        exit();
    }
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    if (isset($_COOKIE['remember_token'])) {
      require_once __DIR__ . '/../includes/check_remember.php'; // Include remember functionality
    } else {
        session_unset();
        session_destroy();
        setcookie("remember_token", "", time() - 3600, "/");
        header('Location: ../login-form/index.php?timeout=1');
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Ensure database connection exists
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch profile picture
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $profile_pic = !empty($row["profile_pic"]) ? $row["profile_pic"] : "uploads/default-profile.png";
    $stmt->close();
} else {
    $profile_pic = "uploads/default-profile.png"; // Fallback
}

// Close DB connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->

     <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
    <link rel="stylesheet" href="internal.css" />

    <link rel="icon" href="..\img\logo\logo.svg" type="image/svg">
    <title>User Dashboard || Trustpoint</title>
  </head>
  <body>
  <header class="header d-flex justify-content-between align-items-center">
    <img src="../img/logo/logo.svg" alt="" class = "header-img-first"/>

  <div class="hamburger me-3 ms-2 " onclick="toggleSidebar()">
    <div></div>
    <div></div>
    <div></div>
  </div>
  <div class="d-flex align-items-center justify-content-around">
    <button
      class="btn position-relative text-light"
      aria-label="Zoom Full View"
      style="border: none; padding: 0; background-color: transparent"
    >
      <i class="icon-header fas fa-comment"></i>
    </button>
    <!-- Notifications -->
    <button
      class="btn position-relative  text-light"
      aria-label="Notifications"
    >
      <i class="icon-header fas fa-bell"></i>
    </button>
    <button
      class="btn position-relative  text-light"
      aria-label="Zoom Full View"
      style="border: none; padding: 0; background-color: transparent" onclick="toggleFullscreen();"
    >
      <i class="icon-header fas fa-expand"></i>
    </button>

    <div class="dropdown col-sm-4 col-md-6 ">
    <div class="dropdown">
  <!-- Profile Image as Dropdown Toggle -->
  <div class="d-flex align-items-center dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
    <img src="<?php echo $profile_pic; ?>"
         alt="Profile Picture"
         class="rounded-circle"
         style="height: 35px; width: 35px; padding: 2px;">

    <!-- Username (Visible on Large Screens, Hidden on Small Screens) -->
    <span class="ms-2 d-none d-md-inline"><?php echo $_SESSION['user']; ?></span>
  </div>

  <!-- Dropdown Menu -->
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
    <li><a class="dropdown-item" href="#profile">View Profile</a></li>
    <li><hr class="dropdown-divider" /></li>
    <li>
      <a class="dropdown-item text-danger" href="#" id="logoutBtn">Logout</a>
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
  <button class="close-sidebar" onclick="closeSidebar()"><i class="fas fa-times"></i></button>
  <ul>
    <li>
      <a href="index.php">
        <i><img src="grid_view_24dp_FFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="" srcset="" class="" /></i>
        <span class="text">Home</span>
      </a>
    </li>
    <li>
      <a href="#beneficiaries">
        <i class="fas fa-user-plus"></i>
        <span class="text">Beneficiaries</span>
      </a>
    </li>
    <li>
      <a href="fetch_transaction_user_all.php?view_all=true">
        <i class="fas fa-solid fa-piggy-bank"></i>
        <span class="text">Transactions</span>
      </a>
    </li>
    <li>
      <a href="#services">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="white"
          width="24px"
          height="24px"
        >
          <text x="0" y="20" font-size="20" font-family="Arial">₦</text>
        </svg>
        <span class="text">Services</span>
      </a>
    </li>
    <li>
      <a href="#loans">
        <i class="fas fa-hand-holding-usd"></i>
        <span class="text">Loans</span>
      </a>
    </li>
    <li>
      <a href="#profile">
        <i class="fas fa-cogs"></i>
        <span class="text">Profile Setting</span>
      </a>
    </li>
    <li>
      <a href="#referral">
        <i class="fas fa-star"></i>
        <span class="text">Upgrade</span>
      </a>
    </li>
    <li>
      <a href="#" id="logoutBtn" class="text-danger">
        <i class="fas fa-sign-out-alt"></i>
        <span class="text">Logout</span>
      </a>
    </li>
  </ul>
</div>

    <section class="main-content active section" id="dashboard">
      <div
        class="d-flex justify-content-between align-items-center header-name px-3 py-2"
      >
        <div class="name">
          <h3>
            Hello, <span class="board-name"><?php echo $_SESSION['user_name']; ?>!</span>
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
          <span class="account-number"><?php echo $_SESSION['account_number']; ?></span>
          <h3 class="account-balance ">
            Total Balance
          </h3>
          <div class="account-balance">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="24px"
              height="24px"
            >
              <text x="0" y="20" font-size="20" font-family="Arial"></text>
            </svg>

            <p>
    <span id="account_balancee" class="hidden-text"></span>
    <i class="fas fa-eye-slash toggle-eye" onclick="toggleVisibility('account_balancee', this)"></i>
            </p>
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
          <a href="./externalTransfer.php" class="payment-option d-flex align-items-center ">
            <i class="fas fa-university me-3 text-primary"></i>
            <span>to Bank</span>
          </a>

          <!-- Transfer to Same Bank -->
          <a href="internalTransfer.php" class="payment-option d-flex align-items-center ">
            <i class="fas fa-user-friends me-3 text-success"></i>
            <span>to TrustPoint</span>
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
    <h3>Transaction History</h3>
    <a href="fetch_transaction_user_all.php?view_all=true">View all</a>
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
    <tbody id = "transactionTable">

    </tbody>
  </table>
</div>

    </section>

    <section class="transfer-content hidden section" id="beneficiaries">
    <div class="d-flex justify-content-between align-items-center header-name px-5 py-3">
        <div class="name">
            <a href="#" class="back"><i class="fa-solid fa-greater-than"></i> Back</a>
        </div>
        <div class="location-name"><a href="../index.php">Home</a>/<a href="#">user</a>/Beneficiaries</div>
    </div>
    <div class="beneficiaries-container d-flex justify-content-between align-items-center">
        <form action="#" method="post" class="account-details flex-grow-1 w-100">
            <div class="search-bar-container">
                <input type="search" name="Services-beneficiaries" id="Services-beneficiaries" class="form-control search-bar w-100" placeholder=" ">
                <label for="Services-beneficiaries" class="form-label">Search Beneficiaries</label>
            </div>
        </form>
        <button class="action-button deposit w-50" data-bs-toggle="modal" data-bs-target="#addBeneficiaryModal">Add Beneficiary</button>
    </div>
    <div class="beneficiaries-container mt-5 mb-5">
        <h2 class="mb-4 fs-5">Beneficiaries</h2>
        <div id="user-list" class="user-list mt-3"></div>
    </div>
</section>
<!-- Add Beneficiary Modal -->
<div class="modal fade" id="addBeneficiaryModal" tabindex="-1" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBeneficiaryModalLabel">Add Beneficiary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

    <form id="addBeneficiaryForm">
        <div class="mb-3">
        <div class="search-bar-container">

            <select class="form-control search-bar" id="bankSelect" required onchange="fetchAccountName()">
                <option value="">Search or Select Bank</option>
            </select>
            <label for="bankSelect" class="form-label">Select Bank</label>
            </div>
        </div>

        <div class="mb-3">
        <div class="search-bar-container bg-muted">
            <input type="text" class="form-control search-bar" id="accountNumber" required oninput="fetchAccountName()" maxlength="10">
            <label for="accountNumber" class="form-label">Account Number</label>
           </div>
            <div class="spinner-border text-primary mt-2 mb-2 d-none" id="loadingSpinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <small id="accountName" class="form-text text-muted"></small>
        </div>

        <div class="mb-3">
        <div class="search-bar-container">
            <input type="text" class="form-control search-bar" id="nickname">
            <label for="nickname" class="form-label">Beneficiary Nickname (Optional)</label>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100" id="addBeneficiarySubmit">Add Beneficiary</button>
    </form>

            </div>
        </div>
    </div>
</div>
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
<section class="transfer-content  hidden section" id="profile">
<div class="container my-5">




    <!-- <div class="">
        <img src="default-profile.png" alt="Profile Picture" class="profile-pic" id="profilePreview">
        <h5 class="mt-2" id="userName">User Name</h5>
        <p id="userEmail">Email: user@example.com</p>
        <input type="file" class="form-control mt-2" id="profilePic" accept="image/*">
        <button class="btn btn-danger btn-sm mt-2" id="removePic">Remove Picture</button>
    </div> -->
    <!-- Main Content -->
    <div class="row">
      <!-- Left Column: Navigation Menu -->
     <!-- Left Column: Navigation Menu -->
<div class="col-md-4">
<!-- Profile Picture Upload Section -->
<div class="text-center mb-4 card profile-section p-3">
    <label for="profilePic" class="d-block">
        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic mb-3 mt-3 rounded-circle border" id="profilePreview" width="120" height="120">
    </label>
    <input type="file" class="form-control mt-2 profile-pic-upload" id="profilePic" accept="image/*">
    <button class="btn btn-primary btn-sm mt-2 mb-4 w-100" id="uploadPic" disabled>
        <span id="uploadText">Upload</span>
        <span id="uploadLoader" class="spinner-border spinner-border-sm" style="display: none;"></span>
    </button>
</div>


    <!-- Navigation Links -->
    <div class="list-group mt-3 mb-5">
        <a href="#personal-info" class="list-group-item list-group-item-action">Personal Information</a>
        <a href="#security" class="list-group-item list-group-item-action">Security Settings</a>
        <a href="#communication" class="list-group-item list-group-item-action">Communication Preferences</a>
        <a href="#linked-accounts" class="list-group-item list-group-item-action">Linked Accounts & Cards</a>
        <a href="#beneficiaries" class="list-group-item list-group-item-action">Beneficiaries</a>
        <a href="#privacy" class="list-group-item list-group-item-action">Privacy & Terms</a>
        <a href="#activity-log" class="list-group-item list-group-item-action">Activity Log</a>
    </div>
</div>


      <!-- Right Column: Editable Sections -->
      <div class="col-md-8">
        <!-- Personal Information -->
        <div class="card section-card" id="personal-info">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapsePersonalInfo">
            <h5 class="mb-0">Personal Information</h5>
          </div>
          <div id="collapsePersonalInfo" class="collapse show">
            <div class="card-body">
              <form>
                <div class="mb-4">


                <div class="search-bar-container">
                <input type="text" class="form-control search-bar" id="fullName" value=" <?php echo $_SESSION['user_name_full']; ?>" readonly>
              <label for="fullName" class="form-label">Full Name</label>
            </div>
                  <small class="text-muted ">Contact support to update.</small>
                </div>

                <div class="mb-3 mt-3">

                <div class="search-bar-container">
                <input type="email" class="form-control search-bar" id="email" value="<?php echo $_SESSION['email']; ?>">
              <label for="email" class="form-label">Email Address</label>
            </div>
                  <button type="button" class="btn btn-sm btn-outline-secondary mt-2 mb-3">Verify Email</button>
                </div>
                <div class="mb-3 ">
                <div class="search-bar-container">
                  <input type="tel" class="form-control search-bar" id="phone" value="<?php echo $_SESSION['account_number']; ?>"  readonly>
                  <label for="phone" class="form-label ">Account Number</label>
                  </div>

                </div>
                <button type="submit" class="btn btn-save mt-5">Save Changes</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Security Settings -->
        <div class="card section-card" id="security">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseSecurity">
            <h5 class="mb-0">Security Settings</h5>
          </div>
          <div id="collapseSecurity" class="collapse">
            <div class="card-body">
              <form>
                <div class="mb-3">
                <div class="row g-2">
              <div class="col-auto">
                  <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                      Change Password
                  </button>
              </div>
              <div class="col-auto">
                  <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2" data-bs-toggle="modal" data-bs-target="#createTransactionPinModal">
                      Create Transaction PIN
                  </button>
              </div>
              <div class="col-auto">
                  <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2" data-bs-toggle="modal" data-bs-target="#changePinModal">
                      Change PIN
                  </button>
              </div>
          </div>


                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="2fa">
                  <label class="form-check-label" for="2fa">Enable Two-Factor Authentication (2FA)</label>
                </div>
                <button type="submit" class="btn btn-save">Save Changes</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Communication Preferences -->
        <div class="card section-card" id="communication">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseCommunication">
            <h5 class="mb-0">Communication Preferences</h5>
          </div>
          <div id="collapseCommunication" class="collapse">
            <div class="card-body">
              <form>
                <div class="mb-3">
                  <label class=" mb-2">Notification Channels</label>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="emailNotifications">
                    <label class="form-check-label" for="emailNotifications">Email</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="smsNotifications">
                    <label class="form-check-label" for="smsNotifications">SMS</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="pushNotifications">
                    <label class="form-check-label" for="pushNotifications">Push Notifications</label>
                  </div>
                </div>
                <button type="submit" class="btn btn-save">Save Changes</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Linked Accounts & Cards -->
        <div class="card section-card" id="linked-accounts">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseLinkedAccounts">
            <h5 class="mb-0">Linked Accounts & Cards</h5>
          </div>
          <div id="collapseLinkedAccounts" class="collapse">
            <div class="card-body">
              <h6>Linked Accounts</h6>
              <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Savings Account - ****1234
                  <button class="btn btn-sm btn-outline-danger">Unlink</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Checking Account - ****5678
                  <button class="btn btn-sm btn-outline-danger">Unlink</button>
                </li>
              </ul>
              <h6>Linked Cards</h6>
              <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Visa - ****4321
                  <button class="btn btn-sm btn-outline-danger">Block</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  MasterCard - ****8765
                  <button class="btn btn-sm btn-outline-danger">Block</button>
                </li>
              </ul>
              <button type="button" class="btn btn-save">Add New Account/Card</button>
            </div>
          </div>
        </div>

        <!-- Beneficiaries -->
        <div class="card section-card" id="beneficiaries">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseBeneficiaries">
            <h5 class="mb-0">Beneficiaries</h5>
          </div>
          <div id="collapseBeneficiaries" class="collapse">
            <div class="card-body">
              <h6>Saved Beneficiaries</h6>
              <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Jane Doe - ****9876
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  John Smith - ****5432
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </li>
              </ul>
              <button type="button" class="btn btn-save">Add New Beneficiary</button>
            </div>
          </div>
        </div>

        <!-- Privacy & Terms -->
        <div class="card section-card" id="privacy">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapsePrivacy">
            <h5 class="mb-0">Privacy & Terms</h5>
          </div>
          <div id="collapsePrivacy" class="collapse">
            <div class="card-body">
              <div class="mb-3">
                <a href="#" class="btn btn-link">Privacy Policy</a>
                <a href="#" class="btn btn-link">Terms of Service</a>
              </div>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="dataSharing">
                <label class="form-check-label" for="dataSharing">Allow Data Sharing with Third Parties</label>
              </div>
              <button type="submit" class="btn btn-save mt-3">Save Changes</button>
            </div>
          </div>
        </div>

       <!-- Activity Log Card -->
<div class="card section-card" id="activity-log">
    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseActivityLog">
        <h5 class="mb-0">Activity Log</h5>
    </div>
    <div id="collapseActivityLog" class="collapse">
        <div class="card-body">
            <table class="table activity-log-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Activity</th>
                        <th>Device</th>

                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here via JavaScript -->
                </tbody>
            </table>
            <button type="button" class="btn btn-save mt-3" id="exportLog">Export Log</button>
        </div>
    </div>
</div>


      </div>
    </div>
  </div>


<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-save">
                <h5 class="modal-title text-light text-center " id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="password-change">
                    <div class="mb-3">
                        <label for="oldPassword">Old Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="oldPassword">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-save">
                        <span class="btn-text">Save Changes</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Create Transaction PIN Modal -->
<div class="modal fade" id="createTransactionPinModal" tabindex="-1" aria-labelledby="createTransactionPinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light text-center" id="createTransactionPinModalLabel">Create Transaction PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-transaction-pin">
                    <div class="mb-3">
                        <label for="transactionPin">Enter New PIN</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="transactionPin" maxlength="4">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmTransactionPin">Confirm PIN</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmTransactionPin" maxlength="4">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-save">
                        <span class="btn-text">Create PIN</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change PIN Modal -->
<div class="modal fade" id="changePinModal" tabindex="-1" aria-labelledby="changePinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light text-center" id="changePinModalLabel">Change Transaction PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="change-pin">
                    <div class="mb-3">
                        <label for="oldPin">Old PIN</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="oldPin" maxlength="4">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="newPin">New PIN</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPin" maxlength="4">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmNewPin">Confirm New PIN</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmNewPin" maxlength="4">
                            <span class="input-group-text"><i class="fa fa-eye toggle-password"></i></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-save">
                        <span class="btn-text">Change PIN</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</section>

  <footer class="footer d-flex justify-content-center align-items-center">
      <small>&copy; 2025, Trustpoint. All Rights
        Reserved.</small>
  </footer>

    <!-- jQuery (should be loaded before Bootstrap and other scripts that depend on it) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (required for Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<!-- Swiper.js (carousel/slideshow library) -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<!-- SweetAlert (for alert popups) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- External/local scripts -->
<script src="../src/login-form/script/logout.js"></script>
<script src="./internal.js"></script>
<script src="./activity_log.js"></script>
<script src="./upload.js"></script>
<script src="./beneficiary.js"></script>

<!-- Main script (should be last to ensure all dependencies are loaded) -->
<script src="script.js"></script>
<script src="ajax-fetch.js"></script>

<script>


document.addEventListener("DOMContentLoaded", function () {
    const isViewAll = window.location.href.includes("view_all=true"); // Detect if it's the full page
    fetchTransactions(1, isViewAll);
});



$(document).ready(function () {
   // Get logged-in user's account

    fetch_frequent_transactions();
});

$(document).on("click", ".prequent", function (e) {
    e.preventDefault(); // Prevent default navigation

    let recipientName = $(this).data("name");
    let recipientAccount = $(this).data("account");

    // Store in session storage (or localStorage)
    sessionStorage.setItem("recipientName", recipientName);
    sessionStorage.setItem("recipientAccount", recipientAccount);

    // Redirect to transfer UI
    window.location.href = "./internalTransfer.php";
});


    document.addEventListener("DOMContentLoaded", function () {
        let element = document.getElementById("account_balancee");
        let actualValue = "<?php echo $_SESSION['balance']; ?>"; // Get balance from PHP
        element.setAttribute("data-actual", actualValue);
        element.innerText = "*".repeat(actualValue.replace(/[^\d]/g, "").length); // Hide balance
    });

    function toggleVisibility(id, icon) {
        let element = document.getElementById(id);
        let actualValue = element.getAttribute("data-actual");

        if (element.classList.contains("hidden-text")) {
            element.innerText = actualValue; // Show actual balance
            element.classList.replace("hidden-text", "visible-text");
            icon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            element.innerText = "*".repeat(actualValue.replace(/[^\d]/g, "").length); // Hide balance
            element.classList.replace("visible-text", "hidden-text");
            icon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.querySelector(".hamburger");
    const sidebar = document.getElementById("sidebar");
    const closeBtn = document.querySelector(".close-sidebar");

    function toggleSidebar() {
        sidebar.classList.toggle("active");
        hamburger.classList.toggle("active");
    }

    function closeSidebar() {
        sidebar.classList.remove("active");
        hamburger.classList.remove("active");
    }

    // Open/close sidebar when clicking the hamburger icon
    hamburger.addEventListener("click", function (event) {
        event.stopPropagation(); // Prevent click from reaching document
        toggleSidebar();
    });

    // Click outside sidebar to close it
    document.addEventListener("click", function (event) {
        if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
            closeSidebar();
        }
    });

    // Close sidebar when close button is clicked (if close button exists)
    if (closeBtn) {
        closeBtn.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent click from reaching document
            closeSidebar();
        });
    }
});

$(document).ready(function () {
    if ($("#fullscreenBtn").length) {
        $("#fullscreenBtn").on("click", function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });
    }
});

$(document).ready(function () {


    // Ensure the menu exists before adding the event listener
    if ($("#userMenu").length && $(".dropdown-menu").length) {
        $("#userMenu").on("click", function (event) {
            event.stopPropagation(); // Prevent event from bubbling
            $(".dropdown-menu").toggleClass("show");
        });

        // Hide dropdown when clicking outside
        $(document).on("click", function (event) {
            if (!$("#userMenu").is(event.target) && $(".dropdown-menu").has(event.target).length === 0) {
                $(".dropdown-menu").removeClass("show");
            }
        });
    }


    loadBalance();
    setInterval(loadBalance, 5000);

    // ** Profile Dropdown Toggle **
    $("#userMenu").click(function (event) {
        event.stopPropagation();
        $(".dropdown-menu").toggleClass("show");
    });

    $(document).click(function (event) {
        if (!$("#userMenu").is(event.target) && $(".dropdown-menu").has(event.target).length === 0) {
            $(".dropdown-menu").removeClass("show");
        }
    });


    // ** Profile Picture Preview **
    $("#profilePic").change(function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#profilePreview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // ** Remove Profile Picture **
    $("#removePic").click(function () {
        $("#profilePreview").attr("src", 'default-profile.png');
        $("#profilePic").val('');
    });





$(document).ready(function () {
    var senderAccount = "<?php echo $_SESSION['account_number']; ?>"; // Get logged-in user's account

    $.ajax({
        url: "./fetch_frequent_transactions.php",
        type: "POST",
        data: { sender_account: senderAccount },
        dataType: "json",
        success: function (response) {
            console.log("Server Response:", response); // Debugging

            if (response.length === 0) {
                console.log("No frequent recipients found.");
            } else {
                let html = "";

                            response.forEach(function (tx) {
                html += `
                    <div class="swiper-slide">
                        <a href="./internalTransfer.php" class="prequent" data-name="${tx.name}" data-account="${tx.receiver_account}">
                            <img src="logo.svg" alt="User Logo">
                            <span>
                                ${tx.name}
                                <p>${tx.receiver_account}</p>
                            </span>
                        </a>
                    </div>
                `;
            });

                $(".swiper-wrapper").html(html);

                // Initialize Swiper
                new Swiper(".swiper", {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        550: {
                            slidesPerView: 2,
                            spaceBetween: 10,
                        },
                        850: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        990: {
                            slidesPerView: 2,
                            spaceBetween: 30,
                        },
                        1200: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                    },
                });
            }
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
        },
    });
});

$(document).on("click", ".prequent", function (e) {
    e.preventDefault(); // Prevent default navigation

    let recipientName = $(this).data("name");
    let recipientAccount = $(this).data("account");

    // Store in session storage (or localStorage)
    sessionStorage.setItem("recipientName", recipientName);
    sessionStorage.setItem("recipientAccount", recipientAccount);

    // Redirect to transfer UI
    window.location.href = "./internalTransfer.php";
});
    // ** Change Password Form Submission **
    $("#password-change").submit(function (e) {
        e.preventDefault();
        var oldPassword = $("#oldPassword").val();
        var newPassword = $("#newPassword").val();
        var confirmPassword = $("#confirmPassword").val();
        var submitButton = $(".btn-save");
        var btnText = $(".btn-text");
        var loader = $(".spinner-border");

        // Disable button & show loader
        submitButton.prop("disabled", true);
        btnText.text("Processing...");
        loader.removeClass("d-none");

        if (newPassword.length < 6) {
            Swal.fire("Warning", "Password should be at least 6 characters long.", "warning");
            resetButton();
            return;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire("Error", "Passwords do not match!", "error");
            resetButton();
            return;
        }

        changePassword();
        function resetButton() {
            submitButton.prop("disabled", false);
            btnText.text("Save Changes");
            loader.addClass("d-none");
        }
    });

    // ** Create Transaction PIN **
    $("#create-transaction-pin").submit(function (e) {
        e.preventDefault();
        var pin = $("#transactionPin").val();
        var confirmPin = $("#confirmTransactionPin").val();

        if (pin.length !== 4 || confirmPin.length !== 4) {
            Swal.fire("Error", "PIN must be 4 digits.", "error");
            return;
        }
        if (pin !== confirmPin) {
            Swal.fire("Error", "PINs do not match.", "error");
            return;
        }

        var submitBtn = $(this).find("button[type=submit]");
        var spinner = submitBtn.find(".spinner-border");
        var btnText = submitBtn.find(".btn-text");

        btnText.addClass("d-none");
        spinner.removeClass("d-none");
        process_pin();

    });

    // ** Change Transaction PIN **
    $("#change-pin").submit(function (e) {
        e.preventDefault();
        var oldPin = $("#oldPin").val();
        var newPin = $("#newPin").val();
        var confirmNewPin = $("#confirmNewPin").val();

        if (newPin.length !== 4 || confirmNewPin.length !== 4) {
            Swal.fire("Error", "PIN must be 4 digits.", "error");
            return;
        }
        if (newPin !== confirmNewPin) {
            Swal.fire("Error", "New PINs do not match.", "error");
            return;
        }

        var submitBtn = $(this).find("button[type=submit]");
        var spinner = submitBtn.find(".spinner-border");
        var btnText = submitBtn.find(".btn-text");

        btnText.addClass("d-none");
        spinner.removeClass("d-none");

        change_pin();
    });
});
</script>


  </body>
</html>


