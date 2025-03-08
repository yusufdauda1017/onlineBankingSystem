
// Action Handlers
function handleAction(action, userIndex) {
    const user = users[userIndex];
    alert(`${action} action triggered for ${user.fname} ${user.sname} (Account: ${user.accountNumber})`);
  }

  // Function to handle routing
  function handleRouting() {
    const sections = document.querySelectorAll('.section');
    const sideBarLinks = document.querySelectorAll('#sidebar ul li a');
    const hash = window.location.hash || '#dashboard';

    sections.forEach((section) => {
      if ('#' + section.id === hash) {
        section.classList.add('active');
        section.classList.remove('hidden');
      } else {
        section.classList.remove('active');
        setTimeout(() => section.classList.add('hidden'), 500);
      }
    });

    sideBarLinks.forEach((link) => {
      if (link.getAttribute('href') === hash) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  // Initialize all functions after DOM loads
  document.addEventListener('DOMContentLoaded', function () {
   
    loadUserData();
    handleRouting();
  });

  // Listen for hash changes
  window.addEventListener('hashchange', handleRouting);



