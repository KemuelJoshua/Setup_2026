# Sims

Sims is a multi-school management platform that gives each school its own isolated workspace. A central platform administrator provisions schools, while each school manages its users, access rules, and academic structure from its own domain.

The current release focuses on the administrative foundation needed before teaching and learning activities can be introduced.

## Requirements

Install the following before setting up Sims locally:

- PHP 8.3 or later, with Composer 2;
- the PHP extensions required by Laravel and Composer, including PDO MySQL;
- Node.js 22 LTS or later, with npm;
- MySQL 8.0 or later;
- Docker Desktop or Docker Engine; and
- Git.

The following local ports must be available:

| Port   | Used by                 |
| ------ | ----------------------- |
| `8000` | Laravel application     |
| `5173` | Vite development server |
| `3306` | MySQL database          |
| `6379` | Redis Docker container  |

Sims uses MySQL for the central and school databases during local development. Redis runs in Docker and is used for the application cache, sessions, and queued jobs.

## Local installation with Docker Redis

Run all project commands from the repository's root directory.

### 1. Install the application dependencies

```powershell
composer install
npm install
```

You can confirm that PHP and its required extensions satisfy the project requirements with:

```powershell
composer check-platform-reqs
```

### 2. Create the local environment file

```powershell
Copy-Item .env.example .env
```

Update these values in `.env`:

```dotenv
APP_NAME="Sims"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
CENTRAL_DOMAIN=localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sims
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

Use `REDIS_CLIENT=predis` because the Predis client is already included with the project. A separate PHP Redis extension is not required for this local setup.

Replace the database username and password with the credentials for your local MySQL installation.

### 3. Create the MySQL database

Create the central `sims` database using the MySQL command-line client:

```powershell
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS sims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Enter the MySQL root password when prompted. If the MySQL account configured in `.env` is not `root`, make sure that account can access the `sims` database.

Sims creates a separate MySQL database whenever a school workspace is provisioned and removes that database when the school is permanently deleted. The configured MySQL account must therefore have permission to create and drop databases in the local development environment.

### 4. Start Redis in Docker

Create a persistent Docker volume, then start a Redis container:

```powershell
docker volume create sims-redis-data
docker run --name sims-redis --detach --restart unless-stopped --publish 127.0.0.1:6379:6379 --volume sims-redis-data:/data redis:7-alpine redis-server --appendonly yes
```

Confirm that Redis is ready:

```powershell
docker exec sims-redis redis-cli ping
```

The expected response is `PONG`.

The container only needs to be created once. On later sessions, start the existing container with:

```powershell
docker start sims-redis
```

### 5. Initialize the application

Generate the application key, clear any cached configuration, and prepare the central database:

```powershell
php artisan key:generate --no-interaction
php artisan optimize:clear --no-interaction
php artisan migrate --seed --no-interaction
```

The database seeder creates this central administrator account for local development:

```text
Email: admin@gmail.com
Password: password
```

Change this password if the environment is shared with anyone else.

### 6. Run Sims locally

Start the Laravel server, Redis-backed queue listener, application log viewer, and Vite development server together:

```powershell
composer run dev
```

