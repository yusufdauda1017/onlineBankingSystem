<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '', // Change to your actual domain in production
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Prevent unauthorized access
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login-form/index.php');
    exit();
}

// Prevent session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Store user agent hash to prevent session hijacking
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT']);
} elseif ($_SESSION['user_agent'] !== hash('sha256', $_SERVER['HTTP_USER_AGENT'])) {
    session_unset();
    session_destroy();
    header('Location: ../login-form/index.php');
    exit();
}

// Define timeout duration (2 minutes)
$timeout_duration = 120;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: ../login-form/index.php');
    exit();
}
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/1d6525ef6a.js" crossorigin="anonymous"></script>
    <!-- Swiper CSS (if needed) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <link rel="icon" href="../img/logo/logo.svg" type="image/svg+xml" />
    <title>Money Transfer</title>
    <style>

:root {
            --primary-color: #559403;
            --primary-hover-color: #005313;
            --background-color: #001f10;
            --text-black: #333;
            --text-white: #eeeeee;
            --primary-font: 'Poppins', sans-serif;
            --secondary-font: 'Inter', sans-serif;
        }

      html,
      body {
        height: 100%;
        margin: 0;
        display: flex;
        justify-content: center; /* Center horizontally */
        align-items: center;     /* Center vertically */
        width: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        min-height: 100vh;
      }
      .container {
        max-width: 450px;
        width: 100%;
        padding: 10px;
      }
      .hidden { display: none; }
      .hidden-text {
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    .visible-text {
        visibility: visible;
        opacity: 1;
    }
      .header {
        text-align: center;
        padding: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        background: #005313;
      }
      .header img {
        border-radius: 10px;
        max-width: 100%;
        height: auto;
      }
      .shadow {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
      }
      /* Floating label input styling */
      .input-container { position: relative; margin-bottom: 1.5rem; }
      .floating-input {
        width: 100%;
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 0.6rem;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s ease, padding 0.3s ease;
        background: transparent;
      }
      .floating-input:focus { border-color: #218838; box-shadow: none; }
      .floating-label {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        pointer-events: none;
        background: white;
        padding: 0 5px;
        font-size: 1rem;
        color: #6c757d;
      }
      .floating-input:focus + .floating-label,
      .floating-input:not(:placeholder-shown) + .floating-label {
        top: 0px;
        font-size: 0.8rem;
        color: #218838;
      }
      /* Textarea specific fix */
      .floating-input:focus + .floating-label,
      .floating-input:not(:placeholder-shown) + .floating-label { top: 0px; }
      .amount {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
      }
      .amount::after {
        content: "→";
        font-size: 44px;
        color: #005313;
        position: absolute;
        left: 30%;
        top: 0%;
      }
      .toggle-eye {
        cursor: pointer;
        margin-left: 5px;
        color: #005313;
      }
      .toggle-eye:hover { color: rgb(0, 146, 34); }
      .receipt-container {
    position: relative;
    max-width: 400px;
    width: 100%;
    padding: 30px;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: none; /* Remove direct background */
    z-index: 1;
    overflow: hidden;
}
 /* PIN Modal */
 .modal-content {
    background-color: var(--background-color);
    color: var(--text-white) !important;
    border-radius: 12px;
}

.modal-header {
    background-color: var(--primary-color);
    color: var(--text-white);
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-top: none;
}

.modal-title {
    font-weight: 600 !important;
    font-size: 1.2rem !important;
}

/* PIN Input Fields */
.pin-box {
    width: 50px;
    height: 50px;
    font-size: 1.8rem;
    font-weight: bold;
    text-align: center;
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    background: transparent;
    color: var(--text-white);
    outline: none;
    transition: all 0.3s ease-in-out;
}

.pin-box:focus {
    border-color: var(--primary-hover-color);
    box-shadow: 0 0 8px rgba(85, 148, 3, 0.5);
}

/* Error Animation */
.shake {
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
}

.error {
    border-color: #ff4d4d !important;
}

#pinError {
    font-size: 0.85rem;
    color: #ff4d4d;
}
/* button{
width: 45% !important;
} */

/* Pseudo-element for background image */
.receipt-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('../img/logo/logo.svg');
    background-size: calc(100% / 30) auto;
    background-repeat: repeat;
    opacity: 0.5; /* Controls transparency */
    z-index: -1; /* Keeps it behind the content */
}

