
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar'); // For unique ID
    const content = document.querySelectorAll('.content'); // For multiple elements with a shared class
    const greeting = document.querySelectorAll('.greating'); // For multiple elements with a shared class

    sidebar.classList.toggle('active');

    // Loop through all content elements and toggle the class
    content.forEach(element => {
        element.classList.toggle('expanded');
    });

    // Loop through all greeting elements and toggle the class
    greeting.forEach(element => {
        element.classList.toggle('expanded');
    });
}

const sections = document.querySelectorAll('.section');
const sideBarLinks = document.querySelectorAll('#sidebar ul li a');

function handleRouting() {
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

// Listen for hash changes
window.addEventListener('hashchange', handleRouting);

// Initialize routing on page load
handleRouting();

// Sample Data for Charts
const transactionsChartCtx = document.getElementById('transactionsChart').getContext('2d');
new Chart(transactionsChartCtx, {
type: 'line',
data: {
labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
datasets: [{
label: 'Transactions (₦)',
data: [10000, 15000, 20000, 25000, 30000, 35000],
borderColor: 'blue',
borderWidth: 2,
fill: false,
tension: 0.4,
}]
},
options: {
plugins: {
legend: {
  display: true,
  position: 'top',
},
tooltip: {
  enabled: true,
  mode: 'index',
  intersect: false,
}
},
responsive: true,
}
});


const userGrowthChartCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthChartCtx, {
type: 'bar',
data: {
labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
datasets: [{
  label: 'New Users',
  data: [50, 75, 100, 125, 150, 200],
  backgroundColor: 'green'
}]
}
});