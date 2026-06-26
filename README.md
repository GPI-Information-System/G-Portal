# System Directory

A web-based **System Monitoring & Maintenance Management Platform** designed for tracking application health, scheduling maintenance activities, managing users, generating analytics, and providing public-facing status pages.

---

## Features

### Core Features

#### Authentication & Session Management

- Role-based access control:
  - Super Admin
  - Admin
  - Viewer
- Secure PHP session handling

#### System Dashboard

- Add, edit, and delete systems
- Real-time system health monitoring
- Status cards and filters
- Maintenance calendar integration

#### Analytics & Reporting

- Uptime and downtime reporting
- Interactive charts
- Search and pagination
- PDF export support

#### Public Viewer Pages

- Read-only system status viewer
- Public maintenance schedule viewer
- Japanese translation support
- System filtering and search

#### Notification Center

- Real-time status change alerts
- Notification bell and panel
- Toast notifications

---

### Maintenance Scheduling

- Single-system maintenance scheduling
- Bulk maintenance scheduling
- Automatic schedule conflict detection
- Monthly calendar view
- Maintenance detail modal
- Countdown timers
- Exceeded duration tracking

---

### Email Notifications

- Automatic alerts when systems go down/offline
- Maintenance schedule notifications
- Schedule creation, update, and cancellation emails
- Change tracking showing exactly what changed
- Recipient tag input with autocomplete
- PHPMailer integration
- File-based logging fallback when SMTP is unavailable

---

### Error Landing Page

Custom branded **G-Portal Error Page** supporting:

- `404` – Page Not Found
- `403` – Access Denied
- `500` – Internal Server Error
- `503` – Service Unavailable
- `maintenance` – System Under Maintenance
- `down` – System Down
- `offline` – System Offline

#### Dynamic Features

- Retrieves system details from the database
- Displays maintenance information automatically
- Shows contact information
- Quick link back to the public status portal

---

# Folder Structure

```text
System-Directory/
│
├── assets/
│   ├── css/
│   │   ├── style.css                 # Main dashboard styles
│   │   ├── maintenance.css           # Maintenance modal styles
│   │   ├── analytics.css             # Analytics page styles
│   │   └── users.css                 # User management styles
│   │
│   └── js/
│       ├── main.js                   # Dashboard logic, charts, filters, CRUD
│       ├── maintenance.js            # Calendar, maintenance modal, bulk scheduling
│       ├── health_check.js           # Polling logic (every 10s), toast notifications
│       ├── analytics.js              # Analytics page JS
│       ├── viewer.js                 # Viewer page (JP translation, filters, popovers)
│       ├── viewer_maintenance.js     # Maintenance viewer utilities
│       ├── notifications.js          # Notification bell and panel
│       └── users.js                  # User management CRUD
│
├── backend/
│   ├── logs/
│   │   ├── health_check.log          # Auto-rotating (max 500 lines)
│   │   ├── emails.log                # Auto-rotating (max 500 lines)
│   │   └── maintenance_emails.log
│   │
│   ├── add_system.php
│   ├── edit_system.php
│   ├── delete_system.php
│   ├── add_user.php
│   ├── edit_user.php
│   ├── delete_user.php
│   ├── save_maintenance.php
│   ├── trigger_health_check.php      # Main health check engine
│   ├── check_systems_health.php      # Cron health check version
│   ├── get_analytics_data.php
│   ├── get_notifications.php
│   ├── get_systems_status.php
│   ├── send_email_notification.php
│   ├── update_log_note.php
│   └── logout.php
│
├── config/
│   ├── database.php                  # DB connection and helpers
│   ├── session.php                   # Session and role helpers
│   └── email_config.php              # SMTP configuration
│
├── pages/
│   ├── dashboard.php                 # Admin dashboard
│   ├── analytics.php                 # Analytics & Reports
│   ├── viewer.php                    # Public status page
│   ├── viewer_maintenance.php        # Public maintenance page
│   └── users.php                     # User management
│
├── sessions/                         # PHP session storage
│
├── uploads/
│   └── logos/                        # Uploaded system logos
│
├── vendor/                           # Composer dependencies (PHPMailer)
│
└── index.php                         # Login page
```

