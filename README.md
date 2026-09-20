# VetHub – Setup Guide

## 📌 Project Overview

VetHub is a PHP-based veterinary management system that uses **XAMPP/Apache** for the web server and **MySQL** for the database.

Before running the project, **each user/developer must create the VetHub database and required tables in HeidiSQL**.

---

# 🛠️ Requirements

Make sure the following are installed:

- **XAMPP**
  - Apache
  - MySQL
- **HeidiSQL**
- **Visual Studio Code**
- A web browser such as Google Chrome

## Recommended XAMPP Configuration

| Service | Port |
|---|---:|
| Apache | 80 |
| MySQL | 3306 |

The project is intended to run through Apache using:

```text
http://localhost/VetHub/views/LoginPage.php
```

---

# 📂 Project Installation

1. Download or clone the VetHub project.
2. Place the entire `VetHub` folder inside:

```text
C:\xampp\htdocs\
```

The final folder structure should look like:

```text
C:\xampp\htdocs\VetHub\
```

3. Open **XAMPP Control Panel**.
4. Start:
   - Apache
   - MySQL

---

# 🗄️ DATABASE SETUP — REQUIRED

> **IMPORTANT:** The VetHub application will not work correctly unless the required database and tables are created first.

The application is configured to connect to:

```text
Host: localhost
Database: vethub_db
Username: root
Password: 
```

The default configuration expects the XAMPP MySQL `root` account with a blank password.

## Step 1 — Open HeidiSQL

1. Open **HeidiSQL**.
2. Connect to your local MySQL/MariaDB server.
3. Use:

```text
Hostname / IP: 127.0.0.1
Port: 3306
User: root
Password: 
```

> If your XAMPP MySQL configuration uses a different username, password, or port, update the project's database configuration accordingly.

---

## Step 2 — Create the Database and Tables

Open a new SQL query in HeidiSQL and run the following script.

```sql
-- ============================================
-- VETHUB DATABASE
-- ============================================

CREATE DATABASE IF NOT EXISTS vethub_db;
USE vethub_db;


-- ============================================
-- TABLE: tbl_owners
-- ============================================

CREATE TABLE tbl_owners (
    ownerID INT(11) NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postalCode VARCHAR(20) NOT NULL,
    createdAt DATETIME NULL DEFAULT NULL,
    updatedAt DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (ownerID),
    UNIQUE KEY unique_owner_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- TABLE: tbl_pets
-- ============================================

CREATE TABLE tbl_pets (
    petID INT(11) NOT NULL AUTO_INCREMENT,
    ownerID INT(11) NOT NULL,
    petName VARCHAR(50) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    dateOfBirth DATE NULL DEFAULT NULL,
    color VARCHAR(50) NULL DEFAULT NULL,
    microchipID VARCHAR(100) NULL DEFAULT NULL,
    createdAt DATETIME NULL DEFAULT NULL,
    updatedAt DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (petID),
    CONSTRAINT fk_pets_owner
        FOREIGN KEY (ownerID)
        REFERENCES tbl_owners(ownerID)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- TABLE: tbl_appointments
-- ============================================

CREATE TABLE tbl_appointments (
    appointmentID INT(11) NOT NULL AUTO_INCREMENT,
    petID INT(11) NOT NULL,
    ownerID INT(11) NOT NULL,
    appointmentDate DATE NOT NULL,
    appointmentTime TIME NOT NULL,
    status VARCHAR(50) NOT NULL,
    reason TEXT NOT NULL,
    notes TEXT NULL DEFAULT NULL,
    createdAt DATETIME NULL DEFAULT NULL,
    updatedAt DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (appointmentID),

    CONSTRAINT fk_appointments_pet
        FOREIGN KEY (petID)
        REFERENCES tbl_pets(petID)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_appointments_owner
        FOREIGN KEY (ownerID)
        REFERENCES tbl_owners(ownerID)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- TABLE: tbl_users
-- ============================================

CREATE TABLE tbl_users (
    userID INT(11) NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    prcNumber INT(11) NOT NULL,
    createdAt DATETIME NULL DEFAULT NULL,
    updatedAt DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (userID),
    UNIQUE KEY unique_user_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- TABLE: tbl_user_sessions
-- ============================================

CREATE TABLE tbl_user_sessions (
    sessionID INT(11) NOT NULL AUTO_INCREMENT,
    userID INT(11) NOT NULL,
    loginTime TIMESTAMP NULL DEFAULT NULL,
    logoutTime TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (sessionID),

    CONSTRAINT fk_sessions_user
        FOREIGN KEY (userID)
        REFERENCES tbl_users(userID)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- TABLE: tbl_error_logs
-- ============================================

CREATE TABLE tbl_error_logs (
    errorID INT(11) NOT NULL AUTO_INCREMENT,
    errorMessage VARCHAR(255) NULL DEFAULT NULL,
    errorTime DATETIME NULL DEFAULT NULL,
    createdAt DATETIME NULL DEFAULT NULL,
    updatedAt DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (errorID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

# 📋 Required Tables

After running the SQL script, the `vethub_db` database should contain these **six tables**:

```text
vethub_db
│
├── tbl_owners
├── tbl_pets
├── tbl_appointments
├── tbl_users
├── tbl_user_sessions
└── tbl_error_logs
```

## Table Relationships

```text
tbl_owners
    │
    ├──< tbl_pets
    │        │
    │        └──< tbl_appointments
    │
    └──< tbl_appointments


