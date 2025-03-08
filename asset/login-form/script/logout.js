document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("logoutBtn").addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default link behavior

        fetch('../../includes/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Logged Out',
                    text: 'You have been logged out successfully!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '../../login.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Logout Failed',
                    text: data.message || 'Something went wrong. Please try again.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while logging out. Please try again.'
            });
        });
    });
});
