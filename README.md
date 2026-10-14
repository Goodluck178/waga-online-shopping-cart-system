Academic Project
 WAGA - Online Shopping Cart System
 A secure, full-stack e-commerce platform built with PHP & MySQL, featuring persistent cart logic, role-based access control, and relational database design.

Project Overview
WAGA is a complete online shopping system that handles real-world e-commerce workflows: user registration, product catalog, persistent shopping cart, and order processing.
The project demonstrates backend development fundamentals critical for enterprise environments, secure authentication, database normalization, and transactional integrity.

Tech Stack (Codebase)
•	Backend: PHP 8.2.12 (Procedural PHP with OOP concepts, Session Management)
•	Database: MariaDB 10.4.32 - InnoDB Engine with Foreign Key Constraints
•	Frontend / Client-Side: HTML5, CSS3, Vanilla JavaScript
•	Security: `password_hash()` with BCRYPT (`$2y$10$`), SQL Injection prevention via constraints, Unique indexes on email/mobile
•	Server: Apache via XAMPP
•	Storage: Product images stored as `product_{hash}.jpeg/jpg/png` in filesystem

My system uses 6 relational tables with proper referential integrity:

1. users
•	id (PK), name, surname, email (UNIQUE), password (BCRYPT hashed), mobileNo (UNIQUE), profile_image (default.png or profile {ID}.jpg), role ENUM('user','admin')
•	Implements secure authentication with BCRYPT hashing (verified in my data: $2y$10$...)
•	Unique constraints on `email` and `mobileNo` to prevent duplicates
•	Role-based access: user vs admin, the system currently has 6 admin users.

2. products
•	id (PK), product_name, product_price DECIMAL (10,2), product_image, created_at
•	Sample data: HP Laptop (€650), HD Plasma TV (€300), sports car (€10000)

3. waga_cart (Active Cart System)
•	id (PK), user_id (FK -> users), order_status ENUM('pending','paid'), total_sum DECIMAL (10,2), created_at
•	This is my main cart implementation. Handles pending vs paid state.
•	Foreign Key `ON DELETE CASCADE` ensures data integrity.

4. waga_cart_item
•	id (PK), cart_id (FK -> waga_cart), product_id (FK -> products), quantity

5. orders & order_items
•	orders: id, user_id (FK), order_date, total_amount, status ENUM('pending','paid')
•	order_items: id, order_id (FK), product_id (FK), quantity, price
•	Secondary order system with full CASCADE constraints, ready for checkout finalization.

All tables use InnoDB with `utf8mb4` and proper foreign key relationships.

Key Features Implemented

Authentication & User Management:
•	Secure Registration/Login with BCRYPT password hashing
•	Profile image upload with default fallback (`default.png`)
•	Mobile number uniqueness validation
•	Role-based redirection (Admin Dashboard vs User Shop)

E-Commerce Core Logic:
•	Persistent cart (`waga_cart` + `waga_cart_item`) linked to `user_id` - survives login/logout unlike session-only carts
•	Add to cart with quantity handling (e.g., Cart ID 35 contains 5 different products totaling €21,428)
•	Cart status tracking: pending (active shopping) vs paid (completed)
•	Product catalog with image handling

Admin Capabilities:
•	User role management (admin/user)
•	Product CRUD
•	View paid carts as order history

Security Practices Demonstrated
•	Password Security: No plain-text passwords. All entries use `password_hash()` BCRYPT 
•	Data Integrity: Foreign keys with `ON DELETE CASCADE` prevent orphaned records.
•	Uniqueness: Unique indexes on `email` and `mobileNo` at database level.
•	Input Handling: Product images sanitized as hashed filenames (`product_6a03542db2d49.jpeg`) to prevent directory traversal.

 How to Run Locally
1. Start XAMPP Apache & MySQL
2. Clone to `C:\xampp\htdocs\waga\`
3. In phpMyAdmin, create `online_shop_db` and import `online_shop_db.sql`
4. Configure DB connection in `config.php` / `db_connect.php`:
    php
    $conn = new mysqli("127.0.0.1", "root", "", "online_shop_db"); Run: http://localhost/waga/Test Accounts (from my DB):
Admin: mads@gmail.com / my admin passwordUser: Goodluck@yahoo.com / my user password
