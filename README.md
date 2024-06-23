<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# REST API Sistem Pengajuan Izin

## Installation Steps

1. **Clone the project repository:**
   ```bash
   git clone https://github.com/Eef-M/REST-API-Sistem-Pengajuan-Izin.git
   ```
2. **Navigate to the project directory:**
    ```bash
    cd REST-API-Sistem-Pengajuan-Izin
    ```
3. **Laravel Setup:**
    ```bash
    composer install
    ```
4. **Create a copy of the .env file:**
    ```bash
    cp .env.example .env
    ```
5. **Set env. in this project using MySQL:**
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pengajuan_izin
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6. **Generate an application key:**
    ```bash
    php artisan key:generate
    ```
7. **Run migrations and seed the database (if needed):**
    ```bash
    php artisan migrate --seed
    ```
8. **Run application/server**
    ```bash
    php artisan serve
    ```

## Postman Collection
[View Postman Collection](https://documenter.getpostman.com/view/33972671/2sA3XWcyrt) for documentation of the API.

Created By Eef Mekelliano