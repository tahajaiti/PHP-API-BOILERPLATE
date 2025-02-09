# PHP API Boilerplate

## Overview
A lightweight, efficient PHP boilerplate that provides a well-structured foundation for building RESTful APIs. It implements clean architecture across models, controllers, services, repositories, a router, and exception handlers, enabling scalability and maintainability.

## Features
- **Dynamic Routing System**: Custom-built for flexible API request handling.
- **Separation of Concerns**: Modular MVC structure including Models, Controllers, and Services.
- **Abstraction Layer**: Repositories for data management.
- **Middleware**: Simplifies tasks like authentication, validation, and logging.
- **Centralized Exception Handler**: Ensures consistent and predictable error responses.
- **Request & Response Standardization**:
  - Input validation and sanitization.
  - Structured API responses.
- **JSON Web Token (JWT) Integration**: Robust authentication via Firebase JWT library.
- **Environment Configuration**: Supports development, staging, and production setups.
- **Dependency Management**: Composer integration for streamlined package usage.

## Directory Structure
```php
│── app/                 # Main application directory
│   ├── Controller/      # Manages HTTP requests & responses
│   ├── Core/            # Core system components (router, request, etc.)
│   ├── Handler/         # Error & exception handling
│   ├── Helpers/         # Reusable utilities
│   ├── Middleware/      # Scripts for pre/post-request processing
│   ├── Model/           # Data layers & database entities
│   ├── Repository/      # Abstracted database interactions
│   ├── Routes/          # Definitions of API routes
│   ├── Service/         # Business logic implementations
│
├── vendor/              # Composer-managed dependencies
├── .htaccess            # URL rewriting configuration
├── composer.json        # Dependency manager configuration
├── composer.lock        # Dependency version locks
├── config.php           # Settings (database, environment variables)
├── index.php            # Application entry point
```

## Usage
1. Define API endpoints in `app/Routes/`.
2. Define database Entities in `app/Models`.
3. Implement endpoint logic in `app/Controller/`.
4. Write application-specific logic in `app/Service/`.
5. Add `app/Middleware/` for tasks like authentication and logging.
6. Use `app/Repository/` for database operations.
7. Update `config.php` for environment variables (e.g., production).

## Contributing
I encourage contributions. Feel free to open pull requests or issues to suggest improvements.

## License
I really don't care, Clone the project, get inspired and whatever it's open source! 