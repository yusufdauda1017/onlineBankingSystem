<?php
session_start();

// Define timeout duration in seconds (1 minute = 60 seconds)
$timeout_duration =  5*60;


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If no session, redirect to login page
    header('Location: ../login.php');
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
        header('Location: ../src/login-form/index.php');
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
    <title>Admin Dashboard</title>
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
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="..\img\logo\logo.svg" type="image/svg">
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
          <a href="#dashboard"
            ><i class="fas fa-home"></i> <span class="text">Dashboard</span></a
          >
        </li>
        <li>
          <a href="#transaction"
            ><i class="fas fa-money-bill-wave"></i>
            <span class="text">Transaction</span></a
          >
        </li>
        <li>
          <a href="#users"
            ><i class="fas fa-users"></i> <span class="text">Users</span></a
          >
        </li>
        <li>
          <a href="#account"
            ><i class="fas fa-user-shield"></i>
            <span class="text">Account</span></a
          >
        </li>
        <li>
          <a href="#loans"
            ><i class="fas fa-hand-holding-usd"></i>
            <span class="text">Loan</span></a
          >
        </li>
        <li>
          <a href="#commission"
            ><i class="fas fa-handshake"></i>
            <span class="text">Commission</span></a
          >
        </li>
        <li>
          <a href="#referral"
            ><i class="fas fa-share-alt"></i>
            <span class="text">Referral</span></a
          >
        </li>
        <li>
          <a href="#setting"
            ><i class="fas fa-cogs"></i> <span class="text">Settings</span></a
          >
        </li>
        <li>
          <a href="#logout" class="text-danger"
            ><i class="fas fa-sign-out-alt"></i>
            <span class="text">Logout</span></a
          >
        </li>
      </ul>
    </div>
    <!-- Main Content -->

  <div class="dashboard section active" id="dashboard">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><<h4 class="mb-4">Welcome back <?php echo $_SESSION['user_name']; ?>!</h4>        </div>
        <div class="me-3">
          <h6 class="mb-4"><span style="color: #9e9d9d">Dashboard</span></h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <!-- Dashboard Cards -->
        <div class="row d-flex justify-content-center align-items-center">
    <!-- Total Users Card -->
    <div class="col-md-4 mb-3">
        <div class="dashboard-card shadow p-4 text-center">
            <i class="fas fa-users fa-3x text-primary mb-2"></i>
            <h4>Total Users</h4>
            <p id="totalUsers" class="stat-value">Loading...</p>
            <div class="card-footer text-muted" id="usersUpdated">Updating...</div>
        </div>
    </div>

    <!-- Total Transactions Card -->
    <div class="col-md-4 mb-3">
        <div class="dashboard-card shadow p-4 text-center">
            <i class="fas fa-wallet fa-3x text-success mb-2"></i>
            <h4>Total Transactions</h4>
            <p id="totalTransactions" class="stat-value">Loading...</p>
            <div class="card-footer text-muted" id="transactionsUpdated">Updating...</div>
        </div>
    </div>

    <!-- New Signups Card -->
    <div class="col-md-4 mb-3">
        <div class="dashboard-card shadow p-4 text-center">
            <i class="fas fa-user-plus fa-3x text-warning mb-2"></i>
            <h4>New Signups</h4>
            <p id="newSignups" class="stat-value">Loading...</p>
            <div class="card-footer text-muted" id="signupsUpdated">Updating...</div>
        </div>
    </div>
</div>

        <div class="line"></div>

      <!-- Filters -->