Open the central workspace at [http://localhost:8000](http://localhost:8000) and sign in with the seeded administrator account.

When a school is created, enter a local domain such as `school-a.localhost`. Its workspace will then be available at `http://school-a.localhost:8000`. Modern browsers resolve `*.localhost` domains to the local computer without a hosts-file entry.

## Daily local startup

After the first installation, the normal startup process is:

```powershell
docker start sims-redis
composer run dev
```

Stop the application with `Ctrl+C`. Stop Redis when it is no longer needed:

```powershell
docker stop sims-redis
```

## Local troubleshooting

- **Redis connection refused** — confirm Docker is running, then run `docker start sims-redis` and `docker exec sims-redis redis-cli ping`.
- **Port 6379 is already in use** — stop the other local Redis service or use a different host port and update `REDIS_PORT` in `.env` to match.
- **MySQL connection refused** — confirm MySQL is running and that `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` match the local MySQL installation.
- **Creating a school fails with a database error** — make sure the configured MySQL user is allowed to create and drop databases.
- **Configuration changes are ignored** — run `php artisan optimize:clear --no-interaction`, then restart `composer run dev`.
- **The page has no styling or does not refresh** — confirm the Vite process started successfully as part of `composer run dev`.
- **A school domain does not open** — use a domain ending in `.localhost`, keep `CENTRAL_DOMAIN=localhost`, and include port `8000` in the browser address.
- **Queued work is not processed** — confirm the `queue` process is still running in the output from `composer run dev`.

## Purpose

Sims is designed to help an organization operate several schools from one platform without mixing their records. It provides a consistent way to:

- create and maintain school workspaces;
- control whether a school can access the platform;
- manage school users, roles, and permissions;
- define the school calendar and academic structure; and
- build curricula from reusable academic records.

## People and access

### Platform administrator

The platform administrator works in the central Sims workspace. This user can create schools, maintain school profiles and domains, activate or deactivate access, and remove inactive schools.

Creating a school also creates its isolated workspace and first administrator account.

### School administrator

The School Admin role manages one school workspace. By default, this role receives all available school administration permissions, including user, role, school-year, academic-structure, and curriculum management.

### Teachers and students

Teacher and Student roles are available as starting roles. Their dedicated classroom and learning experiences are not part of the current administrative release.

Custom roles can also be created by combining the permissions appropriate to a school staff member's responsibilities.

## Workspace model

Sims has two distinct areas:

| Area              | Purpose                                                                              |
| ----------------- | ------------------------------------------------------------------------------------ |
| Central workspace | Manages schools, their domains, profiles, initial administrators, and access status. |
| School workspace  | Manages the users and academic records belonging to one school.                      |

Each school uses its own domain and isolated data store. A user signed in to one school works only with that school's records. Deactivating a school blocks access to its workspace without immediately deleting its information.

## Available capabilities

### School administration

- Create a school with its code, name, domain, contact details, profile information, and initial administrator.
- Search the school directory by name, code, domain, or email.
- Update a school's profile and domain.
- Activate or deactivate a school workspace.
- Delete a school after it has been deactivated. Deletion permanently removes the school workspace and its data.

### User and access management

- Create, update, search, and remove school user accounts.
- Assign roles to users.
- Create and maintain roles.
- Grant role permissions for viewing, creating, updating, deleting, and changing the status of supported records.
- Protect administrative actions according to the signed-in user's permissions.

### Academic setup

- **School years** — record a school-year code, name, start date, end date, and status. Statuses are Planned, Active, and Closed.
- **Educational levels** — organize the academic catalog into levels used by the school.
- **Academic structures** — define Semester, Quarterly, Trisem, or custom Term structures and their academic periods.
- **Grade levels** — maintain the year or grade levels offered under an educational level.
- **Sections** — maintain the school's section catalog by educational level.
- **Subjects** — maintain reusable subjects by educational level.
- **Programs** — maintain active or inactive programs by educational level.
- **Curricula** — combine a school year, educational level, program, academic structure, grade levels, academic periods, and subjects into a curriculum.

The main academic relationship is:

```text
School workspace
├── School years
├── Educational levels
│   ├── Academic structures and periods
│   ├── Grade levels
│   ├── Sections
│   ├── Subjects
│   └── Programs
└── Curricula
    └── Subjects assigned to grade levels and academic periods
```

### Account and security

- Sign in and sign out securely.
- Reset a forgotten password.
- Verify an email address.
- Confirm a password before accessing sensitive settings.
- Change the account password.
- Enable two-factor authentication and use recovery codes.
- Update profile information.
- Choose light, dark, or system appearance.

## Recommended administrative workflow

1. The platform administrator creates and activates a school workspace.
2. The school's first administrator signs in through the assigned school domain.
3. The school administrator reviews roles and permissions, then creates user accounts.
4. The administrator creates the school year and marks it Planned or Active as appropriate.
5. Educational levels and academic term structures are defined.
6. Grade levels, sections, subjects, and programs are added to the academic catalog.
7. A curriculum is created and subjects are assigned to the appropriate grade levels and academic periods.
8. Records and access permissions are maintained as the school's needs change.

## Important operating notes

- School records are isolated from other schools on the platform.
- School access status is controlled centrally.
- A school must be deactivated before it can be deleted.
- Deleting a school is permanent and removes its isolated workspace data.
- Access to school administration features depends on the permissions assigned to the user's role.
- Academic records should be created in dependency order; for example, educational levels and term structures must exist before they can be used in a curriculum.

## Current product scope

Sims currently provides the multi-school, identity, access-control, and academic-configuration foundation of a learning management system.

Course content delivery, assignments, assessments, grading, progress tracking, classroom communication, and dedicated teacher or student workspaces are product-direction features and are not yet available as complete operational modules. The dashboard and notification page are also still being developed.
