function formatName(name, type = "title") {
    if (!name) return "";

    if (type === "title") {
        // Capitalize the first letter of each word
        return name
            .toLowerCase()
            .split(" ")
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
    } else if (type === "uppercase") {
        // Convert the entire name to uppercase
        return name.toUpperCase();
    } else {
        return name; // Return as is if type is invalid
    }
}



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
        url: './asset/include/get_balance.php',
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
    const recipientAccountInput = document.getElementById("recipientAccountInput");
    const accountName = document.getElementById("account-name");
    const amountSection = document.getElementById("amountSection");
    const loadingSpinner = document.getElementById("loadingSpinner");

    amountSection.classList.add("hidden");

    recipientAccountInput.addEventListener("input", function () {
        let accountNumber = this.value.trim();
        if (accountNumber.length >= 9) {
            loadingSpinner.classList.remove("d-none");
            fetch("./asset/include/fetch_account_name.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "account_number=" + encodeURIComponent(accountNumber)
            })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add("d-none");
                if (data.success) {
                    accountName.textContent = formatName(data.account_name, "title");
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
            fetch("./asset/include/check_balance.php", {
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
    const recipientAccountInput = document.getElementById("recipientAccountInput").value.trim();
    const accountName = document.getElementById("account-name").textContent.trim();
    const amountCharges = parseFloat(document.getElementById("amount-charges").textContent.replace("Charges: ₦", "").trim()) || 0;
    const amountInput = parseFloat(document.getElementById("amountInput").value.trim());
    const note = document.getElementById("note").value.trim();

    if (!recipientAccountInput || !accountName || accountName.includes("❌")) {
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
        confirmRecipientElement.textContent = formatName(accountName, "title");
        confirmAccountElement.textContent = recipientAccountInput;
        chargesFees.textContent = `₦${amountCharges.toFixed(2)}`;
    } else {
        console.error("Confirmation elements missing.");
    }
}
                    accountName.textContent = 


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
    for (let i = 0; i < pinInputs.length; i++) {
        pinInputs[i].addEventListener("input", function () {
            if (this.value.length === 1 && i < pinInputs.length - 1) {
                pinInputs[i + 1].focus();
            }
            checkPinCompletion();
        });

        // Move to previous field when pressing backspace
        pinInputs[i].addEventListener("keydown", function (e) {
            if (e.key === "Backspace" && this.value === "" && i > 0) {
                pinInputs[i - 1].focus();
            }
        });
    }

    // Enable Confirm button when all fields are filled
    function checkPinCompletion() {
        let pinValue = "";
        for (let i = 0; i < pinInputs.length; i++) {
            pinValue += pinInputs[i].value;
        }
        confirmBtn.disabled = pinValue.length !== 4; // Enable only when 4 digits entered
    }

    // Confirm PIN Button Click
    confirmBtn.addEventListener("click", function () {
        let pinValue = "";
        for (let i = 0; i < pinInputs.length; i++) {
            pinValue += pinInputs[i].value;
        }

        if (pinValue.length !== 4 || isNaN(pinValue)) {
            if (pinError) pinError.classList.remove("d-none");
            if (pinContainer) pinContainer.classList.add("shake");

            for (let i = 0; i < pinInputs.length; i++) {
                pinInputs[i].classList.add("error");
                pinInputs[i].value = "";
            }

            setTimeout(() => {
                if (pinContainer) pinContainer.classList.remove("shake");
                pinInputs[0].focus();
            }, 500);
        } else {
            if (pinError) pinError.classList.add("d-none");
            submitTransaction(pinValue);
        }
    });

    function submitTransaction(pin) {
        // Collect form data
        let recipientInput = document.getElementById("recipientAccountInput");
        let amountInput = document.getElementById("amountInput");
        let noteInput = document.getElementById("note");
        let chargesElement = document.getElementById("amount-charges");

        let formData = {
            recipient_account: recipientInput ? recipientInput.value.trim() : "",
            amount: amountInput ? amountInput.value.trim() : "",
            note: noteInput ? noteInput.value.trim() : "",
            pin: pin,
            charges: chargesElement ? parseFloat(chargesElement.textContent.replace("Charges: ₦", "").trim()) : 0
        };

        // Debug: Log the formData object to the console
        console.log("Form Data Being Sent:", formData);

        // Validate form data
        if (!formData.recipient_account || !formData.amount || isNaN(formData.charges)) {
            showAlert("Invalid Data", "Please check your input data.", "error");
            return;
        }

        // Show processing screen
        toggleProcessingScreen(true);

        // Hide PIN modal
    hideModal("pinModal"); // Hide the PIN modal

fetch("./asset/include/transferLogic.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(formData)
})
.then(response => response.json())
.then(data => {
    const process = document.getElementById("processingScreen");
    process.classList.add("hidden");
    if (data.success) {
        // Create the receiptData object step by step
        const receiptData = {};

        // Assign values individually
        receiptData.transaction_id = data.transaction_id;
        receiptData.total_deducted = data.total_deducted;
        receiptData.recipient_account = formData.recipient_account;

        // Get recipient name from the DOM
        const confirmRecipientElement = document.getElementById("confirmRecipient");
        receiptData.recipient_name = confirmRecipientElement ? confirmRecipientElement.textContent : "";

        // Format the amount to 2 decimal places
        receiptData.amount = parseFloat(formData.amount).toFixed(2);

        // Assign status and date
        receiptData.status = data.status;
        receiptData.date = new Date().toLocaleString();

        // Assign note from formData
        receiptData.note = formData.note;

        // Get sender account and name from the DOM
        const receiptSenderAccountElement = document.getElementById("receiptSenderAccount");
        const receiptSenderNameElement = document.getElementById("receiptSenderName");

        receiptData.sender_account = receiptSenderAccountElement ? receiptSenderAccountElement.textContent : "";
        receiptData.sender_name = receiptSenderNameElement ? receiptSenderNameElement.textContent : "";

        // Store receipt data in localStorage
        localStorage.setItem("receiptData", JSON.stringify(receiptData));

        // Display the receipt (assuming you have a function for this)
        showReceipt(receiptData);
    } else {
        // Show error message using SweetAlert2
        Swal.fire({
            title: "Transaction Failed",
            text: data.message || "An unknown error occurred.",
            icon: "error",
            confirmButtonText: "Try Again"
        }).then(() => {
            // Reopen the PIN modal
            const modalInstance = bootstrap.Modal.getOrCreateInstance(pinModalEl, { backdrop: 'static' });
            modalInstance.show();
        });
    }
})
.catch(error => {
    // Handle any errors
    console.error("Error:", error);
    Swal.fire("Error", "An error occurred. Please try again.", "error");
});

    }

    function prepareReceiptData(data, formData) {
        try {
            return {
                transaction_id: data && data.transaction_id ? data.transaction_id : "N/A",
                total_deducted: data && data.total_deducted ? data.total_deducted : 0.00,
                recipient_account: formData && formData.recipient_account ? formData.recipient_account : "N/A",
                recipient_name: getTextContent("confirmRecipient"),
                amount: formData && formData.amount ? parseFloat(formData.amount).toFixed(2) : "0.00",
                status: data && data.status ? data.status : "Pending",
                date: new Date().toLocaleString(),
                note: formData && formData.note ? formData.note : "",
                sender_account: getTextContent("receiptSenderAccount"),
                sender_name: getTextContent("receiptSenderName")
            };
        } catch (error) {
            console.error("Error preparing receipt data:", error);
            return {};
        }
    }

    function toggleProcessingScreen(show) {
        let processingScreen = document.getElementById("processingScreen");
        if (processingScreen) {
            if (show) {
                processingScreen.classList.remove("hidden");
            } else {
                processingScreen.classList.add("hidden");
            }
        }
    }

    function hideModal(modalId) {
        let modalEl = document.getElementById(modalId);
        if (modalEl) {
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        }
    }

    function showModal(modalId) {
        let modalEl = document.getElementById(modalId);
        if (modalEl) {
            let modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static' });
            if (modal) {
                modal.show();
            }
        }
    }

    function showAlert(title, text, icon, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: "OK"
        }).then(function () {
            if (callback && typeof callback === "function") {
                callback();
            }
        });
    }

    function getTextContent(elementId, fallback) {
        let element = document.getElementById(elementId);
        return element ? element.textContent : (fallback || "N/A");
    }

    function showReceipt(data) {
        document.getElementById("receiptTransactionId").textContent = data.transaction_id;
        document.getElementById("receipttotalDeduction").textContent = "₦" + data.total_deducted;
        document.getElementById("receiptAccount").textContent = data.recipient_account;
        document.getElementById("receiptRecipient").textContent = data.recipient_name;
        document.getElementById("receiptAmount").textContent = "₦" + data.amount;
        document.getElementById("receiptFee").textContent = data.status;
        document.getElementById("receiptDate").textContent = data.date;
        document.getElementById("receiptNote").textContent = data.note;

        document.getElementById("transferForm").classList.add("hidden");
        document.getElementById("confirmationScreen").classList.add("hidden");
        document.getElementById("processingScreen").classList.add("hidden");
        document.getElementById("receiptScreen").classList.remove("hidden");

        window.removeEventListener("beforeunload", beforeUnloadHandler);

        history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            resetTransactionData();
            window.location.href = "./index.php";
        };
    }

    function resetTransactionData() {
        localStorage.removeItem("receiptData");
    }

    window.addEventListener("beforeunload", resetTransactionData);
})();
