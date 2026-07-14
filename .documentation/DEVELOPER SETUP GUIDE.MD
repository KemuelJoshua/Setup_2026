# Developer Setup Guide

## Tech Stack

- Laravel 13
- PHP 8.3+
- Vue.js 3
- Inertia.js
- Vite
- Tailwind CSS
- MySQL 8+
- Node.js 22+
- npm 10+

## Requirements

Install these tools before setting up the project.

| Software | Required Version |
|----------|------------------|
| PHP | 8.3 or higher |
| Composer | 2.10.2 or higher |
| Node.js | 22.23.1 LTS or higher |
| npm | 10.9.8 or higher |
| MySQL | 8.0 or higher |
| Git | Latest stable version |

Verify the installed versions.

```bash
php -v
composer -V
node -v
npm -v
mysql --version
git --version
```

## Installation

Clone the repository and enter the project directory.

```bash
git clone https://github.com/KemuelJoshua/Setup_2026.git
cd Setup_2026
```

Install PHP dependencies.

```bash
composer install
```

Install JavaScript dependencies.

```bash
npm install
```

## Environment Setup

Create the local environment file.

Linux and macOS:

```bash
cp .env.example .env
```

Windows:

```cmd
copy .env.example .env
```

Generate the application key.

```bash
php artisan key:generate
```

Update `.env` with your local settings.

```env
APP_NAME=ProjectName
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_database
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

## Database Setup

Create a MySQL database.

```text
project_database
```

Run the migrations and seeders.

```bash
php artisan migrate --seed
```

Create the storage symlink.

```bash
php artisan storage:link
```

## Running the Application

Start the Laravel server.

```bash
php artisan serve
```

Open the application.

```text
http://127.0.0.1:8000
```

Start the Vite development server in a second terminal.

```bash
npm run dev
```

Start the queue worker in a third terminal.

```bash
php artisan queue:work
```

Start the scheduler in a fourth terminal.

```bash
php artisan schedule:work
```

## Development Workflow

Use these terminals during development.

```text
Terminal 1: php artisan serve
Terminal 2: npm run dev
Terminal 3: php artisan queue:work
Terminal 4: php artisan schedule:work
```

## Running Tests

Run the test suite.

```bash
php artisan test
```

Run Pest directly.

```bash
vendor/bin/pest
```

## Production Build

Install optimized PHP dependencies.

```bash
composer install --no-dev --optimize-autoloader
```

Install JavaScript dependencies and build frontend assets.

```bash
npm install
npm run build
```

Cache Laravel configuration, routes, events, and views.

```bash
php artisan optimize
```

Run production migrations.

```bash
php artisan migrate --force
```

## Troubleshooting

Regenerate the application key.

```bash
php artisan key:generate
```

Clear cached configuration.

```bash
php artisan optimize:clear
```

Recreate the storage symlink.

```bash
php artisan storage:link
```

Restart the Vite development server.

```bash
npm run dev
```

Regenerate Composer autoload files.

```bash
composer dump-autoload
```

Fix Linux storage permissions.

```bash
chmod -R 775 storage bootstrap/cache
```

## Contributing

Create a feature branch.

```bash
git checkout -b feature/your-feature-name
```

Commit with a clear message.

```bash
git commit -m "feat: add student dashboard"
git commit -m "fix: resolve login validation issue"
git commit -m "refactor: simplify role permissions"
```

Push the branch.

```bash
git push origin feature/your-feature-name
```

Open a pull request for review.

## Project Structure

```text
app/
bootstrap/
config/
database/
public/
resources/
    css/
    js/
routes/
storage/
tests/
vendor/

composer.json
package.json
vite.config.ts
```

## License

This project is proprietary and intended for authorized development use only.
