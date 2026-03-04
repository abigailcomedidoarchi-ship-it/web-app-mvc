# Email Authentication System with OTP and Email Verification

This is a PHP-based web application demonstrating a secure authentication system with email verification and OTP (One-Time Password) login.

---

## Features

- User registration with email verification
- Password hashing for security
- Login with OTP verification (expires after 5 minutes)
- Session-based authentication
- Logout and session management
- Clean MVC structure

---

## Requirements

- PHP 7.4+  
- MySQL / MariaDB  
- Composer (for PHPMailer)  
- Web server (Apache, Nginx, or built-in PHP server)

---

## Notes
- OTP expires in 5 minutes
- Sessions cleared on logout
- Update SMTP credentials in AuthController.php to send emails

## Installation

1. **Clone the repository**
```bash
git clone https://github.com/abigailcomedidoarchi-ship-it/web-app-mvc.git
cd web-app-mvc 


