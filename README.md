# sealCMS — Content Management System & API

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?style=flat-square&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-4169E1?style=flat-square&logo=postgresql)](https://www.postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7%2B-DC382D?style=flat-square&logo=redis)](https://redis.io)

`sealCMS` is a robust, modular content management system and RESTful API backend. It combines a dynamic Livewire-powered administrative dashboard with token-authenticated endpoints for decoupled/headless frontend web applications.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Implemented Features](#implemented-features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Local Development Setup](#local-development-setup)
- [Production Installation & Deployment](#production-installation--deployment)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Directory & File Permissions](#directory--file-permissions)
- [Web Server Configuration](#web-server-configuration)
  - [Nginx](#nginx-configuration)
  - [Apache](#apache-configuration)
  - [HTTPS & SSL Setup](#https--ssl-setup)
- [Background Workers & Automation](#background-workers--automation)
  - [Queue Workers (Supervisor)](#queue-workers-supervisor)
  - [Task Scheduler](#task-scheduler)
- [Frontend Assets Workflow](#frontend-assets-workflow)
- [Application Update Procedure](#application-update-procedure)
- [Testing & Code Quality](#testing--code-quality)
- [Project Directory Structure](#project-directory-structure)
- [Troubleshooting](#troubleshooting)
- [Security Best Practices](#security-best-practices)
- [Contributing](#contributing)
- [Support](#support)
- [License](#license)

---

## Project Overview

`sealCMS` is designed to serve as both a standalone web-based management portal and a headless API provider. 

### Core Purpose
- Provide administrators and editors with a responsive, authenticated dashboard to manage blog articles, service portfolios, customer inquiries, job vacancies, and system users.
- Expose a clean, secure REST API consumed by modern client-side frontend applications (e.g., React, Vue, Next.js, or mobile clients).
- Execute background jobs asynchronously (such as automated daily database backups and notification delivery) without stalling HTTP requests.

---

## Implemented Features

### Administrative Dashboard (Livewire & Blade)
- **Blog & Taxonomy Management**: Full CRUD for articles, categories, and tags with slug generation and publishing workflows.
- **Service Catalog**: Management of service offerings, descriptions, and categories.
- **Project Showcase / Case Studies**: Portfolio showcase management with rich descriptions and media associations.
- **Career Openings**: Manage open job listings, specifications, and applications.
- **Inquiry & Lead Pipelines**: Real-time management and status tracking for:
  - Contact form submissions
  - Call/callback requests
  - Project quote/consultation requests
- **Newsletter Subscriptions**: Subscriber tracking, activity status, and cryptographically signed unsubscription handlers.
- **User & Access Control (RBAC)**: Comprehensive role-based permissions powered by Spatie (`admin`, `editor`, `author`, `support`).
- **Backup Management**: Administrative dashboard interface to run, monitor, and clean database and file backups.
- **Account & Security Settings**:
  - Profile information and password management.
  - Two-Factor Authentication (2FA) via QR Code / TOTP.
  - WebAuthn Passkeys enrollment and management (`@laravel/passkeys`).
  - Appearance and theme preferences (Light / Dark / System).

### Headless REST API (`routes/api.php`)
- **Authentication**: Token generation endpoint (`POST /api/login`) via Laravel Sanctum.
- **Public/Protected Content**:
  - `GET /api/posts` & `GET /api/posts/{slug}`
  - `GET /api/services`
  - `GET /api/projects`
  - `GET /api/jobs` & `GET /api/jobs/{slug}`
- **Inquiry Submission Endpoints**:
  - `POST /api/contact`
  - `POST /api/calls`
  - `POST /api/project-request`
- **Subscription Endpoints**:
  - `POST /api/newsletter`
  - `POST /api/unsubscribe` (Signed URL validation)

---

## Technology Stack

- **Backend Framework**: Laravel 13.x
- **Language**: PHP 8.3 / 8.4+
- **Database**: PostgreSQL (Primary) / SQLite / MySQL
- **In-Memory Cache & Queues**: Redis (via `predis`)
- **Admin UI Components**: Livewire 4.x, Flux UI, Alpine.js
- **Styling**: Tailwind CSS v4
- **Asset Bundler**: Vite 8.x
- **Authentication & Security**:
  - Laravel Fortify
  - Laravel Sanctum
  - Laravel Passkeys (`@laravel/passkeys`)
  - Spatie Laravel-Permission
- **System Maintenance**: Spatie Laravel-Backup

---

## System Requirements

Before installing, ensure your host machine or server satisfies the following prerequisites:

- **PHP**: `>= 8.3` (PHP 8.4 recommended)
  - Required Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_pgsql` (or `pdo_mysql` / `pdo_sqlite`), `redis`, `session`, `tokenizer`, `xml`, `zip`
- **Composer**: `>= 2.2`
- **Node.js**: `>= 20.x` or `>= 22.x` & **npm**: `>= 10.x`
- **Database Engine**: PostgreSQL `>= 15`, MySQL `>= 8.0`, or SQLite 3
- **Redis Server**: `>= 7.0` (required for queue, session, and cache drivers)
- **Web Server** (for production): Nginx or Apache with `mod_rewrite`

---

## Local Development Setup

For running the application locally on your workstation without a public domain:

### 1. Clone the Repository
```bash
git clone https://github.com/Alfred336/seal.git my-seal-app
cd my-seal-app
```

### 2. Install Dependencies
```bash
# Install PHP packages
composer install

# Install Node modules
npm install
```

### 3. Setup Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Local Database
For local development, you can use either PostgreSQL or SQLite:

**Option A: Using SQLite (Quickest)**
```bash
touch database/database.sqlite
```
Update your `.env` file:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
CACHE_STORE=array
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**Option B: Using Local PostgreSQL**
Ensure your local PostgreSQL instance is running, create a database, and update `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=myapp_dev
DB_USERNAME=myapp_user
DB_PASSWORD=secret
```

### 5. Run Migrations and Seed Database
```bash
php artisan migrate --seed
```

> [!NOTE]
> The seeder creates a default administrative user:
> - **Email**: `admin@sealtech.test`
> - **Password**: `password`

### 6. Start Development Servers
Run the Laravel development server and Vite asset compiler:
```bash
# Terminal 1: Laravel backend
php artisan serve

# Terminal 2: Vite hot-reloading
npm run dev
```

Visit the application in your browser at `http://127.0.0.1:8000`.

---

## Production Installation & Deployment

Follow these steps to deploy the application on any standard Linux server (Ubuntu, Debian, AlmaLinux, RHEL, etc.):

### Step 1: Place Project in Target Directory
The application can be placed in any directory of your choice (e.g., `/var/www/myproject`, `/opt/myproject`, or `/home/deploy/apps/myproject`).

```bash
# Example: Deploying to /var/www/myapp
sudo mkdir -p /var/www/myapp
sudo chown -R $USER:$USER /var/www/myapp

git clone https://github.com/Alfred336/seal.git /var/www/myapp
cd /var/www/myapp
```

### Step 2: Install Composer Dependencies
Install production dependencies without development packages:
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Configure `.env`
```bash
cp .env.example .env
nano .env
```
Update your application URL, production database credentials, Redis configuration, and mail settings.

```bash
php artisan key:generate
```

### Step 4: Run Migrations and Seeders
```bash
php artisan migrate --force

# Run seeders if this is a fresh setup
php artisan db:seed --force
```

### Step 5: Link Storage
Create the symbolic link from `public/storage` to `storage/app/public`:
```bash
php artisan storage:link
```

### Step 6: Compile Frontend Assets
Build production-optimized JavaScript and CSS:
```bash
npm ci
npm run build
```

### Step 7: Cache Configuration and Routes
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Environment Configuration

Below is a reference guide to the essential variables in `.env`:

| Variable | Description | Example |
|---|---|---|
| `APP_NAME` | Display name of the application | `"My CMS"` |
| `APP_ENV` | Environment context (`local`, `production`, `staging`) | `production` |
| `APP_KEY` | 32-character encryption key (generated via artisan) | `base64:...` |
| `APP_DEBUG` | Detailed debug error pages (`true` in dev, `false` in prod) | `false` |
| `APP_URL` | Canonical web URL of the application | `https://example.com` or `https://cms.example.com` |
| `DB_CONNECTION` | Database driver (`pgsql`, `mysql`, `sqlite`) | `pgsql` |
| `DB_HOST` | Database host address | `127.0.0.1` |
| `DB_PORT` | Database server port | `5432` (PostgreSQL) or `3306` (MySQL) |
| `DB_DATABASE` | Database name | `myapp_production` |
| `DB_USERNAME` | Database user account | `myapp_user` |
| `DB_PASSWORD` | Strong database user password | `YourStrongPasswordHere` |
| `CACHE_STORE` | Cache driver (`redis`, `file`, `array`) | `redis` |
| `QUEUE_CONNECTION` | Queue connection driver (`redis`, `database`, `sync`) | `redis` |
| `SESSION_DRIVER` | Session storage driver (`redis`, `cookie`, `file`) | `redis` |
| `REDIS_HOST` | Redis host server | `127.0.0.1` |
| `REDIS_PORT` | Redis server port | `6379` |
| `REDIS_PASSWORD` | Redis authentication password (if configured) | `null` |
| `MAIL_MAILER` | Mail sending driver (`smtp`, `log`, `sendmail`) | `smtp` |
| `MAIL_HOST` | SMTP server hostname | `smtp.mailgun.org` |
| `MAIL_PORT` | SMTP port | `587` |
| `MAIL_USERNAME` | SMTP account username | `postmaster@example.com` |
| `MAIL_PASSWORD` | SMTP account password | `smtp_password` |
| `MAIL_FROM_ADDRESS` | Global sender email address | `no-reply@example.com` |

> [!IMPORTANT]
> When `APP_URL` begins with `https://`, the application automatically forces secure redirects and secure cookies. Ensure your web server SSL certificate is in place before setting `https://`.

---

## Database Setup

### Setting Up PostgreSQL
Connect to your PostgreSQL server as an administrator:

```bash
sudo -u postgres psql
```

Create a database and a dedicated user with appropriate permissions:

```sql
-- Replace myapp_user, myapp_db, and your_password with your actual values
CREATE USER myapp_user WITH PASSWORD 'your_password';
CREATE DATABASE myapp_db OWNER myapp_user;
GRANT ALL PRIVILEGES ON DATABASE myapp_db TO myapp_user;
\q
```

In your `.env` file, configure the connection accordingly:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=myapp_db
DB_USERNAME=myapp_user
DB_PASSWORD=your_password
```

---

## Directory & File Permissions

The web server user (commonly `www-data` on Ubuntu/Debian, `nginx` or `apache` on RHEL/CentOS) must have write access to `storage` and `bootstrap/cache`.

Never set permissions to `777`. Use standard group-ownership practices:

```bash
# Replace /path/to/project with your actual installation directory
sudo chown -R $USER:www-data /path/to/project

# Ensure proper read/write directory permissions
sudo find /path/to/project/storage /path/to/project/bootstrap/cache -type d -exec chmod 775 {} +

# Ensure proper file permissions
sudo find /path/to/project/storage /path/to/project/bootstrap/cache -type f -exec chmod 664 {} +
```

---

## Web Server Configuration

### Nginx Configuration

Create a virtual host configuration file:

```bash
sudo nano /etc/nginx/sites-available/myapp.conf
```

Paste the generic template below, replacing `example.com`, `/path/to/project`, and the PHP-FPM socket path with the correct values for your server:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;

    # Point directly to the Laravel public directory
    root /path/to/project/public;
    index index.php index.html;

    charset utf-8;

    # Logging
    access_log /var/log/nginx/myapp_access.log;
    error_log  /var/log/nginx/myapp_error.log;

    # Upload size limit
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # Pass PHP scripts to FastCGI server
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        
        # Verify your installed PHP version socket path:
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files (.env, .git, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the configuration and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/myapp.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Apache Configuration

Ensure `mod_rewrite` and PHP-FPM / `mod_proxy_fcgi` are enabled:
```bash
sudo a2enmod rewrite proxy_fcgi setenvif
sudo a2enconf php8.4-fpm
```

Create an Apache VirtualHost file:
```apache
<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com
    ServerAdmin webmaster@example.com

    DocumentRoot /path/to/project/public

    <Directory /path/to/project/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/myapp_error.log
    CustomLog ${APACHE_LOG_DIR}/myapp_access.log combined
</VirtualHost>
```

Enable the site and reload Apache:
```bash
sudo a2ensite myapp.conf
sudo systemctl reload apache2
```

---

### HTTPS & SSL Setup

Obtain and configure a free Let's Encrypt SSL certificate using Certbot:

```bash
# For Nginx
sudo certbot --nginx -d example.com -d www.example.com

# For Apache
sudo certbot --apache -d example.com -d www.example.com
```

Once SSL is active, update your `.env` to use the `https://` protocol:
```env
APP_URL=https://example.com
```
Then clear the cached configuration:
```bash
php artisan config:cache
```

---

## Background Workers & Automation

### Queue Workers (Supervisor)

The application utilizes background queues for long-running operations. Supervisor monitors the worker process and restarts it if it crashes.

Install Supervisor on your system:
```bash
sudo apt-get install supervisor  # Ubuntu / Debian
# or
sudo dnf install supervisor      # RHEL / AlmaLinux
```

Create a new configuration file at `/etc/supervisor/conf.d/myapp-queue.conf`:

```ini
[program:myapp-queue]
process_name=%(program_name)s_%(process_num)02d
directory=/path/to/project
command=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
user=www-data
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/myapp-queue.log
stderr_logfile=/var/log/supervisor/myapp-queue-error.log
```

Update Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

---

### Task Scheduler

Laravel's task scheduler manages periodic tasks (such as database backup cleanups and automated backups defined in `routes/console.php`).

Add a cron entry for the web server user:
```bash
sudo crontab -u www-data -e
```

Add the following single line (replace `/path/to/project` with your project root):
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Alternatively, if you prefer running the scheduler under Supervisor (e.g. in development or containerized environments):

```ini
[program:myapp-schedule]
directory=/path/to/project
command=/usr/bin/php artisan schedule:work
user=www-data
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
stdout_logfile=/var/log/supervisor/myapp-schedule.log
stderr_logfile=/var/log/supervisor/myapp-schedule-error.log
```

---

## Frontend Assets Workflow

This project uses **Vite** with **Tailwind CSS v4**.

```bash
# 1. Install frontend packages
npm install

# 2. Local development with live Hot Module Replacement (HMR)
npm run dev

# 3. Compile and minify assets for production deployment
npm run build
```

> [!TIP]
> Always execute `npm run build` when deploying updates to a production server so that versioned CSS and JS bundles are stored in `public/build/`.

---

## Application Update Procedure

When updating the application on your server:

```bash
# 1. Navigate to project root
cd /path/to/project

# 2. Put application into maintenance mode
php artisan down

# 3. Pull latest code
git pull origin main

# 4. Install updated PHP and frontend dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 5. Compile production assets
npm run build

# 6. Run pending database migrations
php artisan migrate --force

# 7. Clear and optimize application caches
php artisan optimize:clear
php artisan optimize

# 8. Restart queue workers so new code takes effect
sudo supervisorctl restart myapp-queue:*

# 9. Bring application out of maintenance mode
php artisan up
```

---

## Testing & Code Quality

The repository includes full test suites, automated formatting, and static analysis tools.

```bash
# Run feature and unit tests
php artisan test

# Run tests using in-memory SQLite (no database server required)
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test

# Format code according to Laravel standards (Laravel Pint)
vendor/bin/pint

# Run static analysis (Larastan / PHPStan)
vendor/bin/phpstan analyse
```

---

## Project Directory Structure

```
.
├── app/
│   ├── Enums/                 # Application enums (Role, Permission, PostStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/Api/   # REST API Controllers (Sanctum-authenticated)
│   │   └── Middleware/        # ForceHttps, Security Headers, Proxies
│   ├── Livewire/              # Livewire 4 components (Dashboard, Posts, Services, Settings)
│   ├── Models/                # Eloquent models (Post, Service, Project, User, etc.)
│   └── Providers/             # Gate authorization definitions and system bootstrapping
├── bootstrap/
│   └── app.php                # Application middleware, exception handling, and routing
├── config/                    # Configuration files (database, queue, auth, backup, etc.)
├── database/
│   ├── factories/             # Model factories for testing
│   ├── migrations/            # Schema migration files
│   └── seeders/               # Database seeder classes (Roles, Permissions, Samples)
├── public/                    # Web root (index.php, static assets, storage symlink)
├── resources/
│   ├── css/                   # Tailwind CSS styling source
│   ├── js/                    # JavaScript entry points & WebAuthn Passkeys handlers
│   └── views/                 # Blade views and Livewire templates
├── routes/
│   ├── api.php                # RESTful API route definitions
│   ├── console.php            # Scheduled commands and Artisan tasks
│   ├── manage.php             # Admin CMS Livewire routes
│   ├── settings.php           # User profile, security, and appearance routes
│   └── web.php                # Main entry and authentication routes
├── storage/                   # File uploads, logs, framework sessions, and caches
└── tests/                     # Unit and Feature automated tests
```

---

## Troubleshooting

### 1. `500 Internal Server Error`
- Check the Laravel application log:
  ```bash
  tail -n 50 storage/logs/laravel.log
  ```
- Check the web server error log (`/var/log/nginx/error.log` or `/var/log/apache2/error.log`).

### 2. `Permission denied` on `storage` or `bootstrap/cache`
Ensure the web server user owns or has write permissions to these folders:
```bash
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

### 3. `No application encryption key has been specified`
Generate an application key in `.env`:
```bash
php artisan key:generate
```

### 4. Database Connection Refused / Fatal Error
- Ensure the database service is running:
  ```bash
  sudo systemctl status postgresql # or mysql
  ```
- Verify credentials in `.env` match your actual database user, password, and port.
- Test connectivity from the CLI:
  ```bash
  PGPASSWORD='your_password' psql -h 127.0.0.1 -U myapp_user -d myapp_db
  ```

### 5. Infinite Redirect Loops with HTTPS
If your application sits behind a reverse proxy, SSL termination proxy, or Cloudflare, ensure Laravel trusts upstream proxies. This project pre-configures `$middleware->trustProxies(at: '*')` in `bootstrap/app.php`. Make sure your proxy forwards the `X-Forwarded-Proto` header.

### 6. Vite / CSS / JS Not Loading (404 on Assets)
Run the production build command and verify assets exist in `public/build/`:
```bash
npm run build
ls -la public/build
```

### 7. Queue Jobs Not Running
- Verify Supervisor is active:
  ```bash
  sudo supervisorctl status
  ```
- Check worker log output:
  ```bash
  tail -f /var/log/supervisor/myapp-queue.log
  ```

---

## Security Best Practices

- **Never commit `.env`**: Ensure `.env` remains in `.gitignore`.
- **Set `APP_DEBUG=false` in Production**: Leaving debug enabled exposes database credentials, API keys, and stack traces to visitors.
- **Protect Sensitive Files**: Ensure your web server denies requests to `.env`, `.git`, and any configuration files.
- **Enforce Strong Credentials**: Use cryptographically generated passwords for your database and Redis instances.
- **Run Behind HTTPS**: Use Let's Encrypt or an authorized SSL provider to protect session tokens, login credentials, and API communication.
- **Restrict File Permissions**: Avoid `chmod 777`. Keep file ownership restricted to the application user and web server group.

---

## Contributing

Contributions are welcome! To contribute to this project:

1. Fork the repository on GitHub.
2. Create a dedicated feature branch:
   ```bash
   git checkout -b feature/my-new-feature
   ```
3. Ensure your changes follow coding standards:
   ```bash
   vendor/bin/pint
   vendor/bin/phpstan analyse
   php artisan test
   ```
4. Commit your changes with clear, descriptive commit messages.
5. Push to your branch and open a Pull Request explaining the rationale and changes.

---

## Support

If you encounter bugs, issues, or have feature requests, please submit an issue via the [GitHub Issue Tracker](https://github.com/Alfred336/seal/issues).

---

## License

This project does not currently declare an open-source license. All rights are reserved by the repository owner. 

For inquiries regarding permissions, licensing, or commercial usage, please contact the maintainer or repository owner.
