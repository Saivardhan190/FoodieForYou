<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>About Us - Foodie</title>
  <style>
    /* General Styles */
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: var(--text-primary);
    }

    :root {
      --primary-color: #7d0495;
      --secondary-color: #ff69b4;
      --background-color: #f8f9fa;
      --card-background: #ffffff;
      --text-primary: #2c3e50;
      --text-secondary: #666666;
      --success-color: #27ae60;
      --transition-speed: 0.3s;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: transparent;
      padding: 10px 20px;
      color: white;
      position: absolute;
      width: 100%;
      z-index: 10;
      flex-wrap: wrap;
    }

    .logo {
      font-family: 'Times New Roman', Times, serif;
      font-weight: 500;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 90px;
      padding-right: 60px;
      margin-right: 0px;
    }

    .nav-links a {
      font-family: 'Times New Roman', Times, serif;
      color: white;
      text-decoration: none;
      font-size: 15px;
      font-weight: 800;
      transition: all var(--transition-speed);
      position: relative;
      display: inline-block;
      padding: 5px 0;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 100%;
      height: 2px;
      background: var(--secondary-color);
      transform: scaleX(0);
      transition: transform var(--transition-speed);
      transform-origin: bottom right;
    }

    .nav-links a:hover::after {
      transform: scaleX(1);
      transform-origin: bottom left;
    }

    .cart-icon {
      position: relative;
      font-size: 20px;
      cursor: pointer;
    }

    .profile-dropdown {
      position: relative;
      text-align: center;
      padding-right: 40px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 23px;
      top: 30px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(5px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      padding: 0px;
      min-width: 190px;
      z-index: 1;
      text-align: center;
      padding-bottom: 10px;
    }

    .dropdown-content a {
      display: block;
      padding: 8px 0px;
      color: #fff;
      text-decoration: none;
      font-weight: bolder;
      text-align: center;
      width: 100%;
    }

    .dropdown-content a:hover {
      background-color: #f1f1f1;
    }

    .dropdown-content.show {
      display: block;
    }

    .welcome-section {
      text-align: center;
      padding: 200px 20px;
      color: white;
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/welcome.jpg');
      background-size: cover;
      background-position: center;
      min-height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .welcome-section h1 {
      font-size: 70px;
      margin: 0;
    }

    .welcome-section p {
      font-size: 24px;
      margin: 10px 0px 0px 220px;
    }

    .btn-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 30px;
    }

    .welcome-section .btn {
      background: transparent;
      border: 2px solid white;
      padding: 12px 30px;
      font-size: 18px;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    .welcome-section .btn:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    h2 {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 40px; /* Increased margin to accommodate the underline */
    text-align: center;
    position: relative;
    display: block; /* Changed from inline-block to block */
    width: 100%; /* Ensure the h2 takes full width */
  }

  h2::after {
    content: '';
    position: absolute;
    left: 50%; /* Center the underline */
    bottom: -15px; /* Adjust the distance from the text */
    width: 60px; /* Length of the underline */
    height: 3px; /* Thickness of the underline */
    background-color: var(--secondary-color); /* Color of the underline */
    transform: translateX(-50%); /* Center the underline */
  }

    p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: white;
      max-width: 800px;
      margin: 0 auto 20px;
      text-align: center;
    }

    /* Our Story Section */
    .story {
      background-color: var(--card-background);
      padding: 60px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .story p {
      font-size: 1.2rem;
      color: var(--text-secondary);
    }

    /* Gallery Section */
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 40px;
    }

    .gallery img {
      width: 100%;
      border-radius: 10px;
      transition: transform var(--transition-speed);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .gallery img:hover {
      transform: scale(1.05);
    }

    /* Testimonials Section */
    .testimonials {
      background-color: var(--background-color);
      padding: 60px 20px;
    }

    .testimonial {
      background: var(--card-background);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      transition: transform var(--transition-speed);
    }

    .testimonial:hover {
      transform: translateY(-5px);
    }

    .testimonial p {
      font-style: italic;
      font-size: 1rem;
      color: var(--text-secondary);
    }

    .testimonial span {
      display: block;
      margin-top: 10px;
      font-weight: bold;
      color: var(--primary-color);
    }

    /* Meet the Team Section */
    .team {
      background-color: var(--card-background);
      padding: 60px 20px;
    }

    .team p {
      font-size: 1.1rem;
      margin-bottom: 15px;
      color: var(--text-secondary);
    }

    .team strong {
      color: var(--primary-color);
    }

    /* Awards Section */
    .awards {
      background-color: var(--background-color);
      padding: 60px 20px;
    }

    .awards ul {
      list-style: none;
      padding: 0;
      max-width: 800px;
      margin: 0 auto;
    }

    .awards li {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 10px;
      padding-left: 20px;
      position: relative;
    }

    .awards li::before {
      content: "★";
      color: var(--primary-color);
      position: absolute;
      left: 0;
    }

    /* Footer */
    footer {
      background-color: #333;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }

    footer .contact-info {
      margin-bottom: 20px;
    }

    footer .contact-info p {
      font-size: 1rem;
      color: #fff;
      margin: 5px 0;
    }

    footer .social-links {
      margin: 20px 0;
    }

    footer .social-links a {
      margin: 0 10px;
      color: #d4af37;
      font-size: 1.5rem;
      text-decoration: none;
      transition: color var(--transition-speed);
    }

    footer .social-links a:hover {
      color: var(--secondary-color);
    }

    footer .newsletter {
      margin: 20px 0;
    }

    footer .newsletter input {
      padding: 10px;
      border: none;
      border-radius: 5px;
      width: 250px;
      margin-right: 10px;
    }

    footer .newsletter button {
      padding: 10px 20px;
      background: #d4af37;
      border: none;
      border-radius: 5px;
      color: #fff;
      cursor: pointer;
      transition: background var(--transition-speed);
    }

    footer .newsletter button:hover {
      background: var(--primary-color);
    }

    footer .map {
      margin-top: 20px;
    }

    footer iframe {
      width: 100%;
      height: 300px;
      border: none;
      border-radius: 10px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      h2 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }

      .gallery {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      }

      footer .newsletter input {
        width: 100%;
        margin-bottom: 10px;
      }

      footer .newsletter button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <p>Foodie</p>
    </div>
    <div class="nav-links">
      <a href="../index.php">Home</a>
      <a href="../pages/menu.php">Menu</a>
      <a href="../pages/reservation.php">Reservation</a>
      <a href="../pages/about.php">About</a>
      <a href="../pages/cart.php" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
      </a>
      <?php if (isset($_SESSION['username'])): ?>
        <div class="profile-dropdown">
          <a href="#">
            <i class="fas fa-user"></i>
          </a>
          <div class="dropdown-content">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p><hr>
            <a href="../pages/cart.php">Cart</a>
            <a href="../pages/orders.php">Orders</a>
            <a href="../pages/logout.php">Log Out</a>
          </div>
        </div>
      <?php else: ?>
        <div class="login-link">
          <a href=".../pages/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login</a>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Welcome Section -->
  <div class="welcome-section">
    <h1>About Us</h1>
    <p>At Foodie, we don’t just serve food—we serve memories. Every dish is a story, every bite an experience, and every visit a celebration of flavors, passion, and love.</p>
    <div class="btn-container">
    </div>
  </div>

  <!-- Our Story -->
  <section class="story">
    <h2>Our Story</h2>
    <p>Established in 2020, Foodie was born out of a passion for creating unforgettable dining experiences. Founded by Chef John Doe, the restaurant quickly became a cornerstone of fine dining in New York, blending tradition with innovation.</p>
    <p>Our mission is to deliver exceptional culinary experiences by combining the finest ingredients, expert craftsmanship, and unparalleled hospitality. We strive to create moments that linger in the memories of our guests.</p>
  </section>

  <!-- Gallery -->
  <section>
    <h2>Gallery</h2>
    <div class="gallery">
      <img src="../images/interior.jpg" alt="Interior Dining Space">
      <img src="../images/privatearea.jpg" alt="Private Event Area">
      <img src="../images/kitchen.jpg" alt="Kitchen in Action">
      <img src="../images/signature.jpg" alt="Signature Dish">
      <img src="../images/award.jpg" alt="Award-Winning Dish">
      <img src="../images/customers.jpg" alt="customers dinning">
      <img src="../images/reserve 2.jpg" alt="reservation a table">
      <img src="../images/bar.jpeg" alt="bar">
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials">
    <h2>What Our Guests Are Saying</h2>
    <div class="testimonial">
      <p>"An unforgettable experience! Every bite was a revelation."</p>
      <span>– Emily R., ★★★★★, March 2024</span>
    </div>
    <div class="testimonial">
      <p>"The ambiance, the service, the food—everything was perfect."</p>
      <span>– James L., ★★★★★, February 2024</span>
    </div>
    <div class="testimonial">
      <p>"The best fine dining experience I've ever had. Highly recommend the tasting menu!"</p>
      <span>– Sophia M., ★★★★★, January 2024</span>
    </div>
    <div class="testimonial">
      <p>"A true culinary journey. We'll be back for our anniversary!"</p>
      <span>– David and Sarah T., ★★★★★, December 2023</span>
    </div>
  </section>

  <!-- Meet the Team -->
  <section class="team">
    <h2>Meet the Team</h2>
    <p><strong>Executive Chef John Doe</strong>: With a career spanning 15 years, Chef John has honed his skills in Italy and France, earning accolades for his innovative approach to modern cuisine.</p>
    <p><strong>Jane Smith, Head Sommelier</strong>: A certified wine expert with a passion for pairing the perfect vintage with every dish.</p>
    <p><strong>Michael Brown, General Manager</strong>: Ensures every guest feels like royalty with impeccable service.</p>
    <p><strong>Emily Davis, Pastry Chef</strong>: Creates desserts that are as visually stunning as they are delicious.</p>
  </section>

  <!-- Awards and Recognition -->
  <section class="awards">
    <h2>Awards and Recognition</h2>
    <p>We are proud to have been recognized by:</p>
    <ul>
      <li>Michelin Guide: 2023 Star Award</li>
      <li>James Beard Foundation: 2022 Nominee</li>
      <li>New York Times: "Best Fine Dining Restaurant in NYC"</li>
      <li>Food & Wine Magazine: Top 10 Restaurants in the US</li>
    </ul>
  </section>

  <!-- Footer -->
  <footer>
    <div class="contact-info">
      <p>Contact: +1 (800) 123-4567 | info@foodie.com</p>
      <p>Opening Hours: Mon-Sun, 10 AM - 10 PM</p>
    </div>
    <div class="social-links">
      <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
      <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
    </div>
    <div class="newsletter">
      <input type="email" placeholder="Enter your email">
      <button>Subscribe</button>
    </div>
    <div class="map">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.183792477038!2d-73.9854284845936!3d40.74881797932799!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25903f2b3f3a1%3A0x4b1c3e5f1b3f3a1!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1712345678901!5m2!1sen!2sus" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
  </footer>

  <script>
    // JavaScript to toggle profile dropdown
    document.addEventListener("DOMContentLoaded", function () {
      const profileLink = document.querySelector('.profile-dropdown a');
      if (profileLink) {
        profileLink.addEventListener('click', function(e) {
          e.preventDefault();
          const dropdown = document.querySelector('.dropdown-content');
          dropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
          const dropdown = document.querySelector('.dropdown-content');
          if (dropdown && !e.target.closest('.profile-dropdown')) {
            dropdown.classList.remove('show');
          }
        });
      }
    });
  </script>
</body>
</html>