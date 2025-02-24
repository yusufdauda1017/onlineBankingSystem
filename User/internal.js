


// Define a beforeunload handler (will be removed on receipt display)
function beforeUnloadHandler(e) {
    var inTransferProcess =
        !document.getElementById("transferForm").classList.contains("hidden") ||
        !document.getElementById("confirmationScreen").classList.contains("hidden") ||
        !document.getElementById("processingScreen").classList.contains("hidden");

    if (inTransferProcess) {
        e.preventDefault();
        e.returnValue = ''; // Required for Chrome support
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // Attach the beforeunload handler
    window.addEventListener("beforeunload", beforeUnloadHandler);

    // Cancel transaction button handler
    document.getElementById("cancel-transaction").addEventListener("click", function () {
        Swal.fire({
            icon: "warning",
            title: "Are you sure?",
            text: "Do you want to cancel the transaction?",
            showCancelButton: true,
            confirmButtonText: "Yes, cancel it!",
            cancelButtonText: "No, keep it",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "./index.php";
            }
        });
    });


    // Load initial balance
    if (typeof loadBalance === "function") loadBalance();

    // Account fetching for recipient
    if (typeof accountFetching === "function") accountFetching();

    // Amount fetching and validation
    if (typeof amountFetching === "function") amountFetching();


 // Event listener for transfer button click
    let transferBtn = document.getElementById("btn-transfer");
    if (transferBtn) transferBtn.addEventListener("click", handleTransferClick);

// Event listener for transfer button click
let goBackBtn = document.getElementById("goBack");
if (goBackBtn) goBackBtn.addEventListener("click", goBack);

// Event listener for transfer button click
let goToProcessBtn = document.getElementById("goToProcess");
if (goToProcessBtn) goToProcessBtn.addEventListener("click", goToProcess);


    // Event listener for PIN section click
    let pinSection = document.getElementById("pinSection");
    if (pinSection) pinSection.addEventListener("click", handlePinSectionClick);

    // Event listener for confirm transaction button click
    let confirmTransaction = document.getElementById("confirmTransaction");
    if (confirmTransaction) confirmTransaction.addEventListener("click", handleConfirmTransaction);

    // Check if a receipt exists in localStorage and show it on page load
    if (localStorage.getItem("receiptData")) {
        showReceipt(JSON.parse(localStorage.getItem("receiptData")));
    }
});

// Function to display receipt and remove beforeunload event
function showReceipt(receiptData) {
    // Display receipt logic goes here
    console.log("Showing receipt:", receiptData);

    // Remove beforeunload handler since transaction is completed
    window.removeEventListener("beforeunload", beforeUnloadHandler);
}

function loadBalance() {
    $.ajax({
        url: './get_balance.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#account_balance').text(response.balance + " NGN");
            } else {
                $('#account_balance').text("Error fetching balance");
            }
        },
        error: function() {
            $('#account_balance').text("Error connecting to server");
        }
    });
}

function accountFetching() {
    const searchRecipient = document.getElementById("searchRecipient");
    const accountName = document.getElementById("account-name");
    const amountSection = document.getElementById("amountSection");
    const loadingSpinner = document.getElementById("loadingSpinner");

    amountSection.classList.add("hidden");

    searchRecipient.addEventListener("input", function () {
        let accountNumber = this.value.trim();
        if (accountNumber.length >= 5) {
            loadingSpinner.classList.remove("d-none");
            fetch("./fetch_account_name.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "account_number=" + encodeURIComponent(accountNumber)
            })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add("d-none");
                if (data.success) {
                    accountName.textContent = data.account_name;
                    accountName.classList.add("text-success");
                    accountName.classList.remove("text-danger");
                    amountSection.classList.remove("hidden");
                } else {
                    accountName.textContent = "❌ Account Not Found";
                    accountName.classList.add("text-danger");
                    accountName.classList.remove("text-success");
                    amountSection.classList.add("hidden");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                loadingSpinner.classList.add("d-none");
                accountName.textContent = "❌ Error fetching account";
                accountName.classList.add("text-danger");
                amountSection.classList.add("hidden");
            });
        } else {
            accountName.textContent = "";
            amountSection.classList.add("hidden");
        }
    });
}

