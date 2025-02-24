<?php
session_start();

// Define timeout duration in seconds (1 minute = 60 seconds)
$timeout_duration = 5*60;

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
    <title>Transaction</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/1d6525ef6a.js" crossorigin="anonymous"></script>

    <link rel="icon" href="../img/logo/logo.svg" type="image/svg">
<style>
    *{
margin: 0;
padding: 0;
box-sizing: border-box;
}
:root {
    --primary-color: #559403;
    --primary-hover-color: #005313;
    --background-color: #001f10;
    --text-black: #333;
    --text-white: #f5f5f5;
}

/* Header Styling */
.header {
    background-color: var(--primary-hover-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.branding {
    display: flex;
    align-items: center;
    font-family: 'Poppins', sans-serif;
    color: var(--text-white);
    gap: 10px;
}

.branding img {
    height: 40px;
}

.toggle {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 20px;
    width: 30px;
    cursor: pointer;
margin-left: 2rem;
}

.toggle div {
    height: 4px;
    width: 100%;
    background-color: var(--text-white);
    border-radius: 2px;
    transition: all 0.3s ease-in-out;
}

   /* Your Sidebar Styling */
   #sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 60px;
    margin-top: 4rem;
    height: calc(100vh - 4rem);
    background: var(--background-color);
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    padding: 2px;
    transition: all 0.3s ease-in-out;
    z-index: 999;
    overflow-y: auto;
    overflow-x: hidden;
}

#sidebar.active {
  left: 0; /* Fully expanded */
  width: 250px;
}
#sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
#sidebar ul li a {
    color: var(--text-white);
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 16px;
    border-radius: 4px;
    transition: background 0.3s;
}
#sidebar ul li a:hover {
    background: var(--primary-hover-color);
}

#sidebar ul li a i {
    margin-right: 10px;
    font-size: 18px;
}

#sidebar:not(.active) ul li a span.text {
  visibility: hidden;
    opacity: 0;
    width: 0;
    margin: 0;
    overflow: hidden;
font-size: 0.3rem;

}
#sidebar h4:not(.active){
visibility: hidden;
opacity: 0;
font-size: 1rem;
margin: 1rem;
padding: 0.3rem;
}
#sidebar.active h4{
visibility: visible;
opacity: 1;
font-size: 1rem;
margin: 1rem;
padding: 0.3rem;

}
#sidebar  h4{
color: var(--text-white);
margin-bottom: 0.5rem;
text-align: left;
font-size: 1rem;
padding: 0.3rem;
}
#sidebar.active ul li a span.text {
    visibility: visible;
    opacity: 1;
    margin-left: 10px;
}
.fa-search{
margin-left: -30px;
color: #333;

}
/* Main Content Styling */
.main-content {
padding: 30px;
transition: margin-left 0.3s ease;
overflow-x: hidden;
max-width: 90%; /* Adjust to control how much space the content spans */
margin:0.5rem  5rem; /* Centers the content horizontally */
box-sizing: border-box;
margin-left: 180px;
width: calc(100% - 280px);

}
.main-content {
    transition: all 0.3s ease;
    margin-left: 100px;
    width: calc(100% - 100px);

}

#sidebar.active ~ .main-content {
    margin-left: 250px;
    width: calc(100% - 250px);
}

.main-content.expanded {
margin-left: 270px; /* Space for expanded sidebar */
width: calc(100% - 280px);
transition: margin-left 0.3s ease;
overflow-x: hidden;
padding: 30px;
}

/* Dashboard Card Styling */
.dashboard-card {
background:#ffffff;
border: 2px solid var(--primary-hover-color); /* Subtle border for structure */
border-radius: 12px; /* Smooth corners */
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), /* Soft shadow */
        0 1px 3px rgba(0, 0, 0, 0.08); /* Fine detail shadow */
padding: 20px 25px;
margin-bottom: 20px;
text-align: center;
transition: transform 0.3s ease, box-shadow 0.3s ease;
color: #333;
}

/* Hover Effect */
.dashboard-card:hover {
transform: translateY(-5px); /* Slight lift on hover */
box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15), /* More pronounced shadow */
        0 2px 4px rgba(0, 0, 0, 0.12); /* Fine detail shadow */
}

.dashboard-card h3 {
font-size: 40px;
color: var(--primary-hover-color);
margin-bottom: 10px;
}