---

# Architecture Overview

## Frontend

### CSS

- `style.css` – Main dashboard styling
- `maintenance.css` – Maintenance scheduling UI
- `analytics.css` – Analytics and reporting styles
- `users.css` – User management styles

### JavaScript

- `main.js` – Dashboard logic, charts, filters, CRUD
- `maintenance.js` – Calendar and maintenance scheduling
- `health_check.js` – 10-second polling and health monitoring
- `analytics.js` – Analytics search, pagination, charts
- `viewer.js` – Public status viewer
- `viewer_maintenance.js` – Maintenance schedules and countdowns
- `notifications.js` – Notification center
- `users.js` – User management CRUD

---

## Backend Modules

### System Management

- `add_system.php`
- `edit_system.php`
- `delete_system.php`

### User Management

- `add_user.php`
- `edit_user.php`
- `delete_user.php`

### Maintenance Management

- `save_maintenance.php`

### Health Monitoring

#### trigger_health_check.php

- Main health check engine
- Badge status updates
- Domain fallback support

#### check_systems_health.php

- Cron-compatible health checks
- Scheduled monitoring

### Analytics & Notifications

- `get_analytics_data.php`
- `get_notifications.php`
- `get_systems_status.php`
- `update_log_note.php`

### Email Services

#### send_email_notification.php

- SMTP delivery via PHPMailer
- File logging fallback support

---

# Demo Accounts

> For development and testing purposes only.

## Super Admin

```text
Username: superadmin
Password: admin123
```

## Admin

```text
Username: admin
Password: admin123
```

---

# Error Page Testing

### 404 - Page Not Found

```text
http://localhost:8080/system-directory/pages/error_page.php?type=404
```

### 500 - Internal Server Error

```text
http://localhost:8080/system-directory/pages/error_page.php?type=500
```

### 403 - Access Denied

```text
http://localhost:8080/system-directory/pages/error_page.php?type=403
```

### Maintenance Mode

```text
http://localhost:8080/system-directory/pages/error_page.php?type=maintenance&domain=youtube.com
```

### System Down

```text
http://localhost:8080/system-directory/pages/error_page.php?type=down&domain=youtube.com
```

---

# Logging

The application maintains rotating logs under:

```text
backend/logs/
├── health_check.log
├── emails.log
└── maintenance_emails.log
```

### Log Retention

```text
Maximum 500 lines per log file
```

---

# Database Maintenance

## Full Database Reset

> Clears systems, maintenance schedules, and analytics history.

```sql
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM maintenance_schedules;
DELETE FROM status_logs;
DELETE FROM systems;

ALTER TABLE maintenance_schedules AUTO_INCREMENT = 1;
ALTER TABLE status_logs AUTO_INCREMENT = 1;
ALTER TABLE systems AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;
```

---

## Analytics Data Reset Only

> Clears analytics history while preserving system records.

```sql
-- Clear all status change logs
TRUNCATE TABLE status_logs;

-- Clear all maintenance schedules
TRUNCATE TABLE maintenance_schedules;

-- Reset systems currently marked as maintenance
UPDATE systems
SET status = 'online',
    updated_at = NOW()
WHERE status = 'maintenance';
```

### Notes

- Removes all maintenance schedules
- Clears analytics and status history
- Keeps all system records intact
- Resets systems currently in maintenance mode back to `online`
- Does not modify systems marked as:
  - `down`
  - `offline`
  - `archived`

---

# Dependencies

- PHP 8+
- MySQL / MariaDB
- PHPMailer
- Composer
- JavaScript (ES6+)
- HTML5 / CSS3

---

# Key Highlights

✅ Real-time Health Monitoring  
✅ Maintenance Scheduling & Calendar Management  
✅ Email Notification System  
✅ Public Status Viewer Pages  
✅ Analytics & Uptime Reporting  
✅ User & Role Management  
✅ Dynamic G-Portal Error Pages  
✅ PHPMailer Integration with Logging Fallback  
✅ Maintenance Conflict Detection  
✅ Bulk Maintenance Scheduling  
✅ Notification Center  
✅ Public Maintenance Viewer