function amountFetching() {
    const amountInput = document.getElementById("amountInput");
    const amountCharges = document.getElementById("amount-charges");
    const amountWarning = document.getElementById("amount-warning");
    const btnTransfer = document.getElementById("btn-transfer");
    const loadingSpinner = document.getElementById("loadingSpinner2");

    if (!amountInput || !btnTransfer || !loadingSpinner || !amountCharges) {
        console.error("Required elements not found in the DOM.");
        return;
    }

    amountInput.addEventListener("input", function () {
        const amount = parseFloat(amountInput.value);
        if (!isNaN(amount) && amount >= 100) {
            amountCharges.innerText = "Charges: ₦0.00";
            amountWarning.innerText = "";
            loadingSpinner.classList.remove("d-none");
            fetch("./check_balance.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "amount=" + encodeURIComponent(amount)
            })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add("d-none");
                if (data.success) {
                    amountWarning.innerText = "";
                    btnTransfer.classList.remove("disabled");
                    btnTransfer.disabled = false;
                    let charge = (amount * 0.01).toFixed(2);
                    amountCharges.innerText = `Charges: ₦${charge}`;
                } else {
                    amountWarning.innerText = "⚠️ " + data.message;
                    amountWarning.classList.add("text-danger");
                    btnTransfer.classList.add("disabled");
                    btnTransfer.disabled = true;
                    amountCharges.innerText = "Charges: ₦0.00";
                }
            })
            .catch(error => {
                console.error("Error:", error);
                amountWarning.innerText = "❌ Error fetching balance";
                amountWarning.classList.add("text-danger");
                loadingSpinner.classList.add("d-none");
                btnTransfer.classList.add("disabled");
                btnTransfer.disabled = true;
                amountCharges.innerText = "Charges: ₦0.00";
            });
        } else {
            amountWarning.innerText = "❌ Invalid amount, should be at least 100";
            btnTransfer.classList.add("disabled");
            btnTransfer.disabled = true;
            amountCharges.innerText = "Charges: ₦0.00";
        }
    });
}

function handleTransferClick() {
    const searchRecipient = document.getElementById("searchRecipient").value.trim();
    const accountName = document.getElementById("account-name").textContent.trim();
    const amountCharges = parseFloat(document.getElementById("amount-charges").textContent.replace("Charges: ₦", "").trim()) || 0;
    const amountInput = parseFloat(document.getElementById("amountInput").value.trim());
    const note = document.getElementById("note").value.trim();

    if (!searchRecipient || !accountName || accountName.includes("❌")) {
        Swal.fire("Error", "Please enter a valid recipient.", "error");
        return;
    }

    if (isNaN(amountInput) || amountInput < 100 || amountInput > 1000000) {
        Swal.fire("Error", "Please enter a valid amount within ₦100 - ₦1,000,000.", "error");
        return;
    }

    document.getElementById("transferForm").classList.add("hidden");
    document.getElementById("confirmationScreen").classList.remove("hidden");

    const confirmAmountElement = document.getElementById("confirmAmount");
    const confirmRecipientElement = document.getElementById("confirmRecipient");
    const confirmAccountElement = document.getElementById("confirmAccount");
    const chargesFees = document.getElementById("confirmFee");

    if (confirmAmountElement && confirmRecipientElement && confirmAccountElement && chargesFees) {
        confirmAmountElement.textContent = `₦${amountInput.toFixed(2)}`;
        confirmRecipientElement.textContent = accountName;
        confirmAccountElement.textContent = searchRecipient;
        chargesFees.textContent = `₦${amountCharges.toFixed(2)}`;
    } else {
        console.error("Confirmation elements missing.");
    }
}


// Cancel transaction button handler
function goBack() {
    Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        text: "Do you want to Go Back?",
        showCancelButton: true,
        confirmButtonText: "Yes, cancel it!",
        cancelButtonText: "No, keep it",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("transferForm").classList.remove("hidden");
            document.getElementById("confirmationScreen").classList.add("hidden");
        }
    });
}


// Cancel transaction button handler
function goToProcess() {
    Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        text: "Do you want to go back?",
        showCancelButton: true,
        confirmButtonText: "Yes, cancel it!",
        cancelButtonText: "No, keep it",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("confirmationScreen").classList.add("hidden");

            // Hide Bootstrap modal safely
            const pinModalElement = document.getElementById("pinModal");
            if (pinModalElement) {
                const pinModal = bootstrap.Modal.getInstance(pinModalElement);
                if (pinModal) {
                    pinModal.hide();
                }
            }

            document.getElementById("confirmationScreen").classList.remove("hidden");
        }
    });
}

