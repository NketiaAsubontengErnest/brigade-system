# Brigade Company Management System

A complete, production-ready **Boys' & Girls' Brigade Company Management System** built with PHP MVC architecture, PostgreSQL, and Bootstrap 5.

## Features

- **Public Website** - Professional website with about, activities, events, news, gallery, and member registration
- **Admin Dashboard** - Full management dashboard with real-time statistics and charts
- **Member Portal** - Members can view their profile, dues, payments, attendance, training, badges, and digital membership card
- **Dues & Payments** - Complete dues management with partial payments, receipts, and PDF generation
- **Finance Management** - Income and expense tracking with financial reports
- **Attendance** - Activity-based attendance tracking with bulk marking
- **Training & Badges** - Training course management and badge/award tracking
- **Events** - Event management with member registration
- **Reports** - Membership, attendance, dues, finance, training, and event reports with PDF/Excel export
- **Digital Membership Card** - QR code verified digital membership cards
- **Role-Based Access Control** - Granular permissions system with Super Admin, Captain, Treasurer, etc.
- **Audit Logging** - Complete audit trail for all critical operations
- **Security** - CSRF protection, password hashing, prepared statements, XSS protection

## Technology Stack

- **PHP 8.2+** with custom MVC architecture
- **PostgreSQL** database with PDO
- **Bootstrap 5** for responsive UI
- **Chart.js** for dashboard charts
- **Dompdf** for PDF generation
- **PhpSpreadsheet** for Excel export
- **Composer** for dependency management

## Installation

### Prerequisites

- PHP 8.2 or higher
- PostgreSQL 12+
- Composer
- A web server (Apache/Nginx) or PHP built-in server

### Step 1: Clone/Extract the project

```bash
cd brigade-system
```

### Step 2: Install dependencies

```bash
composer install
```