<div class="d-flex justify-content-between align-items-center mt-4">
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


        <!-- Chart Section -->
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="chart-container">
              <h5>Transactions Overview</h5>
              <canvas id="transactionsChart"></canvas>
            </div>
          </div>
          <div class="col-md-6">
            <div class="chart-container">
              <h5>User Growth</h5>
              <canvas id="userGrowthChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Dashboard Cards -->
    <!-- Dashboard Cards -->
    <div class="transaction section hidden" id="transaction">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Transaction</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Transaction</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-clipboard"></i></h3>
              <h4><a href="index2.php">View All Transactions</a></h4>
              <div class="card-footer">
                <a href="index2.php"
                  >Shows a list of all transfers and deposits made by users.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-check"></i></h3>
              <h4><a href="#total-users">Approve Transactions</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Handles flagged or pending transactions made .</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-times"></i></h3>
              <h4><a href="#total-users">Failed Transactions</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >See the Lists transactions that failed due to issues like
                  insufficient funds.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-chart-bar"></i></h3>
              <h4><a href="#total-users">Transaction Reports</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Generates daily, weekly, or monthly reports of all
                  transactions.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-flag"></i></h3>
              <h4><a href="#total-users">Flag Suspicious Activity</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Highlights transactions with unusual patterns.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-search"></i></h3>
              <h4><a href="#total-users">Audit Trail</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Tracks changes or corrections made by admins..</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>




