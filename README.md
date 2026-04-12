# 💚 Health Tracker

A full-stack PHP + MySQL health tracking web app with a clean dark UI.

## Features

- 🔐 **Auth** — Register, login, logout with bcrypt password hashing
- 😊 **Mood Tracker** — Log daily mood (1–5 scale) with notes
- 🥗 **Nutrition Tracker** — Log meals with calories, protein, carbs, fats
- 💪 **Workout Tracker** — Log exercises with duration and intensity
- 💧 **Water Intake** — Quick-add buttons + visual progress
- 🎯 **Goals** — Set daily calorie, water, and workout targets
- ⚡ **Dashboard** — Today's summary with progress bars

## Tech Stack

- **Backend**: PHP 8+ with PDO (prepared statements)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: Vanilla HTML/CSS/JS (no frameworks)
- **Fonts**: Syne + DM Sans (Google Fonts)

## Setup

### 1. Clone the repo
```bash
git clone https://github.com/YOUR_USERNAME/health-tracker.git
cd health-tracker
```

### 2. Create the database
```bash
mysql -u root -p < database.sql
```

### 3. Configure the app
Edit `includes/config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'health_tracker');
define('APP_URL',  'http://localhost/health-tracker');
```

### 4. Serve with PHP
```bash
# Option A: PHP built-in server
php -S localhost:8000

# Option B: Place in your XAMPP/WAMP/MAMP htdocs folder
# and visit http://localhost/health-tracker
```

### 5. Login
Use the demo account (created by the seed SQL):
- **Email**: demo@example.com
- **Password**: password

Or register a new account.

## Project Structure

```
health-tracker/
├── index.php              # Root redirect
├── database.sql           # DB schema + seed data
├── includes/
│   ├── config.php         # DB connection + constants
│   ├── auth.php           # Login, register, session helpers
│   ├── header.php         # Sidebar layout
│   └── footer.php         # Closing tags
├── pages/
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── mood.php
│   ├── nutrition.php
│   ├── workout.php
│   ├── water.php
│   ├── goals.php
│   └── logout.php
└── assets/
    ├── css/style.css
    └── js/app.js
```

## Security Practices Used

- Passwords hashed with `password_hash()` (bcrypt)
- All DB queries use **PDO prepared statements** (no SQL injection)
- All output escaped with `htmlspecialchars()` (no XSS)
- Session ID regenerated on login
- `HttpOnly` + `SameSite` cookie flags

## Screenshots

> Add screenshots here after running the app locally.

## License

MIT
