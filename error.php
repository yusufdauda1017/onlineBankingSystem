<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="icon" href="./asset/img/logo/logo.svg" type="image/svg">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="style.css">
    <style>

    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;500&display=swap');

:root {
    --primary-font: 'Montserrat', sans-serif;  /* Corporate & professional */
    --secondary-font: 'Roboto', sans-serif; /* Clean and readable */
    --primary-color: #559403;
    --primary-hover-color: #005313;
    --background-color: #001f10;
    --text-black: #333;
    --text-white: #f5f5f5;
}

/* Reset default styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    font-family: var(--secondary-font);
    background-color: var(--background-color);
    color: var(--text-white);
    text-align: center;
}

h1 {
    font-size: 6rem;
    font-family: var(--primary-font);
    font-weight: 700;
    color: var(--primary-color);
    text-shadow: 2px 2px 10px rgba(255, 255, 255, 0.2);
}

p {
    font-size: 1.2rem;
    font-weight: 300;
    max-width: 600px;
    margin: 20px 0;
    color: var(--text-white);
}

/* Company Logo */
.logo {
    max-width: 200px;
    margin-bottom: 20px;
}

/* Button Styling */
a.button {
    display: inline-block;
    padding: 12px 24px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    text-transform: uppercase;
    background: var(--primary-color);
    color: var(--text-white);
    border-radius: 5px;
    transition: background 0.3s ease-in-out;
}

a.button:hover {
    background: var(--primary-hover-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    h1 {
        font-size: 4rem;
    }

    p {
        font-size: 1rem;
    }
}


    </style>
</head>
<body>

    
    <!-- Company Logo (Replace with your logo URL) -->
    <img src="asset/img/logo/logo-1.png" alt="Company Logo" class="logo">

    <!-- 404 Error Message -->
    <h1>404</h1>
    <p>Oops! The page you’re looking for doesn’t exist or has been moved.</p>

    <!-- Go Back Button -->
    <a href="./index.php" class="button">Return Home</a>

    <script src="script.js"></script>
</body>
</html>
