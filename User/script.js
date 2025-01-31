const swiper = new Swiper('.swiper', {
    slidesPerView: 3,
    spaceBetween: 20,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

// User Data (Array of Objects)
const users = [
    { fname: "Yusuf", sname: "Yakubu", accountNumber: "9122190440" },
    { fname: "Abubakar", sname: "Yakubu", accountNumber: "9122190441" },
    { fname: "Abdullahi", sname: "Yakubu", accountNumber: "9122190442" },
    { fname: "Dauda", sname: "Yakubu", accountNumber: "9122190443" },
  ];

  // Load User Data
  document.addEventListener("DOMContentLoaded", () => {
    const userList = document.getElementById("user-list");

    users.forEach((user, index) => {
      // Create User Card
      const userCard = document.createElement("div");
      userCard.className = "user-card"; // Use your custom CSS class
      userCard.setAttribute("data-index", index);

      // Badge
      const badge = document.createElement("div");
      badge.className = "badges"; // Use your custom badge style
      badge.textContent = `${user.fname.charAt(0)}${user.sname.charAt(0)}`;

      // User Info
      const userInfo = document.createElement("div");
      userInfo.className = "user-info"; // Use your custom user-info style
      userInfo.innerHTML = `
        <p class="name">${user.fname} ${user.sname}</p>
        <p class="account">${user.accountNumber}</p>
      `;

      // Action Menu
      const actionMenu = document.createElement("div");
      actionMenu.className = "action-menu"; // Use your custom action-menu style
      actionMenu.innerHTML = `
        <button class="btn edit" onclick="handleAction('Edit', ${index})">Edit</button>
        <button class="btn remove" onclick="handleAction('Remove', ${index})">Remove</button>
        <button class="btn transfer" onclick="handleAction('Transfer', ${index})">Transfer</button>
      `;
      actionMenu.style.display = "none";

      // Toggle Dropdown
      const toggleMenu = () => {
        actionMenu.style.display =
          actionMenu.style.display === "none" ? "block" : "none";
      };

      // Add Click Listeners
      badge.addEventListener("click", toggleMenu);
      userInfo.addEventListener("click", toggleMenu);

      // Assemble User Card
      userCard.appendChild(badge);
      userCard.appendChild(userInfo);
      userCard.appendChild(actionMenu);

      // Append to User List
      userList.appendChild(userCard);
    });
  });

  // Action Handlers
  function handleAction(action, userIndex) {
    const user = users[userIndex];
    alert(`${action} action triggered for ${user.fname} ${user.sname} (Account: ${user.accountNumber})`);
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


