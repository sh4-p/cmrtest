# CRM System

A modern, full-featured Customer Relationship Management system built with Laravel 12, Inertia.js, Vue 3, and Tailwind CSS.

## Features

### Core CRM Functionality
- **Lead Management** - Track and manage sales leads through the pipeline
- **Contact Management** - Maintain detailed contact information
- **Company Management** - Organize contacts by company
- **Deal Management** - Track deals through customizable stages
- **Task Management** - Assign and track tasks with priorities and due dates
- **Activity Tracking** - Log calls, emails, meetings, and other interactions

### User Management & Permissions
- **Role-Based Access Control** - Admin, Manager, and Sales Rep roles
- **Granular Permissions** - Fine-grained control over CRUD operations
- **User Assignment** - Assign leads, deals, and tasks to specific users
- **Ownership Filtering** - Users see only their own data or all data based on permissions

### User Interface
- **Real-Time Dashboard** - KPI cards with trend indicators
- **Global Search** - Search across all entities from anywhere
- **Responsive Design** - Mobile-friendly Tailwind CSS interface
- **SPA Experience** - Seamless navigation with Inertia.js
- **Interactive Tables** - Sortable, filterable data tables

### Technical Features
- **RESTful API** - Complete API with Sanctum authentication
- **Form Validation** - Server-side and client-side validation
- **Policy-Based Authorization** - Laravel policies for all entities
- **Database Agnostic** - Works with SQLite, MySQL, PostgreSQL
- **Demo Data** - Comprehensive seeder for testing

## Requirements

- PHP 8.4+
- Composer
- Node.js 18+ and npm
- SQLite, MySQL 8+, or PostgreSQL 14+

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd cmrtest
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` and set your database connection:

**For Development (SQLite):**
```env
DB_CONNECTION=sqlite
```

**For Production (MySQL):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_database
DB_USERNAME=crm_user
DB_PASSWORD=secure_password
```

### 5. Run Migrations

```bash
# Create database tables
php artisan migrate

# Seed with demo data (optional)
php artisan db:seed --class=DemoDataSeeder
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start the Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 🐳 Docker Installation (Recommended)

The easiest way to run the CRM system is using Docker. All dependencies (PHP, Node.js, MySQL, Redis) are included.

### Quick Start with Docker

```bash
# 1. Clone the repository
git clone <repository-url>
cd cmrtest

# 2. Start development environment (one command!)
make start

# That's it! 🎉
# Application: http://localhost:8080
# Login: admin@crm.test / password
```

### Manual Docker Setup

```bash
# Copy Docker environment
cp .env.docker .env

# Start services with Vite HMR
docker-compose --profile dev up -d

# Install dependencies and setup
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```

### Available Make Commands

```bash
make help          # Show all available commands
make dev           # Start development with HMR
make prod          # Start production environment
make install       # Initial setup (deps + migrate + seed)
make migrate       # Run migrations
make fresh         # Fresh database with seed data
make test          # Run tests
make shell         # Access container shell
make db-backup     # Backup database
make clean         # Clean cache and volumes
```

### Docker Services

| Service | Port | Description |
|---------|------|-------------|
| Nginx | 8080 | Web server |
| PHP-FPM | 9000 | Application (PHP 8.4) |
| MySQL | 3306 | Database |
| Redis | 6379 | Cache & Queue |
| Vite | 5173 | Dev server (dev profile) |

For detailed Docker documentation, see [docker/DOCKER_GUIDE.md](docker/DOCKER_GUIDE.md)

## Demo Users

After running the DemoDataSeeder, you can log in with:

| Email | Password | Role |
|-------|----------|------|
| admin@crm.test | password | Admin |
| manager@crm.test | password | Manager |
| sales@crm.test | password | Sales Rep |

## Permissions

### Admin Role
- Full access to all features
- Can delete any record
- Can manage all entities

### Manager Role
- View and edit all records
- Cannot delete records owned by others
- Can assign leads and manage stages

### Sales Rep Role
- View and edit own records only
- Cannot view other users' data
- Can create new records

## API Documentation

### Authentication

All API endpoints require Sanctum authentication:

```bash
# Login to get token
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}
```

### Available Endpoints

```
GET    /api/leads              # List leads
POST   /api/leads              # Create lead
GET    /api/leads/{id}         # Show lead
PUT    /api/leads/{id}         # Update lead
DELETE /api/leads/{id}         # Delete lead
POST   /api/leads/{id}/convert # Convert lead to contact

GET    /api/contacts           # List contacts
GET    /api/companies          # List companies
GET    /api/deals              # List deals
GET    /api/tasks              # List tasks
GET    /api/activities         # List activities

GET    /api/dashboard/stats    # Dashboard statistics
GET    /api/search?q={query}   # Global search
```

## Production Deployment

### 1. Environment Setup

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use MySQL or PostgreSQL
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-secure-password

# Configure mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### 2. Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Build assets for production
npm run build
```

### 3. Set Permissions

```bash
# Storage and cache directories
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --class=DemoDataSeeder
```

### 5. Configure Web Server

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/cmrtest/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Technology Stack

- **Backend:** Laravel 12.39.0 (PHP 8.4)
- **Frontend:** Vue.js 3 (Composition API)
- **SPA Framework:** Inertia.js 2.0
- **Styling:** Tailwind CSS
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel Permission
- **Icons:** Heroicons
- **Database:** SQLite / MySQL / PostgreSQL

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # API controllers
│   │   │   └── Web/          # Inertia controllers
│   │   └── Resources/        # API resources
│   ├── Models/               # Eloquent models
│   └── Policies/             # Authorization policies
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/       # Vue components
│   │   ├── Composables/      # Vue composables
│   │   ├── Layouts/          # Page layouts
│   │   └── Pages/            # Inertia pages
│   └── views/                # Blade views
└── routes/
    ├── api.php               # API routes
    └── web.php               # Web routes
```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
# PHP (Laravel Pint)
./vendor/bin/pint

# JavaScript (ESLint)
npm run lint
```

### Watch Assets

```bash
npm run dev
```

## Troubleshooting

### Database Issues

```bash
# Reset database
php artisan migrate:fresh --seed
```

### Clear Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission Denied

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues and questions, please open an issue on the GitHub repository.