<!-- end of it -->
    <div class="users section hidden" id="users">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Users Management</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Users</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-plus"></i></h3>
              <h4><a href="#total-users">Add New User</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users">create user accounts manually.</a>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-edit"></i></h3>
              <h4><a href="#total-users">Edit User Details</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users">update user information.</a>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-lock"></i></h3>
              <h4><a href="#total-users">Deactivate User Account</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Temporarily blocks a user from using the system.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-money-bill-wave"></i></h3>
              <h4><a href="#total-users">Credit User Account</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users"
                  >Add money to a user's account manually.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-naira-sign"></i></h3>
              <h4><a href="#total-users">Debit User Account</a></h4>
              <div class="card-footer">
                <a href="#total-users">
                  Deduct money from a user's account manually.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-lock"></i></h3>
              <h4><a href="#total-users">User Access Control</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users">Manage Access.</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-unlock-alt"></i></h3>
              <h4><a href="#total-users">Reset Password</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users"
                  >Helps reset forgotten passwords for users.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-id-card"></i></h3>
              <h4><a href="#total-users">View User Profile</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Displays user details, transaction history, and current
                  status.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-cog"></i></h3>
              <h4><a href="#total-users">Assign Roles</a></h4>
              <div class="card-footer mt-3">
                <a href="#total-users">change user roles.</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="account section hidden" id="account">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Account Monitoring</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Account</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-clock"></i></h3>
              <h4><a href="#total-users">Real-Time Monitoring</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Represents live updates and continuous tracking of account
                  activities.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-toggle-on"></i></h3>
              <h4><a href="#total-users">Account Status</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Indicates the active, dormant, or blocked status of
                  accounts.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-shield-alt"></i></h3>
              <h4><a href="#total-users">Fraud Detection Alerts</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Symbolizes security alerts for potential fraud activities.</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-chart-line"></i></h3>
              <h4><a href="#total-users">Account Usage Reports</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Represents data trends and graphical reports of account
                  usage.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-link"></i></h3>
              <h4><a href="#total-users">Linked Accounts.</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >hows connections between accounts belonging to the same
                  user.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-lock"></i></h3>
              <h4><a href="#total-users">Manual Freezing</a></h4>
              <div class="card-footer">
                <a href="#total-users">
                  Illustrates the action of freezing an account for security
                  purposes.</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-unlock-alt"></i></h3>
              <h4><a href="#total-users">Account Recovery</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Represents data trends and graphical reports of account
                  usage.</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="loan section hidden" id="loans">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Loan Management</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Loan</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-handshake"></i></h3>
              <h4><a href="#total-users">Interest-Free
                Loans</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Our Qard Hasan loans are
                  completely interest-free</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-hand-holding-heart"></i></h3>
              <h4><a href="#total-users">Zakat
                Management</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >calculate and
                  distribute your Zakat the right way.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-briefcase"></i></h3>
              <h4><a href="#total-users">Wealth
                Management</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >manage money in a way that
                  is fair, Sharia-compliant.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-heart"></i></h3>
              <h4><a href="#total-users">Charity &
                Endowments</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  > support long-term
                  community projects through Waqf
                  (endowments).</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-globe"></i></h3>
              <h4><a href="#total-users">Social Funds</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >programs that offer financial help and
                  community support for those in need.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-shield-alt"></i></h3>
              <h4><a href="#total-users">Islamic
                insurance</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#total-users"
                  >Takaful</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-seedling"></i></h3>
              <h4><a href="#total-users">Development
                Projects</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#total-users"
                  > Sustainable Initiatives.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-hands-helping"></i></h3>
              <h4><a href="#total-users">Financial
                Aid</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#total-users"
                  >Assistance Programs.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fa fa-gift"></i></h3>
              <h4><a href="#total-users">Donation</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#total-users"
                  >Contributions</a
                >
              </div>
            </div>
          </div>
        </div>
          <h3 class="text-center mt-3 mb-5 text-success" >The loan Center</h3>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-file-alt"></i></h3>
              <h4><a href="#total-users">Loan Application Review</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  > Reviewing and managing loan applications.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-calendar-check"></i></h3>
              <h4><a href="#total-users">Repayment Tracking</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Approve or reject loan requests, including providing reasons for rejection.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-chart-bar"></i></h3>
              <h4><a href="#total-users">Loan Reports</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Generate reports on disbursed and repaid loans for analysis.</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-cogs"></i></h3>
              <h4><a href="#total-users">Loan Policy Settings</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Define loan terms like maximum amount, duration, and eligibility.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-history"></i></h3>
              <h4><a href="#total-users">User Loan History</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >View the complete loan history of individual users, including status.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-exclamation-triangle"></i></h3>
              <h4><a href="#total-users">Loan Overdue</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Highlight overdue loans, send reminders, and take action for non-repayment.</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="Commission section hidden" id="commission">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Commission Management</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Commission</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-percent"></i></h3>
              <h4><a href="#total-users">Set Commission Rates</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Define the percentage or flat fee for commissions, including referral bonuses or transaction fees.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-money-bill-alt"></i></h3>
              <h4><a href="#total-users">View Earned</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >View a detailed list of commissions earned by the system, categorized by activity type.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-friends"></i></h3>
              <h4><a href="#total-users">Referral Tracking</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Monitor commissions earned from user referrals, including the count of referred users.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-calendar-alt"></i></h3>
              <h4><a href="#total-users">Payout Schedule</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Set the frequency of commission payouts, such as weekly or monthly.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-edit"></i></h3>
              <h4><a href="#total-users">Commission Adjustment</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Manually adjust commissions for disputes or corrections when needed.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-chart-line"></i></h3>
              <h4><a href="#total-users">Analytics</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Analyze trends in commissions with visual charts to improve marketing strategies.</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-shield-alt"></i></h3>
              <h4><a href="#total-users">Fraud and Validation </a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Detects and prevents fraudulent activities in commission payouts.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-file-invoice"></i></h3>
              <h4><a href="#total-users">Disbursement History</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Tracks and manages detailed logs of all commission payouts.</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="Referral section hidden" id="referral">
      <div
        class="greating greating d-flex justify-content-between align-items-center"
        id=""
      >
        <div class=""><h4 class="mb-4">Referral Settings</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Referral</span>
          </h6>
        </div>
      </div>
      <div class="main-content container-fluid content" id="">
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-gavel"></i></h3>
              <h4><a href="#total-users">Referral Rules</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Set rules like reward amounts and eligibility for the referral program.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-qrcode"></i></h3>
              <h4><a href="#total-users">Referral Code</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Generate and track unique codes for user referrals.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-chart-pie"></i></h3>
              <h4><a href="#total-users">Referral Statistics</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Track the number of referrals made by each user.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-gift"></i></h3>
              <h4><a href="#total-users">Reward Disbursement</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Send rewards to users after referral verification.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-bell"></i></h3>
              <h4><a href="#total-users">Referral Notifications</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Notify users when their referrals successfully sign up.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-file-alt"></i></h3>
              <h4><a href="#total-users">Audit Logs</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Track all referral-related activities to prevent misuse.</a
                >
              </div>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-trophy"></i></h3>
              <h4><a href="#total-users">Referral Campaign</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Analyze the success of referral campaigns with performance metrics.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-bell"></i></h3>
              <h4><a href="#total-users">Referral Notifications</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Notify users when their referrals successfully sign up.</a
                >
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-file-alt"></i></h3>
              <h4><a href="#total-users">Audit Logs</a></h4>
              <div class="card-footer">
                <a href="#total-users"
                  >Track all referral-related activities to prevent misuse.</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="Setting section hidden" id="setting">
      <div class="greating greating d-flex justify-content-between align-items-center" id="">
        <div><h4 class="mb-4">Settings</h4></div>
        <div class="me-3">
          <h6 class="mb-4">
            Dashboard/ <span style="color: #9e9d9d">Settings</span>
          </h6>
        </div>
      </div>

      <div class="main-content container-fluid content" id="">
        <!-- General Settings Section -->
        <h3 class="text-center mt-3 mb-5 text-success">General Settings</h3>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-cogs"></i></h3>
              <h4><a href="#general-settings">General Settings</a></h4>
              <div class="card-footer">
                <a href="#general-settings">Manage system-wide preferences like name, logo, and timezone.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-undo-alt"></i></h3>
              <h4><a href="#reset-settings">Reset Settings</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#reset-settings">Restore default settings for the system.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-plug"></i></h3>
              <h4><a href="#integration-settings">Integration Settings</a></h4>
              <div class="card-footer">
                <a href="#integration-settings">Set up third-party integrations like payment gateways and APIs.</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Security Settings Section -->
        <h3 class="text-center mt-3 mb-5 text-success">Security Settings</h3>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-shield-alt"></i></h3>
              <h4><a href="#security-settings">Security Settings</a></h4>
              <div class="card-footer">
                <a href="#security-settings">Set password policies, enable 2FA, and manage CAPTCHA settings.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-key"></i></h3>
              <h4><a href="#change-password">Change Password</a></h4>
              <div class="card-footer mt-3 mb-3">
                <a href="#change-password">Update your account password securely.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-redo"></i></h3>
              <h4><a href="#recover-account">Recover Account</a></h4>
              <div class="card-footer">
                <a href="#recover-account">Recover your account in case of forgotten credentials.</a>
              </div>
            </div>
          </div>
        </div>

        <!-- User Preferences Section -->
        <h3 class="text-center mt-3 mb-5 text-success">User Preferences</h3>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-user-cog"></i></h3>
              <h4><a href="#user-preferences">User Preferences</a></h4>
              <div class="card-footer">
                <a href="#user-preferences">Allow users to customize their experience, such as themes and languages.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-bell"></i></h3>
              <h4><a href="#notification-settings">Notification Settings</a></h4>
              <div class="card-footer">
                <a href="#notification-settings">Configure email and SMS notifications for system events.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-comment-dots"></i></h3>
              <h4><a href="#feedback-management">Feedback Management</a></h4>
              <div class="card-footer">
                <a href="#feedback-management">Collect user feedback and respond to queries or complaints.</a>
              </div>
            </div>
          </div>
        </div>

        <!-- System Administration Section -->
        <h3 class="text-center mt-3 mb-5 text-success">System Administration</h3>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-users-cog"></i></h3>
              <h4><a href="#role-permission">Role & Permission</a></h4>
              <div class="card-footer">
                <a href="#role-permission">Define roles and control access to features with permissions.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-database"></i></h3>
              <h4><a href="#backup-restore">Backup & Restore</a></h4>
              <div class="card-footer">
                <a href="#backup-restore">Schedule automatic backups or restore data when needed.</a>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-3">
            <div class="dashboard-card">
              <h3><i class="fas fa-tools"></i></h3>
              <h4><a href="#system-maintenance">System Maintenance</a></h4>
              <div class="card-footer">
                <a href="#system-maintenance">Activate maintenance mode and display custom messages.</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>





