document.addEventListener("DOMContentLoaded", function () {
    loadBanks();
    loadUserData();

    const addBeneficiaryForm = document.querySelector("#addBeneficiaryForm");
    const addBeneficiarySubmit = document.querySelector("#addBeneficiarySubmit");

    if (!addBeneficiarySubmit) {
        console.error("Element with ID 'addBeneficiarySubmit' not found.");
        return;
    }

    addBeneficiaryForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const accountNumber = document.querySelector("#accountNumber").value.trim();
        const accountName = document.querySelector("#accountName").textContent.trim();
        const nickname = document.querySelector("#nickname").value.trim();
        const bankSelect = document.getElementById("bankSelect");
        const bankCode = bankSelect.value.trim();
        const bank = bankSelect.options[bankSelect.selectedIndex]?.textContent.trim();

        if (!accountNumber || !bank || !accountName || !nickname) {
            Swal.fire({
                title: 'Warning!',
                text: "Please enter all required fields.",
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            document.getElementById("accountName").textContent = "";
            return;
        }

        // Check if editing
        const editIndex = this.dataset.editIndex;
        const action = editIndex ? "edit" : "add";

        const payload = {
            action,
            accountNumber,
            accountName,
            nickname,
            bank,
            bankCode,
        };

        // If editing, include the beneficiary ID
        if (editIndex) {
            const beneficiaryID = await getBeneficiaryIDByIndex(editIndex);
            if (beneficiaryID) {
                payload.id = beneficiaryID;
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: "Failed to fetch beneficiary ID.",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }

        try {
            // Disable the submit button to prevent multiple submissions
            addBeneficiarySubmit.disabled = true;
            document.getElementById("accountName").textContent = "";
            const response = await fetch("./beneficiary.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (data.success) {
                loadUserData();
                Swal.fire({
                    title: 'Success!',
                    text: editIndex ? "Beneficiary updated successfully!" : "Beneficiary added successfully!",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                // Reset form and clear edit mode
                addBeneficiaryForm.reset();
                document.getElementById("accountName").textContent = "";
                delete addBeneficiaryForm.dataset.editIndex;

                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById("addBeneficiaryModal")).hide();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || "Failed to process beneficiary.",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });

            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                title: 'Error!',
                text: "An error occurred. Please try again.",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } finally {
            // Re-enable the submit button
            addBeneficiarySubmit.disabled = false;
        }
    });

    // Fetch Account Name when Account Number is entered
    document.getElementById("accountNumber").addEventListener("input", fetchAccountName);

    // Search Beneficiary
    document.querySelector("#Services-beneficiaries").addEventListener("input", function () {
        fetchBeneficiaries(this.value.trim());
    });
});

// ✅ Load Banks
function loadBanks() {
    fetch("fetch_banks.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const bankSelect = document.getElementById("bankSelect");
                if (bankSelect) {
                    bankSelect.innerHTML = '<option value="">Search or Select Bank</option>';
                    data.banks.forEach(bank => {
                        bankSelect.innerHTML += `<option value="${bank.code}">${bank.name}</option>`;
                    });
                }
            }
        })
        .catch(error => console.error("Error fetching banks:", error));
}

// ✅ Fetch Account Name
async function fetchAccountName() {
    const accountNumber = document.getElementById("accountNumber").value.trim();
    const bankCode = document.getElementById("bankSelect").value.trim();
    const accountNameElement = document.getElementById("accountName");
    const loadingSpinner = document.getElementById("loadingSpinner");

    // Validate inputs
    if (accountNumber.length !== 10 || !bankCode) {
        accountNameElement.textContent = "";
        return;
    }

    // Show loading spinner
    loadingSpinner.classList.remove("d-none");

    try {
        // Try the primary method
        const primaryResponse = await fetch("./fetch_account_name_bank.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ accountNumber, bankCode }),
        });

        const primaryData = await primaryResponse.json();

        if (primaryData.success) {
            // If primary fetch succeeds, update the account name
            accountNameElement.textContent = primaryData.accountName;
        } else {
            // If primary fetch fails, try the backup method
            const backupResponse = await fetch("./check_account_db.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ accountNumber, bankCode }),
            });

            const backupData = await backupResponse.json();

            if (backupData.success) {
                // If backup fetch succeeds, update the account name
                accountNameElement.textContent = backupData.account_name; // Use the correct key
            } else {
                // If both fetches fail, show an error message
                accountNameElement.textContent = "Account not found";
            }
        }
    } catch (error) {
        // Handle any network or unexpected errors
        console.error("Error fetching account name:", error);
        accountNameElement.textContent = "Error fetching account name";
    } finally {
        // Hide loading spinner
        loadingSpinner.classList.add("d-none");
    }
}
// ✅ Load User Data
function loadUserData() {
    fetch("./beneficiary.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch" }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                displayBeneficiaries(data.data);
            } else {
                console.error("Error:", data.message);
            }
        })
        .catch(error => console.error("Error fetching users:", error));
}

