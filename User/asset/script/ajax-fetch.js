
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


function loadBalance() {
    $.ajax({
        url: './asset/include/get_balance.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#account_balance').text(response.balance + " NGN");
            } else {
                $('#account_balance').text("Error fetching balance");
            }
        },
        error: function () {
            $('#account_balance').text("Error connecting to server");
        }
    });
}

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

    // Validate passwords
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

    // Call function and pass values
    changePassword(oldPassword, newPassword);

    function resetButton() {
        submitButton.prop("disabled", false);
        btnText.text("Save Changes");
        loader.addClass("d-none");
    }
});

function changePassword(oldPassword, newPassword) {
    $.ajax({
        type: "POST",
        url: "./asset/include/change-password.php",
        data: { oldPassword: oldPassword, newPassword: newPassword },
        dataType: "json",
        success: function (response) {
            Swal.fire(response.status === "success" ? "Success" : "Error", response.message, response.status);
            if (response.status === "success") {
                $("#password-change")[0].reset();
            }
        },
        error: function () {
            Swal.fire("Error", "An error occurred. Please try again.", "error");
        },
        complete: function () {
            $(".btn-save").prop("disabled", false);
            $(".btn-text").text("Save Changes");
            $(".spinner-border").addClass("d-none");
        }
    });
}

function process_pin(pin, btnText, spinner) {
    $.ajax({
        url: "./asset/include/process_pin.php",
        type: "POST",
        data: { action: "create_pin", pin: pin },
        dataType: "json",
        success: function (response) {
            Swal.fire(response.status === "success" ? "Success" : "Error", response.message, response.status);
            if (response.status === "success") {
                $("#createTransactionPinModal").modal("hide");
                $("#create-transaction-pin")[0].reset();
            }
        },
        error: function () {
            Swal.fire("Error", "Something went wrong.", "error");
        },
        complete: function () {
            // ✅ Ensure btnText and spinner are defined and revert the button state
            btnText.removeClass("d-none");
            spinner.addClass("d-none");
        }
    });
}

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

    // ✅ Pass btnText and spinner to process_pin() so they are available in `complete`
    process_pin(pin, btnText, spinner);
});

function change_pin(oldPin, newPin, btnText, spinner) {
    console.log("AJAX request started..."); // Debugging

    $.ajax({
        url: "./asset/include/process_pin.php",
        type: "POST",
        data: { action: "change_pin", oldPin: oldPin, newPin: newPin },
        dataType: "json",
        success: function (response) {
            console.log("AJAX response received:", response); // Debugging

            Swal.fire(response.status === "success" ? "Success" : "Error", response.message, response.status);
            if (response.status === "success") {
                $("#changePinModal").modal("hide");
                $("#change-pin")[0].reset();
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            Swal.fire("Error", "Something went wrong. Please try again.", "error");
        },
        complete: function () {
            // Check if the elements exist before calling removeClass/addClass
            if (btnText && btnText.length) {
                btnText.removeClass("d-none");
            }
            if (spinner && spinner.length) {
                spinner.addClass("d-none");
            }
        }
    });
}

$("#change-pin").submit(function (e) {
    e.preventDefault();

    var oldPin = $("#oldPin").val().trim();
    var newPin = $("#newPin").val().trim();
    var confirmNewPin = $("#confirmNewPin").val().trim();

    // Check if PINs are provided
    if (!oldPin) {
        Swal.fire("Error", "Please enter your old PIN.", "error");
        return;
    }
    if (newPin.length !== 4 || confirmNewPin.length !== 4 || isNaN(newPin) || isNaN(confirmNewPin)) {
        Swal.fire("Error", "PIN must be exactly 4 digits.", "error");
        return;
    }
    if (newPin !== confirmNewPin) {
        Swal.fire("Error", "New PINs do not match.", "error");
        return;
    }

    var submitBtn = $(this).find("button[type=submit]");
    var spinner = submitBtn.find(".spinner-border");
    var btnText = submitBtn.find(".btn-text");

    // Disable button to prevent multiple clicks
    submitBtn.prop("disabled", true);

    if (submitBtn.length && spinner.length && btnText.length) {
        btnText.addClass("d-none");
        spinner.removeClass("d-none");
    }

    // Call function with required parameters
    change_pin(oldPin, newPin, btnText, spinner);

    // Re-enable button after a short delay
    setTimeout(function () {
        submitBtn.prop("disabled", false);
    }, 3000);
});
