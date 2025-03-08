document.addEventListener("DOMContentLoaded", function () {
    function loadActivityLog() {
        fetch("https://trustpoint.wuaze.com/User/asset/include/fetch_activity.php")
            .then(response => response.json())
            .then(data => {
                let logTable = document.querySelector(".activity-log-table tbody");
                logTable.innerHTML = ""; // Clear previous logs

                if (data.error) {
                    logTable.innerHTML = `<tr><td colspan="4" class="text-center">${data.error}</td></tr>`;
                    return;
                }

                if (data.length > 0) {
                    data.forEach(log => {
                        let row = `<tr>
                            <td>${new Date(log.timestamp).toLocaleDateString()}</td>
                            <td>${new Date(log.timestamp).toLocaleTimeString()}</td>
                            <td>${log.action}</td>
                            <td>${log.device_name}</td> <!-- Add Device Name Column -->

                        </tr>`;
                        logTable.innerHTML += row;
                    });
                } else {
                    logTable.innerHTML = `<tr><td colspan="4" class="text-center">No activity found</td></tr>`;
                }
            })
            .catch(error => console.error("Error fetching logs:", error));
    }

    loadActivityLog(); // Load logs on page load
});
