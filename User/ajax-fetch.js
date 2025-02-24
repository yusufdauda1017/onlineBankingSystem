function fetchTransactions(page = 1, viewAll = false) {
    let url = `./fetch_transactions_user.php?page=${page}`;
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
        url: './get_balance.php',
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

function changePassword(){
$.ajax({
    type: "POST",
    url: "change-password.php",
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
        resetButton();
    }
});
}

function process_pin(){

    $.ajax({
        url: "process_pin.php",
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
            btnText.removeClass("d-none");
            spinner.addClass("d-none");
        }
    });
}



function change_pin(){
    $.ajax({
        url: "process_pin.php",
        type: "POST",
        data: { action: "change_pin", oldPin: oldPin, newPin: newPin },
        dataType: "json",
        success: function (response) {
            Swal.fire(response.status === "success" ? "Success" : "Error", response.message, response.status);
            if (response.status === "success") {
                $("#changePinModal").modal("hide");
                $("#change-pin")[0].reset();
            }
        },
        error: function () {
            Swal.fire("Error", "Something went wrong.", "error");
        },
        complete: function () {
            btnText.removeClass("d-none");
            spinner.addClass("d-none");
        }
    });
}