# 🎄 Christmas SMM Gift Panel 🎁

A festive, fully functional **Social Media Marketing (SMM) Reward System**. This web application allows users to earn virtual coins through **Daily Check-ins** and a **Spin & Win Wheel**, which can then be redeemed for real SMM services (Instagram Likes, Followers, etc.) via API integration.

<p align="center">
  <img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExcDdtY2lnODZ0eW12eXF6eXF6eXF6eXF6eXF6eXF6eXF6eXF6eSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/3o7TKLM3nCLAed3Y9q/giphy.gif" alt="Christmas SMM Demo" width="200">
  <br>
  <i>(Spin the wheel, earn coins, and boost your social presence!)</i>
</p>

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![SMM API](https://img.shields.io/badge/SMM-API%20Integration-orange?style=flat-square)](https://indiansmmservices.com/)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg?style=flat-square)](https://github.com/KING-OF-FLAME)

---

## 🌟 About The Project 📍

This project bridges the gap between **Gamification** and **SMM Panels**. Instead of paying cash, users pay with their time and engagement.

**Key Concepts:**
* **Gamified Earning:** Users earn coins via the "Santa Spin Wheel" and Daily Rewards.
* **Hybrid Order System:** Small orders are auto-processed via API; larger orders can be reviewed.
* **Smart Refill:** Spin chances refill automatically every 2 hours using intelligent timestamp logic.

---

## 🚀 Features 📍

### 👤 User Panel
* **🎅 Spin & Win Wheel:** Interactive canvas-based wheel with weighted logic.
* **📅 Daily Check-in:** Streak-based rewards for returning users.
* **⏳ Auto-Refill System:** Spins refill automatically every 2 hours.
* **🎁 Claim System:** Users exchange coins for Instagram Likes/Followers.
* **🎫 Coupon Code System:** Users can redeem promo codes (e.g., `SANTA`) for bonus coins.
* **📊 Order History:** Live status tracking with visual badges.

### 🛡️ Admin Panel
* **Dashboard Analytics:** Live counter for Users, Coins Liability, and Orders.
* **Order Management:** Check live API status, Bulk Approve, or Bulk Reject orders.
* **Service Management:** Import services from API or add custom manual services.
* **Settings Control:** Update site config, referral bonuses, and spin limits directly from UI.

---

## 🛠 Tech Stack

* **Backend:** PHP (PDO for secure Database interactions).
* **Frontend:** HTML5, CSS3 (Custom Christmas Theme), JavaScript (Wheel Logic).
* **Database:** MySQL.
* **API:** Integration with `IndianSMMServices` (v2 API).

---

## ⚙️ Installation Guide

You can install this on your local computer (XAMPP) or a live website (cPanel).

### 📂 Option 1: Localhost (XAMPP/WAMP)

1.  **Clone the Repo:**
    Download this repository as a ZIP or clone it:
    ```bash
    git clone https://github.com/KING-OF-FLAME/christmas-smm-panel.git
    ```
2.  **Move Files:**
    Copy the project folder into `C:\xampp\htdocs\gift`.
3.  **Create Database:**
    * Open `http://localhost/phpmyadmin`.
    * Click **New** -> Create database name: `christmas_smm`.
    * Click **Import** tab -> Choose `christmas_gift_smm.sql` (found in root folder) -> Click **Go**.
4.  **Configure Database:**
    Open `config/db.php` and set:
    ```php
    $host = 'localhost';
    $db_name = 'christmas_smm';
    $username = 'root';
    $password = ''; // Leave empty for XAMPP
    ```
5.  **Configure Base URL:**
    Open `config/config.php` and set:
    ```php
    define('BASE_URL', 'http://localhost/gift/');
    ```
6.  **Run:** Open `http://localhost/gift/` in your browser.

---

### ☁️ Option 2: Shared Hosting (cPanel/Hostinger)

1.  **Upload Files:**
    * Zip all project files.
    * Go to **cPanel > File Manager > public_html**.
    * Upload and Extract the ZIP.
2.  **Create Database:**
    * Go to **cPanel > MySQL Database Wizard**.
    * Create a Database (e.g., `user_christmas`).
    * Create a User (e.g., `user_admin`) and Password.
    * **Link User to Database** and check "ALL PRIVILEGES".
3.  **Import SQL:**
    * Go to **cPanel > phpMyAdmin**.
    * Select your new database.
    * Click **Import** -> Upload `christmas_gift_smm.sql`.
4.  **Configure Database:**
    Open `config/db.php` and enter the credentials you created in Step 2:
    ```php
    $host = 'localhost';
    $db_name = 'user_christmas'; // Your cPanel DB Name
    $username = 'user_admin';    // Your cPanel DB User
    $password = 'your_password'; // Your cPanel DB Pass
    ```
5.  **Configure Base URL:**
    Open `config/config.php` and set your domain:
    ```php
    define('BASE_URL', 'https://your-domain.com/'); // Your Domain Url
    ```

---

## 🔧 Configuration & Settings

### 1. Changing the Website URL (Critical)
To make sure redirects and styles work, you **must** update the `BASE_URL`.
* File: `config/config.php`
* Line: `define('BASE_URL', 'https://your-site.com/');`

### 2. Setting up SMM API
* Go to **IndianSMMServices** (or any panel supporting v2 API) and get your API Key.
* File: `config/config.php`
    ```php
    define('SMM_API_URL', 'https://indiansmmservices.com/api/v2');
    define('SMM_API_KEY', 'YOUR_API_KEY_HERE');
    ```

### 3. Admin Login
* Default Admin URL: `your-site.com/admin/login.php`
* You can set the first admin manually in the database `users` table by setting `role` to `admin`.

---
## 📂 Folder Structure
## 📂 Folder Structure

```text
/christmas-smm-panel/
│
├── admin/                  # Admin Dashboard & Management
│   ├── ban_user.php
│   ├── coupons.php         # Coupon Management
│   ├── edit_user.php
│   ├── fix_database.php
│   ├── import_services.php # API Service Importer
│   ├── index.php           # Admin Stats & Overview
│   ├── login.php
│   ├── logout.php
│   ├── order_action.php    # Approve/Reject Logic
│   ├── orders.php          # Bulk Order Management
│   ├── services.php
│   ├── settings.php        # Site Configuration
│   └── users.php
│
├── api/                    # Backend AJAX Endpoints
│   ├── claim.php           # Process SMM Claims
│   ├── cron.php
│   ├── redeem.php          # Coupon Logic
│   ├── spin.php            # Wheel Spin Logic
│   └── track_order.php
│
├── assets/                 # Frontend Resources
│   ├── css/
│   │   └── style.css
│   ├── images/
│   └── js/
│       └── main.js         # Frontend Logic (Wheel Animation)
│
├── config/                 # Core Configuration
│   ├── config.php          # API Keys, URLs, Constants
│   ├── db.php              # Database Connection
│   └── functions.php       # Helper Functions
│
├── includes/               # Reusable UI Components
│   ├── auth.php            # Login Check
│   ├── footer.php
│   └── header.php
│
├── sql/
│   └── database.sql        # Demo Structure
│
├── christmas_gift_smm.sql  # Main Database Import File
├── dashboard.php           # User Profile & History
├── index.php               # Main Page (Spin Wheel)
├── login.php
├── logout.php
└── register.php ```text

---

## 📸 Screenshots

| User Dashboard (Spin Wheel) | Admin Orders (Bulk Actions) |
| :---: | :---: |
| <img src="https://placehold.co/600x400/d32f2f/FFF?text=User+Dashboard" alt="User UI" width="100%"> | <img src="https://placehold.co/600x400/222/FFF?text=Admin+Panel" alt="Admin UI" width="100%"> |

---

## 🤝 Contributions

1.  Fork the Project.
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the Branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

---

## 📧 Contact

Github: [KING OF FLAME](https://github.com/KING-OF-FLAME)
Instagram: [yash.developer](https://instagram.com/yash.developer)

---

## 🙏 Acknowledgments

* **IndianSMMServices** for the backend API structure.
* **OpenAI** for logic debugging.
* **Bootstrap** for the responsive UI.
