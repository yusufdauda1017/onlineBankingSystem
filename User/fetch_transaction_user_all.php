
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/check_remember.php");

$timeout_duration = 60;

// Check if session exists
if (!isset($_SESSION['user_id'])) {
    // If no session, check if "Remember Me" can re-authenticate
    if (!isset($_COOKIE['remember_token'])) {
        // If no session and no remember token → force login
        header('Location: ../login.php');
        exit();
    }
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    if (isset($_COOKIE['remember_token'])) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/check_remember.php");
 // Include remember functionality
    } else {
        session_unset();
        session_destroy();
        setcookie("remember_token", "", time() - 3600, "/");
        header('Location: ../login.php?timeout=1');
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

    <!-- Bootstrap CSS (Latest Stable Version) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons (Single Version) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Swiper CSS (For Sliders/Animations) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

    <!-- Custom Styles -->
    <link rel="stylesheet" href="./asset/css/style.css" />
    <link rel="stylesheet" href="./asset/css/internal.css" />

    <!-- Favicon -->
    <link rel="icon" href="./asset/img/logo/logo.svg" type="image/svg+xml">

    <title>User Dashboard || Trustpoint</title>
</head>

  <body>
  <header class="header d-flex justify-content-between align-items-center">
    <img src="..asset/img/logo/logo.svg" alt="" class = "header-img-first"/>
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
    <img src="../asset/img/logo/logo-1.png" alt="" />
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
      <a href="#transaction?view_all=true">
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
<section class=" transfer-content section" id="transaction">
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
    <tbody id = "transactionTable">
    </tbody>
  </table>
</div>
</section>
<footer class="footer d-flex justify-content-center align-items-center">
      <small>&copy; 2025, Trustpoint. All Rights
        Reserved.</small>
  </footer>
 <!-- Swiper JS (For Sliders) -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<!-- jQuery (Load before dependent scripts) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert (For Notifications) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap JavaScript (Latest Stable Version) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script src="./asset/script/upload.js"></script>


<script src="./asset/script/activity_log.js"></script>
<script src="./asset/script/script.js"></script>
<script src="./asset/script/internal.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const isViewAll = window.location.href.includes("view_all=true"); // Detect if it's the full page
    fetchTransactions(1, isViewAll);
});

function fetchTransactions(page = 1, viewAll = false) {
    let url = `https://trustpoint.wuaze.com/User/asset/include/fetch_transactions_user.php?page=${page}`;
    if (viewAll) {
        url += "&view=all";
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            displayTransactions(data.transactions);
        })
        .catch(error => console.error("Error fetching transactions:", error));
}

function displayTransactions(transactions) {
    let tableBody = document.getElementById("transactionTable");
    tableBody.innerHTML = "";

    transactions.forEach((txn, index) => {
        let status = txn.status.toLowerCase();
        let badgeClass = "";

        if (status === "successful") {
            badgeClass = "badge bg-success";
        } else if (status === "failed") {
            badgeClass = "badge bg-danger";
        } else if (status === "pending") {
            badgeClass = "badge bg-warning text-dark";
        } else {
            badgeClass = "badge bg-secondary";
        }

        let displayStatus = txn.status.charAt(0).toUpperCase() + txn.status.slice(1).toLowerCase();

        let row = `<tr>
            <td>${index + 1}</td>
            <td>${txn.transaction_no}</td>
            <td>${txn.transaction_type}</td>
            <td>${txn.transaction_date}</td>
            <td>${txn.amount}</td>
            <td><span class="${badgeClass}">${displayStatus}</span></td>
            <td><a href="transaction_details.php?id=${txn.transaction_no}" class="btn btn-primary text-light">View</a></td>
        </tr>`;

        tableBody.innerHTML += row;
    });
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
</script>
</body>
</html>