/* Card Title */
.dashboard-card h4 {
font-size: 1.5rem;
font-weight: bold;
margin: 10px;
color: var(--primary-hover-color, #007bff);
}

.dashboard-card h4 a {
color: var(--primary-hover-color);
text-decoration: none;
font-weight: bold;
font-size: 1.3rem;
}

.dashboard-card h4 a:hover {
text-decoration: underline;
}

/* Card Value */
.dashboard-card p {
font-size: 2rem;
font-weight: bold;
margin: 0;
color: #333;
}

/* Optional Footer for Additional Info */
.dashboard-card .card-footer {
margin-top: 15px;
font-size: 0.9rem;
color: #666;
border-top: 1px solid #ccc;
padding-top: 10px;
}
.card-footer a {
color: var(--background-color);
text-decoration: none;
}

.card-footer a:hover {
text-decoration: underline;
}


/* Notification and Profile Styling */
.btn.position-relative i {
    font-size: 18px;
    color: var(--text-white);
}

.dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-white);
    border: none !important;
}

.dropdown-toggle:hover {
    color: var(--text-white);
    text-decoration: none;
    border: none !important;
}

.dropdown-menu {
    min-width: 200px;
}


.table-responsive {
background: #fff;
border-radius: 8px;
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.chart-container {
background: #fff;
padding: 20px;
border-radius: 8px;
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.line{
display: block;
width: 100%;
height: 2px;
color: var(--primary-color);
background-color: var(--primary-color);
margin-top: -1.5rem;
box-shadow: 2px 3px 8px solid rgba(0, 0, 0, 0.08);
margin-bottom: 5rem;

}
.greating{
margin-top: 6rem;
margin-left: 8rem;
transition: margin-left 0.3s ease;

}
.greating.expanded{
margin-top: 6rem;
margin-left: 20rem;
transition: margin-left 0.3s ease;
}
.greating > div{
color: var(--primary-hover-color);
}

.hidden{
display: none;
}
.section{
    opacity: 0;
    transition: opacity 0.5s ease; /* Smooth transition */
}
.section.active{
    opacity: 1;

}
#sidebar ul li a.active {
    background: var(--primary-hover-color);
    color: white; /* Optional: Change text color */
  }

/* Responsive Design */
@media (max-width: 768px) {

.toggle {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 20px;
        width: 30px;
        cursor: pointer;
    margin-left: 2rem;
    }

    .toggle div {
        height: 4px;
        width: 100%;
        background-color: var(--text-white);
        border-radius: 2px;
        transition: all 0.3s ease-in-out;
    }
    #sidebar.active {
        width: 50%;
    }
    #sidebar{
        width: 0;
    }

    .main-content {
margin-left: 0;
padding: 15px;
}

.dashboard-card {
margin-bottom: 15px;
}


}
/* Responsive Adjustments */
</style>
    <title>Admin Dashboard || Trustpoint</title>
  </head>
  <body>
    <!-- Header -->
