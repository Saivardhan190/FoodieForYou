# 🍽️ FoodieForYou

**FoodieForYou** is a feature-rich food ordering and restaurant management website built with **PHP, MySQL, HTML, CSS, and JavaScript**. It includes both user-facing pages and an admin panel.

- ✅ **Live Demo**: [https://foodieforyou.42web.io](https://foodieforyou.42web.io)  
- 🖥️ **Hosted via**: InfinityFree (Free PHP + MySQL Hosting)  
- 👨‍💻 **Developer**: Sai Vardhan Kallempudi  
- 🐙 **GitHub**: [github.com/Saivardhan190](https://github.com/Saivardhan190)

---

#🗃️ Database Schema & Queries (via phpMyAdmin)
### Table: users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  email VARCHAR(100),
  password VARCHAR(255)
);

### Table: menu
CREATE TABLE menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  description TEXT,
  price DECIMAL(10,2),
  image VARCHAR(255)
);

### Table: cart
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  item_id INT,
  quantity INT
);

### Table: orders
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  order_date DATETIME,
  total_amount DECIMAL(10,2),
  status VARCHAR(50)
);

### Table: order_items
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  item_id INT,
  quantity INT,
  price DECIMAL(10,2)
);

### Table: payments
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  payment_date DATETIME,
  amount DECIMAL(10,2),
  method VARCHAR(50),
  status VARCHAR(50)
);

### Table: feedback
CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  message TEXT,
  submitted_at DATETIME
);

### Table: reservations
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  reservation_date DATE,
  reservation_time TIME,
  num_people INT,
  message TEXT
);

### Table: staff
CREATE TABLE staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  role VARCHAR(100),
  email VARCHAR(100)
);

# 📁 Pages & Functionality
index.php – Homepage displaying featured items.

menu.php – Shows all menu items (from menu).

view_orders.php – User order history (from orders + order_items).

cart.php – Cart management (uses cart).

payment.php – Processes payments (logs to payments).

reservation.php – Reservation form (records to reservations).

signup/login/logout – User authentication (users table).

admin/ – Panel for adding/editing menu items, reservations, staff, generating reports, and viewing all orders.

# 🌐 Deployment on InfinityFree
Sign up on InfinityFree 

1.Create a hosting account → note down FTP and MySQL credentials.

2.Use File Manager or FileZilla to upload your project folder to the htdocs/ directory.

3.In Control Panel → MySQL Databases → create database.

4.Open phpMyAdmin → import the SQL file containing your schema.

5.Update includes/db.php with your hosting credentials.

6.Access your live URL (e.g., https://foodieforyou.epizy.com).

7.For changes to file or domain, update any paths in config or includes. 

# 📤 Deployment Steps (Git + GitHub + InfinityFree)

1.cd /c/xampp/htdocs/restaurant

2.git init

3.touch .gitignore

4.echo "includes/db.php" >> .gitignore

5.git add .

6.git commit -m "Initial commit: FoodieForYou"

7.git remote add origin https://github.com/yourusername/FoodieForYou.git

8.git branch -M main

9.git push -u origin main --force.

# Upload SQL via phpMyAdmin on InfinityFree
📝 Screenshots
Above you'll find screenshots showing how the front-end and admin dashboard look so users and contributors get a visual feel for the project.

# 💡 Tips & Next Steps
Performance: Use image compression and asset minification.

SEO: Add meta tags, alt attributes, and structured data.

Security: Use prepared statements with PDO or MySQLi.

Extras: Implement email notifications, AJAX features in cart, or integrate an API for more dynamic functionality.

# 📝 License & Contact
License: MIT

Developer: Sai vardhan kallempudi

GitHub: https://github.com/Saivardhan190