### Step 3: Configure environment

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=brigade_system
DB_USERNAME=postgres
DB_PASSWORD=your_password
APP_URL=http://localhost:8000
APP_KEY=your-random-32-char-string-here-!
```

### Step 4: Create the database

```sql
CREATE DATABASE brigade_system;
```

### Step 5: Run migrations and seed data

```bash
php artisan migrate
php artisan seed
```

### Step 6: Start the development server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### Or use PHP built-in server directly:

```bash
cd public
php -S localhost:8000
```

## Default Login Credentials

### Super Admin
- **Email:** admin@brigade.com
- **Password:** admin123

### Captain
- **Email:** captain@brigade.com
- **Password:** captain123

### Treasurer
- **Email:** treasurer@brigade.com
- **Password:** treasurer123

> ⚠️ Change these passwords immediately in production!

## Project Structure

```
brigade-system/
├── app/
│   ├── controllers/     # All controllers (17 controllers)
│   ├── models/          # Base model
│   ├── views/           # All views organized by module
│   │   ├── layouts/     # admin, public, auth layouts
│   │   ├── auth/        # Login, password reset
│   │   ├── dashboard/   # Admin dashboard
│   │   ├── members/     # Member management
│   │   ├── dues/        # Dues management
│   │   ├── payments/    # Payment management
│   │   ├── finance/     # Financial management
│   │   ├── attendance/  # Attendance tracking
│   │   ├── events/      # Event management
│   │   ├── training/    # Training courses
│   │   ├── badges/      # Badge management
│   │   ├── awards/      # Award management
│   │   ├── reports/     # Report views
│   │   ├── public/      # Public website
│   │   ├── member_portal/ # Member portal
│   │   └── ...
│   ├── core/            # Core classes (Router, Database, View, etc.)
│   ├── middleware/       # Auth, Guest, Permission middleware
│   ├── helpers/         # Global helper functions
│   └── services/        # Business logic services
├── config/
├── database/
│   ├── migrations/      # Database migration files
│   └── seeders/         # Seed data
├── public/              # Public web root
│   ├── index.php        # Front controller
│   ├── assets/          # CSS, JS, images
│   └── uploads/         # Uploaded files
├── routes/
│   └── web.php          # All route definitions
├── storage/
│   ├── logs/
│   └── cache/
├── .env                 # Environment configuration
├── .env.example         # Environment template
├── artisan              # CLI tool
└── composer.json
```

## Database Tables

The system creates the following tables via migrations:

- **users** - System users with roles
- **roles** - User roles (Super Admin, Captain, etc.)
- **permissions** - Granular permissions
- **role_permissions** - Role-permission mapping
- **company_profile** - Company information (single record)
- **members** - Brigade members
- **guardians** - Parent/guardian information
- **sections** - Brigade sections (age groups)
- **officers** - Officer assignments
- **positions** - Officer positions
- **activities** - Brigade activities
- **attendance_sessions** - Attendance recording sessions
- **attendance** - Individual attendance records
- **events** - Events and activities
- **event_registrations** - Event member registrations
- **dues_types** - Types of dues
- **dues** - Dues records
- **member_dues** - Member-dues assignments
- **payments** - Payment records
- **income** - Income records
- **expenses** - Expense records
- **training_courses** - Training programs
- **training_enrollments** - Training enrollments
- **badges** - Badge definitions
- **member_badges** - Badge awards
- **awards** - Member awards
- **announcements** - Announcements
- **news** - News articles
- **gallery_albums** - Photo albums
- **gallery_images** - Gallery photos
- **documents** - Documents
- **notifications** - System notifications
- **audit_logs** - Audit trail

## Routes

### Public Routes
- `/` - Homepage
- `/about` - About page
- `/leadership` - Leadership page
- `/membership` - Membership info
- `/activities` - Activities listing
- `/events` - Events listing
- `/news` - News articles
- `/news/{slug}` - News detail
- `/gallery` - Photo gallery
- `/documents` - Public documents
- `/contact` - Contact page
- `/register` - Member registration
- `/login` - Login

### Admin Routes (require authentication)
- `/dashboard` - Admin dashboard
- `/members` - Member management
- `/officers` - Officer management
- `/sections` - Section management
- `/activities` - Activity management
- `/attendance` - Attendance tracking
- `/events` - Event management
- `/dues` - Dues management
- `/payments` - Payment management
- `/finance` - Financial management
- `/training` - Training management
- `/badges` - Badge management
- `/awards` - Award management
- `/announcements` - Announcements
- `/news` - News management
- `/gallery` - Gallery management
- `/documents` - Document management
- `/reports` - Reports & exports
- `/users` - User management
- `/settings` - Settings & audit logs

### Member Portal Routes
- `/portal` - Member dashboard
- `/portal/profile` - My profile
- `/portal/attendance` - My attendance
- `/portal/dues` - My dues
- `/portal/payments` - My payments
- `/portal/events` - Events
- `/portal/training` - Training
- `/portal/badges` - Badges & Awards
- `/portal/card` - Digital membership card

## Business Rules

1. **Single Company** - This system manages ONE Brigade Company
2. **Partial Payments** - Supported with automatic balance calculation
3. **Overdue Detection** - Automatic status updates based on due dates
4. **Payment Transactions** - All payment operations use database transactions
5. **Unique Receipts** - Every payment has a unique receipt number
6. **Unique Member Numbers** - Configurable prefix (default: BGB-YYYY-NNNN)
7. **Audit Logging** - All critical operations are logged
8. **Financial Security** - Records are voided, never deleted
9. **Authorization** - Server-side permission checks on all operations
10. **CSRF Protection** - All forms use CSRF tokens

## Deployment

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName brigade.example.com
    DocumentRoot /var/www/brigade-system/public
    
    <Directory /var/www/brigade-system/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name brigade.example.com;
    root /var/www/brigade-system/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate a unique `APP_KEY`
- [ ] Change all default passwords
- [ ] Configure proper file upload limits
- [ ] Enable HTTPS/SSL
- [ ] Set up database backups
- [ ] Configure cron jobs for overdue detection
- [ ] Set proper file permissions on `storage/` directory
- [ ] Remove `.env.example` from public access

## License

Proprietary - Brigade Company Management System
