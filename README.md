# Hospital ADT System

An Admission-Discharge-Transfer (ADT) System built with **Laravel 12** and **FilamentPHP 4**.  

## Features
- Manage wards, teams, and team members  
- Admit, transfer, and discharge patients  
- Record “who treated” entries
- Real-time validation (ward capacity, ward type, assigned team)  
- Built-in RBAC
- Audit logging of actions  

## Tech Stack
- **Laravel 12**
- **FilamentPHP 4**

## Requirements
- PHP 8.2+
- Composer

## Installation
```bash
git clone https://github.com/ahmed-mahran-villacollege/hostpital-adt-system.git
cd hostpial-adt-system
composer install
cp .env.example .env
php artisan key:generate
# Update DB settings in .env
php artisan migrate --seed
php artisan serve
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