<header class="header d-flex justify-content-between align-items-center">
      <div class="branding d-flex align-items-center">
        <img src="../img/logo/logo-1.png" alt="Logo" />
        <div class="toggle ms-5" onclick="toggleSidebar()">
          <div></div>
          <div></div>
          <div></div>
        </div>
        <!-- <span class="fw-bold fs-4 ms-3 ">Admin Dashboard</span> -->
        <div class="col-sm-4  d-flex justify-content-center align-items-center">
          <input
            type="text"
            class="form-control w-1000 px-4"
            placeholder="Search..."
            aria-label="Search"
          /><span class="fas fa-search"></span>
        </div>
      </div>

      <div class="d-flex align-items-center">
        <!-- Notifications -->
        <button
          class="btn position-relative me-3 text-light"
          aria-label="Notifications"
        >
          <img
            src="../img/logo/header-icon-05.svg"
            alt="Zoom Full View"
            style="
              height: 30px;
              width: 30px;
              background-color: white;
              border-radius: 50%;
              padding: 8px;
            "
          />
        </button>
        <button
          class="btn position-relative me-3 text-light"
          aria-label="Zoom Full View"
          style="border: none; padding: 0; background-color: transparent"
        >
          <img
            src="../img/logo/header-icon-04.svg"
            alt="Zoom Full View"
            style="
              height: 30px;
              width: 30px;
              background-color: white;
              border-radius: 50%;
              padding: 8px;
            "
          />
        </button>

        <!-- User Profile -->
        <div class="dropdown col-sm-8">
          <button
            class="btn dropdown-toggle col-sm-12"
            type="button"
            id="userMenu"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img
              src="../img/testimonial/jidda.jpg"
              alt="Profile Picture"
              class="rounded-circle"
              style="
                height: 35px;
                width: 35px;
                padding: 2px;
                background-color: var(--primary-color);
              "
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
      <h4>Menu</h4>
      <ul>
        <li>
          <a href="index.php"
            ><i class="fas fa-home"></i> <span class="text">Dashboard</span></a
          >
        </li>
        <li>
          <a href="index.php/#transaction"
            ><i class="fas fa-money-bill-wave"></i>
            <span class="text">Transaction</span></a
          >
        </li>
        <li>
          <a href="index.php/#users"
            ><i class="fas fa-users"></i> <span class="text">Users</span></a
          >
        </li>
        <li>
          <a href="index.php/#account"
            ><i class="fas fa-user-shield"></i>
            <span class="text">Account</span></a
          >
        </li>
        <li>
          <a href="index.php/#loans"
            ><i class="fas fa-hand-holding-usd"></i>
            <span class="text">Loan</span></a
          >
        </li>
        <li>
          <a href="index.php/#commission"
            ><i class="fas fa-handshake"></i>
            <span class="text">Commission</span></a
          >
        </li>
        <li>
          <a href="index.php/#referral"
            ><i class="fas fa-share-alt"></i>
            <span class="text">Referral</span></a
          >
        </li>
        <li>
          <a href="index.php/#setting"
            ><i class="fas fa-cogs"></i> <span class="text">Settings</span></a
          >
        </li>
        <li>
          <a href="index.php/#logout" class="text-danger"
            ><i class="fas fa-sign-out-alt"></i>
            <span class="text">Logout</span></a
          >
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Your main content goes here -->
    </div>

    <!-- thetransactions -->
<div class=" mt-5  main-content  ">

<!-- Filters -->
<div class="d-flex justify-content-between align-items-center mt-4 " >
 <div class="d-flex">
     <select class="form-select me-2" id="statusFilter" style="width: 200px">
         <option value="">Filter by Status</option>
         <option value="Successful">Successful</option>
         <option value="Pending">Pending</option>
         <option value="Failed">Failed</option>
     </select>
     <input type="date" class="form-control me-2" id="dateFilter" style="width: 200px" placeholder="Select Date">
     <input type="text" class="form-control" id="searchTransaction" style="width: 200px" placeholder="Search Transaction ID">
 </div>
 <button class="btn btn-primary" id="applyFilters">Apply Filters</button>
</div>

<!-- Real-Time Transactions Table -->
<div class="table-responsive mt-4">
 <table class="table table-striped">
     <thead class="thead-dark">
         <tr>
             <th>#</th>
             <th>Transaction ID</th>
             <th>Date</th>
             <th>Amount</th>
             <th>Status</th>
             <th>Action</th>
         </tr>
     </thead>
     <tbody id="transactionTable">
         <!-- Transactions will be loaded here via AJAX -->

     </tbody>
 </table>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation">
 <ul class="pagination justify-content-center mt-3" id="pagination">
     <!-- Pagination buttons will be loaded here -->
 </ul>
</nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const greeting = document.querySelector('.greating');

    sidebar.classList.toggle('active');

    if (sidebar.classList.contains('active')) {
        mainContent.style.marginLeft = '250px';
        greeting.style.marginLeft = '20rem';
    } else {
        mainContent.style.marginLeft = '60px';
        greeting.style.marginLeft = '8rem';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let currentPage = 1;
    let debounceTimer;

    function fetchDashboardStats() {
        fetch("./get_dashboard_stats.php")
            .then(response => response.json())
            .then(data => {
                animateNumber("totalUsers", data.total_users || 0);
                animateNumber("totalTransactions", data.total_transactions || 0, "₦");
                animateNumber("newSignups", data.new_signups || 0);

                updateTimestamp("usersUpdated");
                updateTimestamp("transactionsUpdated");
                updateTimestamp("signupsUpdated");
            })
            .catch(error => {
                console.error("Error fetching stats:", error);
            });
    }

    function animateNumber(id, endValue, prefix = "") {
        let element = document.getElementById(id);
        let startValue = parseFloat(element.innerText.replace(/[^0-9]/g, "")) || 0;
        let duration = 1000; // 1 second
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let currentValue = Math.floor(progress * (endValue - startValue) + startValue);
            element.innerText = prefix + new Intl.NumberFormat().format(currentValue);
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }

    function updateTimestamp(id) {
        let element = document.getElementById(id);
        element.innerText = `Updated ${new Date().toLocaleTimeString()}`;
    }

    setInterval(fetchDashboardStats, 5000);
    fetchDashboardStats();

    function fetchTransactions(page = 1, search = "") {
        let status = $("#statusFilter").val();
        let date = $("#dateFilter").val();

        $.ajax({
            url: "./fetch_transactions.php",
            method: "GET",
            data: { status: status, date: date, page: page, search: search },
            dataType: "json",
            success: function (response) {
                let transactionTable = $("#transactionTable");
                transactionTable.empty();

                if (!response.transactions || response.transactions.length === 0) {
                    transactionTable.append('<tr><td colspan="6" class="text-center">No transactions found</td></tr>');
                } else {
                    response.transactions.forEach((transaction, index) => {
                        let badgeClass = transaction.status === "Successful" ? "bg-success" :
                            transaction.status === "Pending" ? "bg-warning" :
                            "bg-danger";

                        let row = `
                          <tr>
                              <td>${index + 1}</td>
                              <td>${transaction.transaction_no}</td>
                              <td>${transaction.transaction_date}</td>
                              <td>₦${transaction.amount}</td>
                              <td><span class="badge ${badgeClass}">${transaction.status}</span></td>
                              <td>
                                  <button class="btn btn-primary btn-sm"
                                          onclick="viewTransaction('${transaction.transaction_no}', '${transaction.transaction_date}', '${transaction.amount}', '${transaction.status}')">
                                      View
                                  </button>
                              </td>
                          </tr>
                        `;
                        transactionTable.append(row);
                    });
                }

                let pagination = $("#pagination");
                pagination.empty();
                if (response.totalPages && response.totalPages > 1) {
                    for (let i = 1; i <= response.totalPages; i++) {
                        let activeClass = i === page ? "active" : "";
                        pagination.append(`<li class="page-item ${activeClass}"><a class="page-link pagination-btn" href="#" data-page="${i}">${i}</a></li>`);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching transactions:", error);
                $("#transactionTable").html('<tr><td colspan="6" class="text-center text-danger">Failed to load transactions</td></tr>');
            }
        });
    }

    $("#searchTransaction").on("keyup", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            let search = $(this).val();
            fetchTransactions(1, search);
        }, 500);
    });

    $("#applyFilters").click(function () {
        fetchTransactions();
    });

    $(document).on("click", ".pagination-btn", function (e) {
        e.preventDefault();
        currentPage = $(this).data("page");
        fetchTransactions(currentPage);
    });

    let refreshInterval = setInterval(() => fetchTransactions(currentPage), 5000);

    $("#applyFilters, #searchTransaction").on("input", function () {
        clearInterval(refreshInterval);
        refreshInterval = setInterval(() => fetchTransactions(currentPage), 5000);
    });

    fetchTransactions();
});

function viewTransaction(transaction_no, date, amount, status) {
    document.getElementById("modalTransactionID").innerText = transaction_no;
    document.getElementById("modalTransactionDate").innerText = date;
    document.getElementById("modalTransactionAmount").innerText = `₦${amount}`;

    let statusElement = document.getElementById("modalTransactionStatus");
    statusElement.innerText = status;
    statusElement.className = "badge bg-secondary";

    let formattedStatus = status.trim().toLowerCase();

    if (formattedStatus === "successful") {
        statusElement.classList.replace("bg-secondary", "bg-success");
    } else if (formattedStatus === "pending") {
        statusElement.classList.replace("bg-secondary", "bg-warning");
    } else {
        statusElement.classList.replace("bg-secondary", "bg-danger");
    }

    let transactionModal = new bootstrap.Modal(document.getElementById("transactionModal"));
    transactionModal.show();
}

function copyTransactionID() {
    let transactionID = document.getElementById("modalTransactionID").innerText;

    navigator.clipboard.writeText(transactionID).then(() => {
        showToast("Transaction ID copied to clipboard!");
    }).catch(err => {
        console.error("Failed to copy:", err);
    });
}

function showToast(message) {
    let toastElement = document.getElementById("transactionToast");
    let toastBody = document.getElementById("toastBody");
    toastBody.innerText = message;

    let toast = new bootstrap.Toast(toastElement);
    toast.show();
}
</script>
  </body>
</html>