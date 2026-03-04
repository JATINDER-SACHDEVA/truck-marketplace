# TruckBazaar India (OLX-style Truck Marketplace)

A complete truck classified marketplace for India built using **PHP + MySQL + HTML/CSS/JS**.

## Features

- User registration, login, forgot/reset password
- User dashboard with own listings, edit/delete, mark sold
- Truck ad posting with category, specs, location, and multi-image uploads
- Search and filters (keyword, city/location, price range, category)
- Listing details page with gallery and WhatsApp contact button
- Admin panel for listing approvals, spam deletion, and user management
- Auto-location detection (browser geolocation + reverse geocoding) with manual override
- Security basics: prepared statements (SQL injection safe), CSRF tokens, honeypot anti-spam, upload MIME/size validation, posting rate limit

## Project Structure

```text
truck-marketplace/
├── config/
│   └── config.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── public/
│   ├── index.php
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   ├── create-listing.php
│   ├── listing.php
│   ├── dashboard/
│   │   ├── index.php
│   │   ├── edit-listing.php
│   │   ├── delete-listing.php
│   │   └── mark-sold.php
│   ├── admin/
│   │   ├── index.php
│   │   ├── approve.php
│   │   ├── delete-listing.php
│   │   └── delete-user.php
│   └── assets/
│       ├── css/style.css
│       └── js/app.js
├── uploads/
├── sql/
│   └── truck_marketplace.sql
└── README.md
```

## Database Setup

1. Create a MySQL database (or import directly):
   - file: `sql/truck_marketplace.sql`
2. Import using phpMyAdmin or CLI:

```bash
mysql -u your_db_user -p your_db_name < sql/truck_marketplace.sql
```

Default admin login:
- **Email**: `admin@truckbazaar.in`
- **Password**: `Admin@123`

## Shared Hosting Deployment Steps

1. Upload project files to hosting (cPanel File Manager or FTP).
2. Place `public/` contents inside `public_html/` (or point domain document root to `/public`).
3. Keep `config/`, `includes/`, `uploads/`, `sql/` outside public web root when possible.
4. Update database credentials in `config/config.php`:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
5. Create writable permissions for `uploads/` directory (typically `755` or `775`).
6. Ensure PHP version is 8.0+ with PDO MySQL enabled.
7. Visit your domain and test:
   - User registration/login
   - Posting listing
   - Admin approval flow

## Notes

- Forgot password is in **development mode** and displays reset link as flash message.
- For production: integrate SMTP mail for real reset emails.
- For better anti-spam: add Google reCAPTCHA + IP-based throttling in DB.
