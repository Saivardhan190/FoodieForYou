<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Foodie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../restaurant/css/styles.css">
  <script src="script.js" defer></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">
      <p>Foodie</p>
    </div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="../restaurant/pages/menu.php">Menu</a>
      <a href="../restaurant/pages/reservation.php">Reservation</a>
      <a href="../restaurant/pages/about.php">About</a>
      <a href="../restaurant/pages/cart.php" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
      </a>
      <?php if (isset($_SESSION['username'])): ?>
        <div class="profile-dropdown">
          <a href="#">
            <i class="fas fa-user"></i>
          </a>
          <div class="dropdown-content">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p><hr>
            <a href="../restaurant/pages/cart.php">Cart</a>
            <a href="../restaurant/pages/orders.php">Orders</a>
            <a href="../restaurant/pages/logout.php">Log Out</a>
          </div>
        </div>
      <?php else: ?>
        <div class="login-link">
          <a href="../restaurant/pages/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login</a>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Welcome Section -->
  <div class="welcome-section">
    <h1>Welcome to Foodie</h1>
    <p>Love at first bite</p>
    <div class="btn-container">
      <a href="../restaurant/pages/menu.php" class="btn">Explore Menu</a>
      <a href="../restaurant/pages/reservation.php" class="btn">Reserve Table</a>
    </div>
  </div>

  <!-- Chatbot Interface -->
  <div id="chatbot-container">
      <div id="chatbot-header">
        <span>FoodieBot</span>
        <button id="close-chatbot" aria-label="Close chatbot">&times;</button>
      </div>
      <div id="chatbot-body">
        <div id="chatbot-messages"></div>
      </div>
      <div id="chatbot-input-container">
        <input type="text" id="chatbot-input" placeholder="Type your message...">
        <button id="send-button">Send</button>
      </div>
  </div>
  <button id="chatbot-toggle" aria-label="Open chatbot">
    <i class="fas fa-robot"></i> <!-- Robot icon -->
  </button>


  <!-- Menu Section -->
  <div class="menu-section">
    <h2>Our Menu</h2>
    <p>Discover our delicious range of dishes crafted with love and passion.</p>
    <a href="../restaurant/pages/menu.php" class="btn full-menu-btn">View Full Menu</a>
  </div>

  <!-- Food Cards -->
  <div class="food-cards" id="menu">
    <div class="card">
      <img src="images/vegan sushi.jpg" alt="Food Item">
      <h3>Vegan Sushi</h3>
      <p>**Vegan Sushi Platter A colorful assortment of plant-based sushi rolls with avocado, cucumber, and marinated tofu, garnished with edible flowers and served with soy sauce, pickled ginger, and wasabi.</p>
      <a href="../restaurant/pages/menu.php"><button>Add to Cart</button></a>
    </div>
    <div class="card">
      <img src="images/butter chicken.jpeg" alt="Food Item">
      <h3>Butter Chicken</h3>
      <p>Butter Chicken is a rich and creamy Indian dish made with tender pieces of chicken cooked in a spiced tomato-based sauce, often enjoyed with naan or rice.</p>
      <a href="../restaurant/pages/menu.php"><button>Add to Cart</button></a>
    </div>
    <div class="card">
      <img src="images/mango mojito.jpg" alt="Food Item">
      <h3>Berry Blast Smoothie</h3>
      <p>A Berry Blast Smoothie is a refreshing and vibrant drink packed with the sweet-tart flavors of mixed berries, blended with yogurt or milk for a creamy, nutrient-rich treat</p>
      <a href="../restaurant/pages/menu.php"><button>Add to Cart</button></a>
    </div>
  </div>

  <!-- Reservation Section -->
  <div class="reservation-section">
    <h2>Reservation</h2>
    <p>Book your table now and enjoy a delightful dining experience with us.</p>
    <a href="../restaurant/pages/reservation.php" class="btn">Make Reservation</a>
  </div>

  <!-- About Section -->
  <section class="about-section">
    <h2>About Foodie</h2>
    <p>
      At Foodie, we believe that great food is more than just a meal—it's an experience. 
      Our journey began with a passion for creating dishes that bring people together, 
      blending traditional flavors with modern culinary techniques. 
      From our signature biryanis to our decadent desserts, every dish is crafted with care 
      and the freshest ingredients.
    </p>
    <p>
      Our restaurant is a place where food lovers can gather, share, and celebrate. 
      Whether you're here for a casual lunch, a family dinner, or a special occasion, 
      we strive to make every visit memorable. Come join us and experience the love 
      we put into every bite!
    </p>
  </section>

  <!-- Contact Us -->
  <section class="contact-us clearfix">
    <h2>Contact Us</h2>
    <p>Email: info@foodie.com | Phone: +123 456 7890</p>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <!-- Logo & Tagline -->
    <div class="footer-section">
      <img class="diviLogo" src="images/logo.png" alt="Foodie Logo">
      <p class="tagline">
        Just like a perfectly baked pastry, we created Foodie with special love and it shows.
      </p>
    </div>

    <!-- Open Hours -->
    <div class="footer-section">
      <h4>Open Hours</h4>
      <p>Mon-Fri: 9 AM - 6 PM</p>
      <p>Saturday: 9 AM - 4 PM</p>
      <p>Sunday: Closed</p>
    </div>

    <!-- Contact Us -->
    <div class="footer-section">
      <h4>Contact Us</h4>
      <p>176 W street name, New York, NY 10014</p>
      <p>Email: info@foodie.com</p>
      <p>Telephone: +1(800)123-4566</p>
    </div>

    <!-- Food Categories -->
    <div class="footer-section food-categories">
      <h4>Food Categories</h4>
      <div class="category-container">
        <!-- Veg Items -->
        <div class="category-section">
          <h5>Veg Items</h5>
          <ul>
            <li>Paneer Tikka</li>
            <li>Veg Biryani</li>
            <li>Aloo Paratha</li>
          </ul>
        </div>

        <!-- Non-Veg Items -->
        <div class="category-section">
          <h5>Non-Veg Items</h5>
          <ul>
            <li>Chicken Biryani</li>
            <li>Butter Chicken</li>
            <li>Fish Curry</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Subscribe Newsletter -->
    <div class="subscribe">
      <h4>Subscribe Newsletter</h4>
      <input type="email" name="email" id="email" placeholder="Email Address">
      <i class="fas fa-paper-plane fa-2x"></i>
    </div>

    <!-- Copyright & Social Icons -->
    <div class="copyright">
      <span>2023&#169; Foodie &#124; Design by Your Name</span>
      <i class="fab fa-facebook-f fa-2x"></i>
      <i class="fab fa-twitter fa-2x"></i>
      <i class="linkedIn fab fa-linkedin-in"></i>
      <i class="fab fa-instagram fa-2x"></i>
    </div>
  </footer>
</body>
</html>