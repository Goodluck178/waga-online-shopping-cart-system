# Academic Project

# waga-online-shopping-cart-system
 
A secure, full-stack e-commerce platform built with PHP & MySQL, featuring persistent cart logic, role-based access control, and relational database design.

## Project Overview

WAGA is a complete online shopping system that handles real-world e-commerce workflows: user registration, product catalog, persistent shopping cart, and order processing.
The project demonstrates backend development fundamentals critical for enterprise environments, secure authentication, database normalization, and transactional integrity.

## Tech Stack 

PHP  

MariaDB  

HTML5

CSS3

Vanilla JavaScript

Apache via XAMPP



#### My system uses 6 relational tables with proper referential integrity:

#### 1.  users

•	Implements secure authentication with BCRYPT hashing (verified in my data: $2y$10$...)

•	Unique constraints on `email` and `mobileNo` to prevent duplicates

•	Role-based access: user vs admin, the system currently has 6 admin users.

#### 2. products

#### 3. waga_cart (Active Cart System)

•	This is my main cart implementation. Handles pending vs paid state.

•	Foreign Key `ON DELETE CASCADE` ensures data integrity.

#### 4. waga_cart_item

#### 6. orders & order_item

•	Secondary order system with full CASCADE constraints, ready for checkout finalization.

#### All tables use InnoDB with `utf8mb4` and proper foreign key relationships.

## Key Features Implemented

#### Authentication & User Management:

•	Secure Registration/Login with BCRYPT password hashing

•	Profile image upload with default fallback (`default.png`)

•	Mobile number uniqueness validation

•	Role-based redirection (Admin Dashboard vs User Shop)

#### E-Commerce Core Logic:

•	Persistent cart (`waga_cart` + `waga_cart_item`) linked to `user_id` - survives login/logout unlike session-only carts

•	Add to cart with quantity handling 

•	Cart status tracking: pending (active shopping) vs paid (completed)

•	Product catalog with image handling

#### Admin Capabilities:

•	User role management (admin/user)

•	Product CRUD

•	View paid carts as order history

#### Security Practices Demonstrated

•	Password Security: No plain-text passwords. All entries use `password_hash()` BCRYPT

•	Data Integrity: Foreign keys with `ON DELETE CASCADE` prevent orphaned records.

•	Uniqueness: Unique indexes on `email` and `mobileNo` at database level.

•	Input Handling: Product images sanitized as hashed filenames (`product_6a03542db2d49.jpeg`) to prevent directory traversal.

 ## How to Run
 
https://waga-online.morganugochukwu.com/waga/
