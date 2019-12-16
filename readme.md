## Introduction
A simple Todo API CRUD

### Installation

- Clone this repository
- Copy .env.example to .env 
    - `cp .env.example .env`
- Install dependencies 
    - `composer install`
- Generate JWT secret
    - `php artisan jwt:secret`    
- Generate swagger 
    - `php artisan swagger-lume:publish-views`
    - `php artisan swagger-lume:generate`
- Create database `todo`    
- Run the migration 
    - `php artisan migrate`

### API Documentation
`{url}/api/documentation`