/* Ensure all text and content remain fully visible */
.receipt-header,
.receipt-body,
.receipt-footer {
    position: relative;
    z-index: 2;
}

.receipt-header {
    text-align: center;
    margin-bottom: 25px;
}

.receipt-header img {
    max-width: 120px;
    margin-bottom: 15px;
}

.receipt-header h2 {
    color: #005313;
    font-weight: 700;
    font-size: 24px;
    margin: 0;
}

.receipt-body {
    margin-top: 15px;
}

.receipt-body p {
    margin: 10px 0;
    font-size: 12px;
    color: #333333;
    font-weight: 500;
}

.receipt-body strong {
    color: #005313;
    font-weight: 700;
}

.receipt-body hr {
    border-top: 1px solid #000;
    margin: 15px 0;
display:block;
font-weight: 500;
}

.receipt-footer {
    text-align: center;
    margin-top: 25px;
}

.receipt-footer button {
    margin: 8px;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.receipt-footer button:hover {
    transform: scale(1.05);
}

.btn-primary {
    background-color: #005313;
    border: none;
    color: white;
}

.btn-primary:hover {
    background-color: #00400d;
}

.btn-outline-secondary {
    border-color: #005313;
    color: #005313;
}

.btn-outline-secondary:hover {
    background-color: #005313;
    color: #fff;
}

@media print {
    body * {
        visibility: hidden;
    }
    .receipt-container, .receipt-container * {
        visibility: visible;
    }
    .receipt-container {
        box-shadow: none;
        border: none;
    }
    .receipt-footer button {
        display: none;
    }
}
  /* Default Styling (for larger screens) */
  .swal2-popup {
      width: 30vw !important;
      max-width: 330px !important;
      min-width: 250px !important;
      padding: 8px !important;
      border-radius: 10px !important;
  }

  /* Title Styling */
  .swal2-title {
      font-size: 1.3rem !important;
  }

  /* Icon Styling */
  .swal2-icon {
      font-size: 0.6rem !important;
      width: 50px !important;
      height: 50px !important;
  }

  /* Text Styling */
  .swal2-html-container {
      font-size: 0.8rem !important;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
      .swal2-popup {
          width: 80vw !important; /* Make it take more width on small screens */
          max-width: 300px !important;
          min-width: 250px !important;
          padding: 8px !important;
      }

      .swal2-title {
          font-size: 1.1rem !important; /* Reduce title size for smaller screens */
      }

      .swal2-icon {
          width: 50px !important;
          height: 50px !important;
      }

      .swal2-html-container {
          font-size: 0.7rem !important;
      }
  }

  @media (max-width: 480px) {
      .swal2-popup {
          width: 90vw !important; /* Almost full width on very small screens */
          max-width: 280px !important;
          padding: 8px !important;
      }

      .swal2-title {
          font-size: 1rem !important;
      }

      .swal2-html-container {
          font-size: 0.7rem !important;
      }
  }

    </style>
  </head>
  <body>
    <div class="container" id="transferForm">

        <div class="header">
          <img src="../img/logo/logo-1.png" alt="Brand Logo" />
        </div>
        <!-- Transfer Form Section -->
        <div class="shadow px-3" >

        <div class="account-details d-flex justify-content-between align-items-center px-3">
    <div>
        <p class="mb-0"><strong>Account Number:</strong></p>
        <p>
            <span id="userAccount" class="hidden-text"><?php echo htmlspecialchars($_SESSION['account_number'] ?? ''); ?></span>
            <i class="fas fa-eye toggle-eye" onclick="toggleVisibility('userAccount', this)"></i>
        </p>
    </div>
    <div>
        <p class="mb-0 text-success"><strong>Balance:</strong></p>
        <p>
            <span id="account_balance" class="hidden-text"></span>
            <i class="fas fa-eye toggle-eye" onclick="toggleVisibility('account_balance', this)"></i>
        </p>
    </div>
</div>

          <!-- Transfer Form -->
          <div>
            <h4 class="text-center text-success mt-2 mb-4">Transfer Money</h4>
            <div class="input-container mb-0">
              <input type="text" class="floating-input" placeholder=" " id="searchRecipient" />
              <label class="floating-label">Recipient Account Number</label>
            </div>
            <div class="spinner-border text-primary mt-4 mb-4 d-none" id="loadingSpinner" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <small id="account-name" class="text-muted text-end mb-4"></small>
            <div class="input-container mt-3 mb-0 hidden" id="amountSection">
              <input type="number" class="floating-input" placeholder=" " min="100" max="1000000" id="amountInput" />
              <label class="floating-label">Amount</label>
            </div>
            <div class="spinner-border text-primary mt-3 d-none" id="loadingSpinner2" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mb-0 mt-2 d-flex justify-content-between align-items-center px-3">
              <small id="amount-warning" class="text-danger"></small>
              <small id="amount-charges" class="text-muted"></small>
            </div>
            <div class="input-container mt-3">
              <textarea class="floating-input" placeholder=" " id="note"></textarea>
              <label class="floating-label">Transfer Note (Optional)</label>
            </div>
            <div class="row justify-content-center">
              <div class="col-auto">
                <button type="button" class="btn btn-danger" style="width: 180px" id="cancel-transaction">
                  Cancel
                </button>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-success" style="width: 180px" id="btn-transfer">
                  Continue
                </button>
              </div>
            </div>
          </div>
        </div>
        </div>


        <div class="container hidden" id="confirmationScreen">
<div class="header">
  <img src="../img/logo/logo-1.png" alt="Brand Logo" />
</div>
        <!-- Confirmation Screen -->
        <div  class=" shadow p-3">
          <h5 class="text-center text-primary">Confirm Transfer</h5>
          <div class="row d-flex align-items-center mt-3 mb-3">
            <!-- Sender Details -->
            <div class="senders col-4">
              <p class="mb-0"><strong>From: </strong></p>
              <p id="confirmSenderName" class="mb-0"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></p>
              <p class="mb-0">
                <span id="confirmSenderAccount"><?php echo htmlspecialchars($_SESSION['account_number'] ?? ''); ?></span>
              </p>
            </div>
            <!-- Amount with Arrow -->
            <div class="amount col-4 d-flex justify-content-center align-items-center position-relative">
              <p><span id="confirmAmount"></span></p>
            </div>
            <!-- Receiver Details -->
            <div class="recievers-details col-4">
              <p class="mb-0"><strong>To: </strong></p>
              <p class="mb-0"><span id="confirmRecipient"></span></p>
              <p id="confirmAccount" class="mb-0"></p>
            </div>
          </div>
          <p class="text-center"><strong>Transfer Fee:</strong> <span id="confirmFee"></span></p>
          <button class="btn btn-success w-100" id="pinSection">
            Confirm Transfer
          </button>
          <button class="btn btn-outline-danger w-100 mt-2" id="goBack">
            Cancel
          </button>
        </div>

        </div>
      <div class="container hidden" id="processingScreen">
    <div class="header text-center">
        <img src="../img/logo/logo-1.png" alt="Brand Logo" class="logo" />
    </div>
    <div class="processing-content text-center shadow p-4 rounded">
        <div class="spinner-border text-success mt-3" role="status"></div>
        <p class="mt-3 font-weight-bold">Processing transaction...</p>
    </div>
</div>

<div class="receipt-container hidden" id="receiptScreen">
    <div class="receipt-header text-center">
        <img src="../img/logo/Untitled design (9).png" alt="Brand Logo" class="logo" />
        <h2 class="mb-2">Transaction Receipt</h2>
        <h3 class=" text-success"><span id="receipttotalDeduction"></span></h3>
    </div>
    <hr class="divider">
    <div class="receipt-body">
        <p><strong>Transaction ID:</strong> <span id="receiptTransactionId"></span></p>
        <p><strong>Date:</strong> <span id="receiptDate"></span></p>
        <div class="transaction-details">
            <p class = "mb-0 mt-0"><strong>Senders details:</strong></p>
            <div class="text-end">
                <p class="mb-0 mt-0"><span id="receiptSenderName"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span></p>
                <p class="mb-0 mt-0"><span id="receiptSenderAccount"><?php echo htmlspecialchars($_SESSION['account_number'] ?? ''); ?></span></p>
            </div>
        </div>
        <div class="transaction-details ">
            <p class = "mb-0 mt-0"><strong>Receivers details:</strong></p>
            <div class="text-end">
                <p class="mb-0 mt-0"><span id="receiptRecipient"></span></p>
                <p class="mb-0 mt-0"><span id="receiptAccount"></span></p>
            </div>
        </div>
        <p><strong>Amount:</strong> <span id="receiptAmount"></span></p>
        <p><strong>Status:</strong> <span id="receiptFee"></span></p>
        <p><strong>Note:</strong> <span id="receiptNote"></span></p>
        <hr class="divider">
    </div>
    <div class="receipt-footer text-center">
        <small class="text-muted">Thank you for banking with us!</small>
        <small class="text-muted">For support, contact our 24/7 help center.</small>
        <div class="button-group ">
            <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
            <button class="btn btn-outline-secondary" onclick="shareReceipt()">Back</button>
        </div>
    </div>
</div>









 <!-- PIN Modal -->
 <div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold mx-auto" id="pinModalLabel">Enter Transaction PIN</h5>
                </div>

                <!-- Modal Body -->
                <div class="modal-body text-center">
                    <p class="text-light mb-3">Enter your secure 4-digit PIN</p>

                    <!-- PIN Input Fields -->
                    <div class="d-flex justify-content-center gap-3" id="pinContainer">
                        <input type="password" class="pin-box" maxlength="1">
                        <input type="password" class="pin-box" maxlength="1">
                        <input type="password" class="pin-box" maxlength="1">
                        <input type="password" class="pin-box" maxlength="1">
                    </div>

                    <small class="d-none mt-2" id="pinError">Incorrect PIN. Try again.</small>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-light rounded-pill" id="goToProcess">
    Cancel
</button>
                    <button type="button" class="btn btn-success  rounded-pill fw-bold" id="confirmTransaction" disabled>
                        Confirm
                    </button>
                </div>

            </div>
        </div>
    </div>
  </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./internal.js"></script>
<script>
  function toggleVisibility(id, icon) {
    let element = document.getElementById(id);
    if (element.classList.contains("hidden-text")) {
        element.classList.replace("hidden-text", "visible-text");
        icon.classList.replace("fa-eye", "fa-eye-slash"); // Change icon to eye-slash
    } else {
        element.classList.replace("visible-text", "hidden-text");
        icon.classList.replace("fa-eye-slash", "fa-eye"); // Change icon back to eye
    }
}

$(document).ready(function () {
    let recipientName = sessionStorage.getItem("recipientName");
    let recipientAccount = sessionStorage.getItem("recipientAccount");

    if (recipientAccount) {
        $("#searchRecipient").val(recipientAccount);
        $("#account-name").text(recipientName);
        $("#account-name").addClass("text-success");
        $("#amountSection").removeClass("hidden"); // Show amount input
    }
});

</script>
  </body>
</html>