document.addEventListener("DOMContentLoaded", function () {
    const accountInput = document.getElementById("accountInput");
    const suggestionsBox = document.getElementById("suggestions");
    const loadingSpinner = document.getElementById("loadingSpinner");
    let debounceTimer;

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

    // Event listener for transfer button click
    let transferBtn = document.getElementById("btn-account");
    if (transferBtn) transferBtn.addEventListener("click", handleTransferClick);

    accountInput.addEventListener("input", function () {
        clearTimeout(debounceTimer);
        let accountNumber = this.value.trim();
        suggestionsBox.innerHTML = "";
        suggestionsBox.style.display = "none";

        debounceTimer = setTimeout(() => {
            if (accountNumber.length === 10) {
                loadingSpinner.classList.remove("d-none");
                fetch("../bank_detect.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ account_number: accountNumber }),
                })
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.classList.add("d-none");
                    if (data.status && data.data.length > 0) {
                        displaySuggestions(data);
                    } else {
                        loadBanks();
                        fetchAccountName();
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    loadingSpinner.classList.add("d-none");
                    loadBanks();
                    fetchAccountName();
                });
            }
        }, 500);
    });
});

function displaySuggestions(data) {
    const suggestionsBox = document.getElementById("suggestions");

    suggestionsBox.innerHTML = "";

    if (data.status && data.data.length > 0) {
        suggestionsBox.style.display = "block";

        data.data.forEach((acc) => {
            let logo = acc.logo ? acc.logo : "default-logo.png"; // Ensure logo fallback
            let div = document.createElement("div");
            div.classList.add("suggestion-item");
            div.innerHTML = `
                <img src="${logo}" alt="${acc.bank}" width="40">
                <div><strong>${acc.bank}</strong> <br>${acc.account_name}</div>
            `;

            div.addEventListener("click", () => {
                suggestionsBox.style.display = "none"; // Hide suggestions
                const searchRecipient = accountInput.value.trim();

                // **Directly open the confirmation screen**
                openConfirmationScreen(searchRecipient, acc.account_name, acc.bank);
            });

            suggestionsBox.appendChild(div);
        });
    }
}

function openConfirmationScreen(searchRecipient, accountName, bank) {
    // Assuming you have a function to display the confirmation screen

    document.getElementById("confirmRecipient").textContent = accountName;
    document.getElementById("confirmAccount").textContent = `${searchRecipient} (${bank})`;
    // Show the confirmation screen
    document.getElementById("transferForm").classList.add("hidden");
    document.getElementById("confirmationScreen").classList.remove("hidden");
}


// Function to fetch account name
async function fetchAccountName() {
    const accountNumberElement = document.getElementById("accountInput");
    const bankSelectElement = document.getElementById("bankSelect");
    const accountNameElement = document.getElementById("accountName");
    const loadingSpinner = document.getElementById("loadingSpinner");

    if (!accountNumberElement || !bankSelectElement || !accountNameElement || !loadingSpinner) {
        console.error("Required elements are missing in the DOM.");
        return;
    }

    const accountNumber = accountNumberElement.value.trim();
    const bankCode = bankSelectElement.value.trim();

    if (accountNumber.length !== 10 || !bankCode) {
        accountNameElement.textContent = "";
        return;
    }

    loadingSpinner.classList.remove("d-none");

    try {
        let accountName = await fetchPrimaryAccountName(accountNumber, bankCode);
        if (!accountName) {
            accountName = await fetchBackupAccountName(accountNumber, bankCode);
        }
        accountNameElement.textContent = accountName || "Account not found";
    } catch (error) {
        console.error("Error fetching account name:", error);
        accountNameElement.textContent = "Error fetching account name";
    } finally {
        loadingSpinner.classList.add("d-none");
    }
}

// Primary method to fetch account name
async function fetchPrimaryAccountName(accountNumber, bankCode) {
    const response = await fetch("./fetch_account_name_bank.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accountNumber, bankCode }),
    });

    const data = await response.json();
    return data.success ? data.accountName : null;
}

// Backup method to fetch account name
async function fetchBackupAccountName(accountNumber, bankCode) {
    const response = await fetch("./check_account_db.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accountNumber, bankCode }),
    });

    const data = await response.json();
    return data.success ? data.account_name : null;
}

// Load banks into the dropdown
async function loadBanks() {
    try {
        const response = await fetch("fetch_banks.php");
        const data = await response.json();

        if (data.success) {
            const bankSelect = document.getElementById("bankSelect");
            if (bankSelect) {
                bankSelect.innerHTML = `<option value="">Search or Select Bank</option>` +
                    data.banks.map(bank => `<option value="${bank.code}">${bank.name}</option>`).join("");
            }
        }
    } catch (error) {
        console.error("Error fetching banks:", error);
    }
}

// Function to load balance
function loadBalance() {
    $.ajax({
        url: "./get_balance.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                $("#account_balance").text(response.balance + " NGN");
            } else {
                $("#account_balance").text("Error fetching balance");
            }
        },
        error: function () {
            $("#account_balance").text("Error connecting to server");
        }
    });
}

// Function to handle transfer click
function handleTransferClick() {
    const accountInput = document.getElementById("accountInput");
    const searchRecipient = accountInput.value.trim();

    const bankSelect = document.getElementById("bankSelect");
    const selectedBank = bankSelect.options[bankSelect.selectedIndex].text;

    const accountNameElement = document.getElementById("accountName");
    const accountName = accountNameElement ? accountNameElement.textContent.trim() : "";

    if (!searchRecipient || !accountName || accountName.includes("❌")) {
        Swal.fire("Error", "Please enter a valid recipient.", "error");
        return;
    }

    if (!selectedBank || selectedBank === "Search or Select Bank") {
        Swal.fire("Error", "Please select a bank.", "error");
        return;
    }

    document.getElementById("transferForm").classList.add("hidden");
    document.getElementById("confirmationScreen").classList.remove("hidden");

    document.getElementById("confirmRecipient").textContent = accountName;
    document.getElementById("confirmAccount").textContent = `${searchRecipient} (${selectedBank})`;

    console.log({
        recipientAccount: searchRecipient,
        recipientName: accountName,
        bank: selectedBank
    });
}