function handlePinSectionClick() {
    document.getElementById("confirmationScreen").classList.add("hidden");
    const pinModal = new bootstrap.Modal(document.getElementById("pinModal"));
    pinModal.show();
}
(function handleConfirmTransaction() {
    const pinInputs = document.querySelectorAll(".pin-box");
    const confirmBtn = document.getElementById("confirmTransaction");
    const pinError = document.getElementById("pinError");
    const pinContainer = document.getElementById("pinContainer");

    // Auto-focus next field when typing
    pinInputs.forEach((input, index) => {
        input.addEventListener("input", function () {
            if (this.value.length === 1 && index < pinInputs.length - 1) {
                pinInputs[index + 1].focus();
            }
            checkPinCompletion();
        });

        // Move to previous field when pressing backspace
        input.addEventListener("keydown", function (e) {
            if (e.key === "Backspace" && this.value === "" && index > 0) {
                pinInputs[index - 1].focus();
            }
        });
    });

    // Enable Confirm button when all fields are filled
    function checkPinCompletion() {
        const pinValue = Array.from(pinInputs).map(input => input.value).join("");
        confirmBtn.disabled = pinValue.length !== 4; // Enable only when 4 digits entered
    }

    // Confirm PIN Button Click
    confirmBtn.addEventListener("click", function () {
        const pinValue = Array.from(pinInputs).map(input => input.value).join("");

        if (pinValue.length !== 4 || isNaN(pinValue)) {
            pinError.classList.remove("d-none");
            pinContainer.classList.add("shake");

            pinInputs.forEach(input => {
                input.classList.add("error");
                input.value = "";
            });

            setTimeout(() => {
                pinContainer.classList.remove("shake");
                pinInputs[0].focus();
            }, 500);
        } else {
            pinError.classList.add("d-none");
            submitTransaction(pinValue);
        }
    });

    function submitTransaction(pin) {
        const formData = {
            recipient_account: document.getElementById("searchRecipient")?.value.trim(),
            amount: document.getElementById("amountInput")?.value.trim(),
            note: document.getElementById("note")?.value.trim(),
            pin: pin,
            charges: parseFloat(document.getElementById("amount-charges")?.textContent.replace("Charges: ₦", "").trim()) || 0
        };

        if (!formData.recipient_account || !formData.amount || isNaN(formData.charges)) {
            Swal.fire({
                title: "Invalid Data",
                text: "Please check your input data.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return;
        }

        document.getElementById("processingScreen")?.classList.remove("hidden");
        const pinModalEl = document.getElementById("pinModal");
        const pinModal = bootstrap.Modal.getInstance(pinModalEl);
        pinModal.hide();

        fetch("./transferLogic.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("processingScreen")?.classList.add("hidden");
            if (data.success) {
                const receiptData = {
                    transaction_id: data.transaction_id,
                    total_deducted: data.total_deducted,
                    recipient_account: formData.recipient_account,
                    recipient_name: document.getElementById("confirmRecipient")?.textContent,
                    amount: parseFloat(formData.amount).toFixed(2),
                    status: data.status,
                    date: new Date().toLocaleString(),
                    note: formData.note,
                    sender_account: document.getElementById("receiptSenderAccount")?.textContent,
                    sender_name: document.getElementById("receiptSenderName")?.textContent
                };
                localStorage.setItem("receiptData", JSON.stringify(receiptData));
                showReceipt(receiptData);
            } else {
                Swal.fire({
                    title: "Transaction Failed",
                    text: data.message || "An unknown error occurred.",
                    icon: "error",
                    confirmButtonText: "Try Again"
                }).then(() => {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(pinModalEl, { backdrop: 'static' });
                    modalInstance.show();
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "An error occurred. Please try again.", "error");
        });
    }
})();





function showReceipt(data) {
    document.getElementById("receiptTransactionId").textContent = data.transaction_id;
    document.getElementById("receipttotalDeduction").textContent = "₦" + data.total_deducted;
    document.getElementById("receiptAccount").textContent = data.recipient_account;
    document.getElementById("receiptRecipient").textContent = data.recipient_name;
    document.getElementById("receiptAmount").textContent = "₦" + data.amount;
    document.getElementById("receiptFee").textContent =  data.status;
    document.getElementById("receiptDate").textContent = data.date;
    document.getElementById("receiptNote").textContent = data.note;

    // Hide all other screens, show only the receipt
    document.getElementById("transferForm").classList.add("hidden");
    document.getElementById("confirmationScreen").classList.add("hidden");
    document.getElementById("processingScreen").classList.add("hidden");
    document.getElementById("receiptScreen").classList.remove("hidden");

    // Remove beforeunload warning
    window.removeEventListener('beforeunload', beforeUnloadHandler);

    // Override back button: Redirect to dashboard and reset transaction data
    history.pushState(null, null, window.location.href);
    window.onpopstate = function () {
        resetTransactionData(); // Clear receipt data
        window.location.href = "./index.php"; // Redirect to user dashboard
    };
}

function resetTransactionData() {
    localStorage.removeItem("receiptData");
}

// Ensure data is cleared if user closes the tab or navigates away
window.addEventListener("beforeunload", resetTransactionData);

// A placeholder shareReceipt function
function shareReceipt() {
    window.location.href = "./index.php";
}