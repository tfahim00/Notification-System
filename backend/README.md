# Notification System

A simple **Laravel** project that allows sending notifications to users manually, in bulk, and automatically.  
The project demonstrates the use of Laravel’s built-in **Notification System**, **Mail System**, and **Scheduling feature**.

---

## Overview

This project is designed to send notifications to registered users in three different ways:
1. **Manual Notification** — send to a specific user.
2. **Bulk Notification** — send to multiple users at once.
3. **Scheduled Notification** — automatically send notifications at a specific interval using Laravel’s Scheduler.

Notifications are sent via **email**, and **MailHog** is used for local email testing.

---

## Features

-  Send notification manually to a specific user  
-  Send bulk notifications to multiple users  
-  Schedule notifications automatically (via Laravel Scheduler)  
-  MailHog integration for local mail testing  
-  Follows clean and maintainable code structure  
-  Demonstrates use of design patterns and Laravel best practices  

---

##  Installation & Setup

###  Prerequisites
Make sure you have installed the following:
- PHP >= 8.2  
- Composer  
- Laravel 12  
- MySQL
- MailHog (for local email testing)

---

###  Step-by-Step Installation

#### 1️ Clone the Repository
```bash
git clone https://github.com/tfahim00/notification-system.git
cd notification-system
```

#### 2️ Install Dependencies
```bash
composer install
```

#### 3️ Configure Environment
Copy the example environment file and update configuration values:
Edit .env to set up your database:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourdatabasename
DB_USERNAME=yourdatabaseusername
DB_PASSWORD=yourdatabsepassword
```

#### 4️ Run Database Migrations
```bash
php artisan migrate
```

📧 Mail Configuration (MailHog)

#### 1️ Install and start MailHog:
```bash 
mailhog
```

#### 2️ Update your .env file
```bash
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="Notification System"
```

#### 3️ Open http://localhost:8025 to view received emails.



<h3> Usage </h3>

1️⃣ Send Notification Manually

  Sends a notification to a specific user.

  Route: POST '/notifications/send'

2️⃣ Send Bulk Notifications

  Sends the same notification to all users.

  Route: POST '/notifications/send-bulk'

3️⃣ Scheduled Notifications

  Automatically sends notifications based on a defined schedule.

  Configuration Location:
  bootstrap/app.php

  To run the scheduler:
```bash
php artisan schedule:work
```

<h3>API Testing (Optional)</h3>

You can also test using Postman or cURL. But before doing this, you must polpulate your User table with some data.

Example request:
```bash
curl -X POST http://localhost:8000/api/notifications/send \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "message": "Hello from the Notification System!",
    "channel": "email"
  }'
```

```bash
curl -X POST http://localhost:8000/api/notifications/send-bulk \
  -H "Content-Type: application/json" \
  -d '{
    "position": "Developer",
    "message": "Test notification message",
    "channels": ["email"]
  }'
```


<h3>Design Pattern Used</h3>

The project follows:

- Dependency Injection for cleaner controller logic.
- Strategy Pattern to choose the notification strategy.



<h3>Sending Notification Automatically</h3>
- Firstly you need to download and install rabbitmq. Also you have to update the .env file like the below

```bash
QUEUE_CONNECTION=rabbitmq

RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=admin
RABBITMQ_PASSWORD=admin123
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=notifications
```

- After that you need to start the rabbitmq server.
- After this open a new terminal and run

```bash
php artisan queue:work rabbitmq --queue=notifications --tries=3 --sleep=3 -vvv
```

- In another terminal
```bash
php artisan schedule:work
```






