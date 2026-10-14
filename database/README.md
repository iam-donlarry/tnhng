# The Next Highlandah (TNH Season 3) - Database Setup Guide

This directory contains the complete database schema, seed data, and 1-click installation script for deploying the microsite to production.

---

## Files in this Directory

| File | Description |
| :--- | :--- |
| `schema.sql` | Pure DDL structure (creates `outlets`, `promoters`, `participants`, `challenges`, `points_log` with indexes & foreign keys). |
| `seeds.sql` | Seed data (activation venues in Lagos/Abuja/PH, 15 official scorecard challenges, default admin & ambassadors). |
| `install.sql` | **Recommended for Production**: Complete all-in-one import script (schema + seed data combined). |
| `.htaccess` | Restricts direct web HTTP access to SQL dumps. |

---

## Production Deployment Steps

### Option A: Import via cPanel / phpMyAdmin (GUI)
1. Log into your production hosting cPanel or cloud database manager.
2. Under **Databases**, create a new MySQL database (e.g. `tnhng_db` or `microsite`).
3. Create a database user with a strong password and assign all privileges to the database.
4. Open **phpMyAdmin**, select the newly created database.
5. Click the **Import** tab at the top.
6. Choose `database/install.sql` (or `database/schema.sql`) and click **Go** / **Import**.

### Option B: Import via MySQL Command Line (CLI)
Run the following command on your server:
```bash
mysql -u [DB_USER] -p [DB_NAME] < database/install.sql
```
*(Enter your MySQL password when prompted)*

---

## Connecting the Application

Update your database credentials in `config/db.php`:
```php
define('DB_HOST', 'localhost');      // or production DB host / IP
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_strong_password');
define('DB_NAME', 'your_database_name');
```

---

## Default Administrative Credentials

| Account | Email | Staff Code | Default Password | Role |
| :--- | :--- | :--- | :--- | :--- |
| **Campaign Lead (Admin)** | `admin@tnhng.com` | `ADM-001` | `admin123` | `admin` |
| **Ambassador 1** | `emeka@tnhng.com` | `PRM-101` | `promoter123` | `promoter` |
| **Ambassador 2** | `folake@tnhng.com` | `PRM-102` | `promoter123` | `promoter` |
| **Ambassador 3** | `chinedu@tnhng.com` | `PRM-103` | `promoter123` | `promoter` |
| **Ambassador 4** | `fatima@tnhng.com` | `PRM-104` | `promoter123` | `promoter` |

> [!IMPORTANT]
> Change the default admin and promoter passwords immediately after importing to production.