tbl_users
    │
    └──< tbl_user_sessions
```

The foreign-key relationships are:

- `tbl_pets.ownerID` → `tbl_owners.ownerID`
- `tbl_appointments.petID` → `tbl_pets.petID`
- `tbl_appointments.ownerID` → `tbl_owners.ownerID`
- `tbl_user_sessions.userID` → `tbl_users.userID`

The foreign keys use **ON UPDATE CASCADE** and **ON DELETE CASCADE**.

---

# 🔌 Database Connection

The project currently uses the following database configuration:

```php
private $host = "localhost";
private $dbName = "vethub_db";
private $username = "root";
private $password = "";
```

This is located in:

```text
model/database.php
```

If your local MySQL credentials are different, update this file before running the application.

---

# ▶️ Running the Application

After completing the database setup:

### 1. Start XAMPP

Start:

```text
Apache
MySQL
```

### 2. Confirm Apache

Open:

```text
http://localhost/
```

If Apache is running correctly, the XAMPP/Apache page should load.

### 3. Open VetHub

Use:

```text
http://localhost/VetHub/views/LoginPage.php
```

The project's VS Code launch configuration is also set to open this page.

---

# 🔐 Important: Database Credentials

Do **not** change the database name unless you also update:

```text
model/database.php
```

If you change the MySQL username/password, update the connection settings in the same file.

For team projects, avoid committing real passwords or email credentials to GitHub. Use local configuration/environment variables for sensitive credentials.

---

# ❗ Common Errors

## 1. `404 Not Found`

Check that the project is located exactly at:

```text
C:\xampp\htdocs\VetHub\
```

Also confirm that the page exists:

```text
VetHub/views/LoginPage.php
```

Then open:

```text
http://localhost/VetHub/views/LoginPage.php
```

---

## 2. `Connection failed`

Check:

- MySQL is running in XAMPP.
- HeidiSQL can connect to MySQL.
- Database name is exactly:

```text
vethub_db
```

- Username/password in `model/database.php` match your local MySQL setup.

---

## 3. `Table doesn't exist`

If you see an error involving a missing table, return to HeidiSQL and confirm that all six required tables exist:

```text
tbl_owners
tbl_pets
tbl_appointments
tbl_users
tbl_user_sessions
tbl_error_logs
```

---

## 4. `Index of /VetHub`

If Apache displays:

```text
Index of /VetHub
```

instead of the login page, do not assume the Apache port is wrong.

Open the actual application entry page:

```text
http://localhost/VetHub/views/LoginPage.php
```

---

# ✅ Setup Checklist

Before submitting or running the project, make sure:

- [ ] XAMPP is installed.
- [ ] Apache is running.
- [ ] MySQL is running.
- [ ] VetHub is inside `C:\xampp\htdocs\`.
- [ ] HeidiSQL is connected to the local MySQL server.
- [ ] Database `vethub_db` exists.
- [ ] `tbl_owners` exists.
- [ ] `tbl_pets` exists.
- [ ] `tbl_appointments` exists.
- [ ] `tbl_users` exists.
- [ ] `tbl_user_sessions` exists.
- [ ] `tbl_error_logs` exists.
- [ ] Foreign keys were created successfully.
- [ ] `model/database.php` has the correct local database credentials.
- [ ] `http://localhost/VetHub/views/LoginPage.php` opens successfully.

---

## 📚 Database Reference

The database name, six required tables, column definitions, primary keys, unique email constraints, and foreign-key relationships in this README are based on the VetHub project database specification provided with the project.

