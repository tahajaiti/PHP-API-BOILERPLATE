# PHP API Boilerplate

<!-- TOC -->
* [PHP API Boilerplate](#php-api-boilerplate)
* [Overview](#overview)
* [Features](#features)
  * [Folder Structure](#folder-structure)
  * [Usage](#usage)
  * [Contributing](#contributing)
<!-- TOC -->

# Overview

- This is a lightweight and efficient PHP API boilerplate designed to provide a structured and scalable foundation for
  building RESTful APIs. It follows a clean architecture pattern, incorporating models, controllers, services,
  repositories, a router, and custom exception handlers.

# Features

* Routing System: Custom-built router to handle API requests dynamically.
* MVC Structure: Organized with Models, Controllers, and Services for clean separation of concerns.
* Repositories: Abstracted data access layer for better maintainability and testability.
* Middleware Support: Easily add middleware for authentication, logging, validation, and more.
* Custom Exception Handling: Centralized error handling for consistent API responses.
* Request & Response Management:
* Request class for input sanitization and validation.
* Response class for standardized API outputs.
* JSON Support: Automatic JSON parsing and encoding for seamless data handling.
* JWT Support: Built-in JSON Web Tokens (JWT) creation and validation using the Firebase JWT library.
* Environment Configuration: Easy configuration management for different environments (development, staging,
  production).

## Folder Structure

``` php
│── app/              # Main application directory  
│   ├── Controller/   # Handles HTTP requests and responses  
│   ├── Core/         # Core system components (Router, Request, Response)  
│   ├── Handler/      # Custom exception and error handlers  
│   ├── Helpers/      # Utility functions and helper classes  
│   ├── Middleware/   # Middleware for request filtering (e.g., auth, logging)  
│   ├── Model/        # Data models representing database entities  
│   ├── Repository/   # Database abstraction layer for interacting with models  
│   ├── Routes/       # Route definitions for the API  
│   ├── Service/      # Business logic and application services  
│  
├── vendor/           # Composer dependencies (autoloaded packages)  
├── .htaccess         # Apache configuration for URL rewriting  
├── composer.json     # Composer package manager file  
├── composer.lock     # Locks Composer dependencies  
├── config.php        # Configuration file for database, environment variables, etc.  
├── index.php         # Main entry point of the application 
```

## Usage

+ Define API routes in app/Routes/.

+ Implement controllers in app/Controller/.

+ Add business logic in app/Service/.

+ Use app/Middleware/ for request filtering.

+ Handle database operations in app/Repository/.

## Contributing

+ Feel free to submit pull requests or open issues for improvements.