<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Transaction ID:</strong> <span id="modalTransactionID"></span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="copyTransactionID()">Copy</button>
                </p>
                <p><strong>Date:</strong> <span id="modalTransactionDate"></span></p>
                <p><strong>Amount:</strong> <span id="modalTransactionAmount"></span></p>
                <p><strong>Status:</strong> <span id="modalTransactionStatus"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="script.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    let currentPage = 1;
    let debounceTimer;

    /** Fetch and update dashboard stats */
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

    /** Smooth number animation */
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

    /** Update last updated timestamp */
    function updateTimestamp(id) {
        let element = document.getElementById(id);
        element.innerText = `Updated ${new Date().toLocaleTimeString()}`;
    }

    /** Fetch dashboard stats every 5 seconds */
    setInterval(fetchDashboardStats, 5000);
    fetchDashboardStats(); // Initial load

    /** Fetch transactions with filters & pagination */
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
                              <td> <!-- ✅ Wrapped View button inside <td> -->
                                  <button class="btn btn-primary btn-sm"
                                          onclick="viewTransaction( '${transaction.transaction_no}', '${transaction.transaction_date}', '${transaction.amount}', '${transaction.status}')">
                                      View
                                  </button>
                              </td>
                          </tr>
                        `;
                        transactionTable.append(row);
                    });
                }

                // Pagination Handling
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

    /** Debounced Live Search */
    $("#searchTransaction").on("keyup", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            let search = $(this).val();
            fetchTransactions(1, search);
        }, 500); // 500ms delay
    });

    /** Apply Filters */
    $("#applyFilters").click(function () {
        fetchTransactions();
    });

    /** Pagination Click Handling */
    $(document).on("click", ".pagination-btn", function (e) {
        e.preventDefault();
        currentPage = $(this).data("page");
        fetchTransactions(currentPage);
    });

    /** Auto Refresh Every 5 Seconds (resets on filter change) */
    let refreshInterval = setInterval(() => fetchTransactions(currentPage), 5000);

    $("#applyFilters, #searchTransaction").on("input", function () {
        clearInterval(refreshInterval);
        refreshInterval = setInterval(() => fetchTransactions(currentPage), 5000);
    });

    /** Initial Load */
    fetchTransactions();


});
// Function to show transaction details on button click
function viewTransaction(transaction_no, date, amount, status) {
    // Set transaction details inside the modal
    document.getElementById("modalTransactionID").innerText = transaction_no;
    document.getElementById("modalTransactionDate").innerText = date;
    document.getElementById("modalTransactionAmount").innerText = `₦${amount}`;

    // Apply status badge with color
    let statusElement = document.getElementById("modalTransactionStatus");
    statusElement.innerText = status;
    statusElement.className = "badge bg-secondary"; // Reset to default class

    let formattedStatus = status.trim().toLowerCase(); // Ensure consistency

    if (formattedStatus === "successful") {
        statusElement.classList.replace("bg-secondary", "bg-success");
    } else if (formattedStatus === "pending") {
        statusElement.classList.replace("bg-secondary", "bg-warning");
    } else {
        statusElement.classList.replace("bg-secondary", "bg-danger");
    }

    // Show the Bootstrap modal
    let transactionModal = new bootstrap.Modal(document.getElementById("transactionModal"));
    transactionModal.show();
}

// Function to copy transaction ID with a toast notification
function copyTransactionID() {
    let transactionID = document.getElementById("modalTransactionID").innerText;

    navigator.clipboard.writeText(transactionID).then(() => {
        showToast("Transaction ID copied to clipboard!"); // Show toast notification
    }).catch(err => {
        console.error("Failed to copy:", err);
    });
}

// Function to show a Bootstrap toast notification
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
