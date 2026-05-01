# Event & Reminder Management System

A Laravel 12 REST API for managing events, reminders, and invoice generation.

## Requirements

- PHP 8.2+
- Composer
- MySQL
- XAMPP or any local server

## Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/event-manager.git
cd event-manager
```

### 2. Install dependencies
```bash
composer install
```

### 3. Create environment file
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database
Open `.env` and update:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_manager
DB_USERNAME=root
DB_PASSWORD=

### 5. Run migrations
```bash
php artisan migrate
```

### 6. Start the server
```bash
php artisan serve
```

API is now running at `http://127.0.0.1:8000`

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/register | Register a new user |
| POST | /api/login | Login and get token |
| GET | /api/profile | Get logged in user profile |
| POST | /api/logout | Logout |

### Events
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/events | List all events |
| POST | /api/events | Create an event |
| GET | /api/events/{id} | View single event |
| PUT | /api/events/{id} | Update an event |
| DELETE | /api/events/{id} | Delete an event |
| POST | /api/events/{id}/done | Mark as done |
| POST | /api/events/{id}/undone | Mark as undone |
| POST | /api/events/{id}/cancel | Cancel event |

### Reminders
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/reminders | List all reminders |
| POST | /api/reminders | Create a reminder |
| GET | /api/reminders/{id} | View single reminder |
| PUT | /api/reminders/{id} | Update a reminder |
| DELETE | /api/reminders/{id} | Delete a reminder |
| POST | /api/reminders/{id}/done | Mark as done |
| POST | /api/reminders/{id}/undone | Mark as undone |
| POST | /api/reminders/{id}/cancel | Cancel reminder |

### Invoices
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/invoices | List all invoices |
| POST | /api/invoices | Create an invoice |
| GET | /api/invoices/{id} | View invoice as JSON |
| GET | /api/invoices/{id}/download | Download invoice as PDF |

## Authentication

All endpoints except `/register` and `/login` require a Bearer token:
Authorization: Bearer YOUR_TOKEN
Accept: application/json

## Invoice PDF

The invoice PDF supports fully dynamic data including:
- Biller and client details
- Line items with HSN, quantity, GST
- Auto-calculated SGST, CGST, totals
- Discount, early pay discount
- Bank details, terms, and notes
