# Laravel User API

This project is a **Laravel RESTful API** built with **Sanctum** for token-based authentication and **Spatie Laravel Permission** for managing user roles and permissions.

The API provides a secure way to register, login, manage user profiles, and control access based on roles.

---

## 🚀 Features

- User Registration and Login
- Token Authentication with Laravel Sanctum
- Role and Permission Management with Spatie
- RESTful Endpoints for Users
- Auto-generated API Documentation with Scramble
---

## 📚 API Documentation

You can view the full API documentation here:

👉 [API Documentation](http://127.0.0.1:8000/docs/v1/api#/)

This documentation is automatically generated and updated based on your code using **Scramble**.

---

## Installation

Follow these steps to set up the project:

1. Clone the repository:

```bash
git clone https://your-repository-link.git
cd your-project-folder
```
## Install Dependencies

```bash
composer install
npm install && npm run dev
```

## Set up your Environment File

```bash
cp .env.example .env
php artisan key:generate
```

## Configure your .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=db_username
DB_PASSWORD=db_password
```

## Run Database Migrations
```
php artisan migrate
php artisan db:seed
```

## Start the Development Server
`php artisan serve
`

## License 📄
This project is open-source and available under the [MIT License](LICENSE.md)

Made with **JMRX** using Laravel.
