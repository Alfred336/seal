# sealCMS — SealTech Content Management System & API

`sealCMS` is the central content management system and RESTful API backend powering the SealTech web platform. Built with **Laravel 13**, **Livewire 4**, **PostgreSQL**, and **Redis**, it provides a reactive management dashboard along with a secured API for headless frontend applications.

---

## Architecture Overview

- **Admin CMS Dashboard**: Built using Laravel Blade, Livewire 4, Flux UI components, and Tailwind CSS v4 for reactive, full-featured administrative operations.
- **Headless REST API**: Provides token-authenticated (Sanctum) endpoints serving the public frontend (posts, services, projects, careers, contact inquiries, newsletter subscriptions).
- **Background Processing**: Redis-backed queues and schedulers managed via Supervisor for backup routines, email dispatches, and asynchronous jobs.

---

## Core Features

- **Blog & Taxonomy Engine**: Complete post creation, editing, category/tag tagging, and publishing workflows.
- **Content & Showcase Management**: Manage services, project case studies, and career openings.
- **Lead & Inquiry Pipeline**: Ingest and manage contact form submissions, scheduled call requests, and project consultation requests.
- **Newsletter Engine**: Track subscriber lists with secure, signed unsubscribe capabilities.
- **Enterprise Authentication & RBAC**:
  - Spatie Role-Based Access Control (`admin`, `editor`, `author`, `support`).
  - Laravel Fortify authentication with Two-Factor Authentication (2FA).
  - WebAuthn Passkey support via `@laravel/passkeys`.
- **Automated Maintenance & Backups**: Built-in scheduled database and application backup routines.

---

## Technology Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 13.x (PHP 8.4+) |
| **Database** | PostgreSQL 16+ (SQLite supported for testing) |
| **Cache & Queues** | Redis (via `predis`) |
| **Reactive UI** | Livewire 4.x + Flux UI |
| **Styling & Assets** | Tailwind CSS v4 + Vite 8 |
| **Authentication** | Laravel Fortify + Laravel Sanctum + Laravel Passkeys |
| **Authorization** | Spatie Laravel-Permission |
| **Process Manager** | Supervisor |

---

## System Requirements

- **PHP**: `^8.4` (Extensions: `bcmath`, `curl`, `mbstring`, `openssl`, `pdo_pgsql`, `redis`, `xml`, `zip`)
- **PostgreSQL**: `^15` or `^16`
- **Redis**: `^7.0`
- **Node.js**: `^20.x` or `^22.x` and `npm`
- **Composer**: `^2.x`

---

## Installation & Setup

### 1. Clone Repository & Install Dependencies

```bash
git clone <repository-url> seal
cd seal

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install
```

### 2. Environment Configuration

Copy the example environment file and configure your credentials:

```bash
cp .env.example .env
```

Ensure the following core variables are configured in `.env`:

```env
APP_NAME=sealCMS
APP_ENV=local
APP_DEBUG=true
APP_URL=https://devcms.sealtech.co.tz

# PostgreSQL Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dev_sealtech
DB_USERNAME=admin
DB_PASSWORD=your_secure_password

# Redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 3. Generate Application Key & Link Storage

```bash
php artisan key:generate
php artisan storage:link
```

### 4. Database Setup & Seeding

Run migrations and seed default roles, permissions, and initial sample content:

```bash
# Run migrations
php artisan migrate

# Seed roles, permissions, admin user, and sample data
php artisan db:seed
```

#### Default Admin Credentials (from Seeder)
- **URL**: `/login`
- **Email**: `admin@sealtech.test`
- **Password**: `password`

### 5. Build Frontend Assets

```bash
# Production asset compilation
npm run build

# Or live hot-reload development
npm run dev
```

---

## Background Workers (Supervisor)

To handle queued jobs (e.g. notifications, mail delivery) and scheduled commands (e.g. daily database backups), Supervisor configuration templates are included in `supervisor/`:

- `supervisor/dev-seal-queue.conf` — Artisan queue worker
- `supervisor/dev-seal-schedule.conf` — Artisan schedule worker

### Quick Installation

Run the installation script with root privileges:

```bash
sudo supervisor/install.sh
```

### Manual Configuration

```bash
# Copy configurations to supervisor directory
sudo cp supervisor/*.conf /etc/supervisor/conf.d/

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Check worker statuses
sudo supervisorctl status dev-seal-queue dev-seal-schedule
```

---

## Web Server Configuration (Nginx)

Example Nginx server block for hosting under PHP 8.4 FPM:

```nginx
server {
    server_name devcms.sealtech.co.tz;

    root /var/www/dev/backend/seal/public;
    index index.php index.html;

    charset utf-8;

    access_log /var/log/nginx/devcms-access.log;
    error_log  /var/log/nginx/devcms-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;

    listen 80;
    listen [::]:80;
}
```

Enable SSL via Certbot:
```bash
sudo certbot --nginx -d devcms.sealtech.co.tz
```

---

## Directory Structure

```
seal/
├── app/
│   ├── Enums/          # Application enums (Roles, Permissions, PostStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/Api/   # REST API Controllers (Sanctum)
│   │   └── Middleware/        # HTTPS forcing, security middleware
│   ├── Livewire/       # Interactive admin components (Posts, Services, Inquiries, etc.)
│   └── Models/         # Eloquent models (Post, Service, Project, User, etc.)
├── config/             # Framework & package configurations
├── database/
│   ├── factories/      # Model factories for testing and seeding
│   ├── migrations/     # Database schemas
│   └── seeders/        # RolePermissionSeeder & SampleDataSeeder
├── public/             # Entry point (index.php) and compiled Vite assets
├── resources/
│   ├── css/            # Tailwind CSS source files
│   ├── js/             # JavaScript entry points & passkeys handler
│   └── views/          # Blade views & Livewire components
├── routes/
│   ├── api.php         # Public & authenticated API routes
│   ├── manage.php      # Admin dashboard Livewire routes
│   ├── settings.php    # User security, profile, & appearance settings
│   └── web.php         # Authentication & base web routing
├── supervisor/         # Supervisor process configurations & installation helper
└── tests/              # Feature and Unit test suites
```

---

## Testing & Quality Assurance

```bash
# Run test suite
php artisan test

# Run tests with in-memory SQLite
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test

# Code style formatting (Laravel Pint)
vendor/bin/pint

# Static type analysis (PHPStan)
vendor/bin/phpstan analyse
```

---

## License

Proprietary software. All rights reserved by SealTech.
