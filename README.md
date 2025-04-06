# Laravel Task Management API

## Overview

This is a RESTful API built with Laravel that allows users to manage tasks. The API provides authentication using JWT
and includes endpoints for user registration, login, and task management (adding, fetching, updating, and deleting
tasks).

## Features

- User authentication using JWT (JSON Web Token)
- Role-based access control for tasks
- CRUD operations for tasks
- Pagination for task listings
- Validation and exception handling
- API testing using Postman

---

## Prerequisites

Ensure you have the following installed:

- PHP 8.1+
- Composer
- Laravel 12
- PostgreSQL or MySQL

---

## Installation

### 1. Clone the repository

```sh
    git clone git@github.com:Sp4ngl3r/tasks-api.git
    cd your-project-directory
```

### 2. Install dependencies

```sh
    composer install
```

### 3. Set up the environment file

```sh
    cp .env.example .env
```

Update the `.env` file with your database credentials.

### 4. Generate the application key

```sh
    php artisan key:generate
```

### 5. Generate JWT secret key

```sh
    php artisan jwt:secret
```

### 6. Start the development server

```sh
    php artisan serve
```

---

## API Base URL

#### The API base URL is `https://tasks-api-main-qvogx2.laravel.cloud`

---

## API Endpoints

### **Authentication**

#### Register a new user

```http
POST /api/v1/auth/register
```

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password"
}
```

#### Login

```http
POST /api/v1/auth/login
```

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password"
}
```

#### Logout

```http
POST /api/logout
```

**Headers:**

```json
Authorization: Bearer {token}
```

#### Get Authenticated User

```http
GET /api/v1/auth/me
```

---

### **Task Management**

#### Get All Tasks (Paginated)

```http
GET /api/tasks?page=1
```

**Headers:**

```json
Authorization: Bearer {token}
```

#### Get a Single Task

```http
GET /api/tasks/{task_id}
```

#### Create a Task

```http
POST /api/tasks
```

**Request Body:**

```json
{
    "title": "New Task",
    "description": "Task details",
    "status": "pending"
}
```

#### Update a Task

```http
PUT /api/tasks/{task_id}
```

#### Delete a Task

```http
DELETE /api/tasks/{task_id}
```

---

## Authentication & Authorization

- The API uses **JWT authentication** to protect endpoints.
- Users can only manage their own tasks.
- Unauthorized access will return **403 Forbidden**.

---

## Running Tests

Run tests using **Pest**:

```sh
php artisan test
```

To run all tests in parallel:

```sh
php artisan test --parallel
```

---

## Postman Collection

You can import the [Postman collection](Tasks%20API.postman_collection.json) to test the API endpoints.

---

## License

This project is open-source and available under the [MIT License](LICENSE).

