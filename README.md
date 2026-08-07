# ServerAvatar Watchtower

A standalone SaaS platform inspired by Laravel Nightwatch.

## Requirements

- PHP 8.4+
- MySQL 8.0+
- Composer 2.x
- Node.js 20+
- NPM 10+

## Installation

```bash
# Clone the repository
git clone git@github.com:patil-jayshree/serveravatar-watchtower.git
cd serveravatar-watchtower

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Configure .env with your database credentials:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=watchtower
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

## Tech Stack

- **Framework:** Laravel 13
- **PHP:** 8.4+
- **Database:** MySQL
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Build Tool:** Vite
- **API:** Laravel Sanctum
- **Code Style:** Laravel Pint (PSR-12)

## Folder Structure

```
app/
├── Actions/          # Action classes for business logic
├── DTOs/             # Data Transfer Objects
├── Enums/            # PHP Enums
├── Events/           # Laravel Events
├── Exceptions/       # Custom Exceptions
├── Helpers/           # Helper functions
├── Http/
│   ├── Controllers/  # Application controllers
│   ├── Middleware/   # HTTP Middleware
│   ├── Requests/     # Form Request validation
│   └── Resources/    # API Resources
├── Jobs/             # Queue Jobs
├── Listeners/        # Event Listeners
├── Models/           # Eloquent Models
├── Notifications/    # Laravel Notifications
├── Observers/        # Model Observers
├── Policies/         # Authorization Policies
├── Providers/        # Service Providers
├── Services/         # Service classes
├── Support/          # Support classes
└── Traits/           # Reusable Traits
```

## Coding Standards

This project follows **PSR-12** coding standards and uses **Laravel Pint** for code formatting.

```bash
# Format all code
./vendor/bin/pint

# Format specific files
./vendor/bin/pint app/Http/Controllers
```

### Guidelines

- Use **strict typing** where appropriate
- Use **Constructor Dependency Injection**
- Keep controllers **thin** — business logic goes in Actions/Services
- Use **Form Request validation**
- Use **Policies** for authorization
- Use **Enums** instead of magic strings
- Use **DTOs** for data transfer

## Configuration

Watchtower-specific configuration is in `config/watchtower.php`:

```php
'application_name' => env('APP_NAME', 'ServerAvatar Watchtower'),
'version' => '1.0.0',
'api_prefix' => 'api/v1',
'default_theme' => 'light',
'support_email' => env('SUPPORT_EMAIL', 'support@serveravatar.com'),
```

## Database Tables

Laravel infrastructure tables created by default migrations:
- `users`
- `cache` (cache management)
- `jobs` (queue jobs)
- `personal_access_tokens` (Sanctum API tokens)

## Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## License

Proprietary — ServerAvatar.