// ✅ Fetch Beneficiaries on Search
function fetchBeneficiaries(searchTerm) {
    fetch("./beneficiary.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "search", searchTerm }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                displayBeneficiaries(data.data);
            } else {
                console.error("Error:", data.message);
            }
        })
        .catch(error => console.error("Error fetching beneficiaries:", error));
}

// ✅ Display Beneficiaries
function displayBeneficiaries(users) {
    const userList = document.getElementById("user-list");
    if (!userList) {
        console.error("Element with ID 'user-list' not found.");
        return;
    }

    userList.innerHTML = "";

    users.forEach((user, index) => {
        const userCard = document.createElement("div");
        userCard.className = "user-card";
        userCard.dataset.index = index;

        const initials = user.accountName
            ? user.accountName.split(" ").map(name => name.charAt(0).toUpperCase()).join("").slice(0, 2)
            : "?";

        userCard.innerHTML = `
            <div class="badges">${initials}</div>
            <div class="user-info">
                <p class="account-name">${user.accountName || "N/A"}</p>
                <p class="account">${user.bank || "N/A"} - ${user.account_number || "N/A"}</p>
            </div>
            <div class="action-menu" style="display: none;">
                <button class="btn edit" onclick="handleAction('Edit', ${index})">Edit</button>
                <button class="btn remove" onclick="handleAction('Remove', ${index})">Remove</button>
                <button class="btn transfer" onclick="handleAction('Transfer', ${index})">Transfer</button>
            </div>
        `;

        userCard.querySelector(".badges").addEventListener("click", toggleMenu);
        userCard.querySelector(".user-info").addEventListener("click", toggleMenu);
        userList.appendChild(userCard);

        function toggleMenu(event) {
            document.querySelectorAll(".action-menu").forEach(menu => {
                if (menu !== event.target.nextElementSibling) {
                    menu.style.display = "none";
                }
            });
            const menu = userCard.querySelector(".action-menu");
            menu.style.display = menu.style.display === "none" ? "block" : "none";
        }
    });
}

// ✅ Handle Actions
function handleAction(action, index) {
    if (action === "Edit") {
        editBeneficiary(index);
    } else if (action === "Remove") {
        removeBeneficiary(index);
    } else if (action === "Transfer") {
        Swal.fire({
            title: 'Info',
            text: `Transfer functionality to be implemented for beneficiary at index ${index}`,
            icon: 'info',
            confirmButtonText: 'OK'
        });

    }
}

// ✅ Edit Beneficiary
function editBeneficiary(index) {
    fetch("./beneficiary.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch" }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.data) && data.data[index]) {
            const user = data.data[index];

            // Populate modal with existing data
            document.querySelector("#accountNumber").value = user.account_number;
            document.querySelector("#nickname").value = user.nickname;

            // Set the bank select
            const bankSelect = document.getElementById("bankSelect");
            if (bankSelect) {
                bankSelect.value = user.bankCode;
            }

            // Update account name text
            document.querySelector("#accountName").textContent = user.accountName;

            // Change modal title and button text
            document.querySelector("#addBeneficiaryModalLabel").textContent = "Edit Beneficiary";
            document.querySelector("#addBeneficiarySubmit").textContent = "Update Beneficiary";

            // Store the beneficiary index for updating
            document.querySelector("#addBeneficiaryForm").dataset.editIndex = index;

            // Show modal
            bootstrap.Modal.getInstance(document.getElementById("addBeneficiaryModal")).show();
        }
    })
    .catch(error => console.error("Error fetching beneficiary data:", error));
}

// ✅ Get Beneficiary ID by Index
async function getBeneficiaryIDByIndex(index) {
    const response = await fetch("./beneficiary.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch" }),
    });
    const data = await response.json();
    if (data.success && Array.isArray(data.data) && data.data[index]) {
        return data.data[index].id;
    }
    return null;
}

// ✅ Remove Beneficiary
function removeBeneficiary(index) {
    fetch("./beneficiary.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "fetch" }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.data) && data.data[index]) {
            const beneficiaryID = data.data[index].id;

            if (!confirm(`Are you sure you want to remove beneficiary with ID: ${beneficiaryID}?`)) return;

            fetch("./beneficiary.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "remove", id: beneficiaryID }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: "Beneficiary removed successfully!",
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadUserData();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || "Failed to remove beneficiary.",
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                }
            })
            .catch(error => console.error("Error removing beneficiary:", error));
        }
    })
    .catch(error => console.error("Error fetching beneficiary data:", error));
}