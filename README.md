FoodieForYou 🍽️
FoodieForYou is a feature-rich food ordering and restaurant management website built with PHP, MySQL, HTML, CSS, and JavaScript. It includes both user-facing pages and an admin panel.

✅ Live Demo: https://foodieforyou.42web.io
🖥️ Hosted via: InfinityFree (Free PHP + MySQL Hosting) 
youtube.com
+15
infinityfree.com
+15
youtube.com
+15

🚀 Folder Structure
pgsql
Copy
Edit
restaurant/
├── admin/
│   ├── add_item.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_items.php
│   ├── manage_reservations.php
│   ├── manage_staff.php
│   ├── reports.php
│   └── view_orders.php
│
├── css/
│   └── style.css
├── images/
│   └── [food & UI assets]
├── includes/
│   ├── db.php
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── about.php
│   ├── cart.php
│   ├── login.php
│   ├── logout.php
│   ├── menu.php
│   ├── orders.php
│   ├── payment.php
│   ├── reservation.php
│   └── signup.php
├── scripts.js
└── index.php
🗃️ Database Schema & Queries (via phpMyAdmin)
Table: users
sql
Copy
Edit
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  email VARCHAR(100),
  password VARCHAR(255)
);
Table: menu
sql
Copy
Edit
CREATE TABLE menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  description TEXT,
  price DECIMAL(10,2),
  image VARCHAR(255)
);
Table: cart
sql
Copy
Edit
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  item_id INT,
  quantity INT
);
Table: orders
sql
Copy
Edit
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  order_date DATETIME,
  total_amount DECIMAL(10,2),
  status VARCHAR(50)
);
Table: order_items
sql
Copy
Edit
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  item_id INT,
  quantity INT,
  price DECIMAL(10,2)
);
Table: payments
sql
Copy
Edit
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  payment_date DATETIME,
  amount DECIMAL(10,2),
  method VARCHAR(50),
  status VARCHAR(50)
);
Table: feedback
sql
Copy
Edit
CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  message TEXT,
  submitted_at DATETIME
);
Table: reservations
sql
Copy
Edit
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  reservation_date DATE,
  reservation_time TIME,
  num_people INT,
  message TEXT
);
Table: staff
sql
Copy
Edit
CREATE TABLE staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  role VARCHAR(100),
  email VARCHAR(100)
);
🔗 PHP-MySQL Integration Example (includes/db.php)
php
Copy
Edit
<?php
$conn = mysqli_connect(
  'sqlXXX.epizy.com',    // hostname
  'epiz_XXXXXX',         // username
  'your_password',       // password
  'epiz_XXXXXX_foodie'   // database name
);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
📁 Pages & Functionality
index.php – Homepage displaying featured items.

menu.php – Shows all menu items (from menu).

view_orders.php – User order history (from orders + order_items).

cart.php – Cart management (uses cart).

payment.php – Processes payments (logs to payments).

reservation.php – Reservation form (records to reservations).

signup/login/logout – User authentication (users table).

admin/ – Panel for adding/editing menu items, reservations, staff, generating reports, and viewing all orders.

🌐 Deployment on InfinityFree
Sign up on InfinityFree 
forum.infinityfree.com
+3
colorlib.com
+3
campcodes.com
+3
github.com
infinityfree.com
+6
medium.com
+6
youtube.com
+6
.

Create a hosting account → note down FTP and MySQL credentials.

Use File Manager or FileZilla to upload your project folder to the htdocs/ directory.

In Control Panel → MySQL Databases → create database.

Open phpMyAdmin → import the SQL file containing your schema.

Update includes/db.php with your hosting credentials.

Access your live URL (e.g., https://foodieforyou.epizy.com).

For changes to file or domain, update any paths in config or includes. 
forum.infinityfree.com
+4
forum.infinityfree.com
+4
youtube.com
+4
forum.infinityfree.com
+1
forum.infinityfree.com
+1
forum.infinityfree.com
+3
medium.com
+3
forum.infinityfree.com
+3
docs.bdus.cloud

📤 Deployment Steps (Git + GitHub + InfinityFree)
bash
Copy
Edit
cd /c/xampp/htdocs/restaurant
git init
touch .gitignore
# Add entries
echo "includes/db.php" >> .gitignore
git add .
git commit -m "Initial commit: FoodieForYou"
git remote add origin https://github.com/yourusername/FoodieForYou.git
git branch -M main
git push -u origin main --force
Then for InfinityFree:

bash
Copy
Edit
# Export SQL
mysqldump -u root -p foodie > db_schema.sql
# Upload SQL via phpMyAdmin on InfinityFree
📝 Screenshots
Above you'll find screenshots showing how the front-end and admin dashboard look so users and contributors get a visual feel for the project.

💡 Tips & Next Steps
Performance: Use image compression and asset minification.

SEO: Add meta tags, alt attributes, and structured data.

Security: Use prepared statements with PDO or MySQLi.

Extras: Implement email notifications, AJAX features in cart, or integrate an API for more dynamic functionality.

📝 License & Contact
License: MIT

Developer: Sai vardhan kallempudi

GitHub: https://github.com/Saivardhan190
