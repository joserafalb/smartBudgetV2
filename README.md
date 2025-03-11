# Laravel Project with Sail

## 🚀 Introduction
This project is a Laravel application designed to run with **Laravel Sail**, a lightweight command-line interface for managing a local Docker-based development environment.

## 📋 Prerequisites
Make sure you have the following installed on your system:

- **Docker** ([Install Docker](https://docs.docker.com/get-docker/))
- **Docker Compose** (Included with Docker Desktop)
- **Git** ([Install Git](https://git-scm.com/))

## 📥 Installation

1. **Clone the repository:**
   ```
   git clone git@github.com:joserafalb/smartBudgetV2.git
   cd smartBudgetV2
   ```
2. **Copy the environment file and update settings if needed:**
    ```
    cp .env.example .env
    ```
3. **Install Sail**

    - If composer is not installed:
    ```
    docker run --rm -v $(pwd):/app -w /app laravelsail/php81-composer:latest composer require laravel/sail --dev --ignore-platform-reqs
    ```
    - If composer is installed
    ```
    composer require laravel/sail --dev --ignore-platform-reqs
    ```
4. **Start Laravel Sail (first-time setup may take a while):**
    ```
    ./vendor/bin/sail up -d
    ```
5. **Run Composer:**
    ```
    ./vendor/bin/sail composer install
    ```
6. **Run NPM:**
    ```
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run development|production|watch
    ```
## 🏃 Running the Project
- **Start Laravel Sail:**
    ```
    ./vendor/bin/sail up
    ```
- **Stop the environment:**

    ```
    ./vendor/bin/sail down
    ```

- **Access the application in the browser:**
    ```
    http://localhost
    ```

- **Run Artisan commands:**
    ```
    ./vendor/bin/sail artisan <command>
    ```

- **Run Composer commands:**
    ```
    ./vendor/bin/sail composer <command>
    ```
- **Run NPM commands:**
    ```
    ./vendor/bin/sail npm <command>
    ```
## 🐳 Managing Docker Containers
- **To check running containers:**
    ```
    ./vendor/bin/sail ps
    ```
- **To stop and remove all containers:**
    ```
    ./vendor/bin/sail down --volumes
    ```
## 🎯 Additional Configuration

- **Queue workers: If using Laravel queues, start a worker:**
    ```
    ./vendor/bin/sail artisan queue:work
    ```
- **Database access: You can connect to the database via:**
    ```
    ./vendor/bin/sail mysql
    ```

- **Laravel Scheduler: To run scheduled tasks, add this to your cron jobs:**
    ```
    * * * * * cd /path-to-project && ./vendor/bin/sail artisan schedule:run >> /dev/null 2>&1
    ```

