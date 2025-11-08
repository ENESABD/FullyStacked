# GiftList Backend API

REST API for managing recipients and gifts built with PHP and MySQL.

## Tech Stack

- PHP 8+
- MySQL
- JWT Authentication
- NGINX + PHP-FPM

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

Copy `.env` and update database credentials:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=giftlist
DB_USER=root
DB_PASS=
JWT_SECRET=your-secret-key
```

### 3. Create Database

Run the SQL schema file to create the database:

```bash
mysql -u root -p < giftlist-schema.sql
```

### 4. Start Server

For development with PHP built-in server:

```bash
php -S localhost:8000 -t public
```

For production, configure NGINX using the provided `nginx.conf`.

## API Endpoints

### Authentication

- `POST /auth/register` - Register new user
- `POST /auth/login` - Login and get JWT token
- `GET /auth/me` - Get current user (requires auth)

### Recipients

- `GET /recipients` - List all recipients (requires auth)
- `GET /recipients/{id}` - Get single recipient (requires auth)
- `POST /recipients` - Create recipient (requires auth)
- `PUT /recipients/{id}` - Update recipient (requires auth)
- `DELETE /recipients/{id}` - Delete recipient (requires auth)

### Gifts

- `GET /gifts` - List all gifts (requires auth)
- `GET /gifts/{id}` - Get single gift (requires auth)
- `GET /recipients/{id}/gifts` - Get gifts for recipient (requires auth)
- `POST /gifts` - Create gift (requires auth)
- `PUT /gifts/{id}` - Update gift (requires auth)
- `DELETE /gifts/{id}` - Delete gift (requires auth)

### Health

- `GET /health` - Check server status

## Authentication

Protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "message": "Error description"
  }
}
```

## HTTP Status Codes

- 200 - OK
- 201 - Created
- 400 - Bad Request
- 401 - Unauthorized
- 404 - Not Found
- 500 - Internal Server Error
