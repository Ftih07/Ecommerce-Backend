# Authentication Documentation

This document outlines the authentication system implemented in the Ecommerce Backend application.

## Overview

The authentication system uses Laravel Sanctum for API token authentication. It provides endpoints for user registration, login, logout, token refresh, user information, and role-based access control.

## Authentication Endpoints

### Public Endpoints

- **POST /api/auth/register** - Register a new user
  - Required fields: name, email, password, password_confirmation
  - Optional fields: address, profile_image
  - Returns: user object and authentication token
  - Rate limited to 5 attempts per minute per IP

- **POST /api/auth/login** - Login a user
  - Required fields: email, password
  - Returns: user object and authentication token
  - Rate limited to 5 attempts per minute per IP

### Protected Endpoints (Require Authentication)

- **POST /api/auth/logout** - Logout (revoke tokens)
  - Requires: Authentication
  - Returns: Success message

- **GET /api/auth/user** - Get authenticated user information
  - Requires: Authentication
  - Returns: User object

- **POST /api/auth/refresh** - Refresh authentication token
  - Requires: Authentication
  - Returns: New token and user object

- **GET /api/auth/check** - Check if token is valid
  - Requires: Authentication
  - Returns: Validation status and user object

- **GET /api/auth/roles** - Get user roles
  - Requires: Authentication
  - Returns: Array of roles assigned to the user

## Authentication Flow

1. User registers or logs in and receives a token
2. Token is included in subsequent requests as a Bearer token in the Authorization header
3. Token expires after 7 days (configurable in sanctum.php)
4. User can refresh token before expiry to extend access

## Role-Based Access Control

The system implements a role-based access control mechanism with the following default roles:

- **admin**: Full access to all resources
- **customer**: Access to personal resources (carts, orders, payments)
- **seller**: (Future implementation) Access to store management

### Role Assignment

- New users are automatically assigned the "customer" role on registration
- Roles are stored in the `roles` table and relationship in `role_user` table
- User-Role relationship is many-to-many

### Route Protection

- Customer resources (carts, orders, payments) require 'customer' or 'admin' role
- Admin resources (users, store management) require 'admin' role

## Security Measures

- Tokens are stored in the `personal_access_tokens` table
- Passwords are hashed using bcrypt
- Security headers are added to all responses via middleware:
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - X-Frame-Options: DENY
  - Strict-Transport-Security: max-age=31536000; includeSubDomains
  - Content-Security-Policy: default-src 'self'
  - Referrer-Policy: no-referrer-when-downgrade
  - Permissions-Policy: camera=(), microphone=(), geolocation=()
- Rate limiting for authentication attempts (5 per minute)
- Custom error handling for authentication exceptions
- CORS protection for API endpoints

## Future Enhancements

- Two-factor authentication
- OAuth integration for social logins
- Enhanced permission system with granular access control
