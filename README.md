# Driving Experience Tracker

A professional web application for tracking, managing, and analyzing driving experiences with advanced analytics, modern UI design, and comprehensive data visualization.

## Technical Overview

1) Built with PHP + MySQL using PDO and strict typing for safer data access.
2) Clean routing separates API actions from view rendering.
3) Schema initialization and seed logic support zero-setup runs.
4) Repository and model layers keep persistence and domain logic organized.
5) Validation guards required fields and numeric constraints before writes.
6) Many-to-many relationships (maneouvring tags) are handled with join tables.
7) Session-scoped anonymization protects IDs in the UI.
8) Responsive UI with filters, tables, and cards for fast browsing.
9) Charts render analytics with a consistent theme palette.
10) Modular structure makes it easy to extend lookups or metrics.

## 🌐 Live Demo

**Try it live:** [https://kamranquliyev.alwaysdata.net/hwp_backend/index.php](https://kamranquliyev.alwaysdata.net/hwp_backend/index.php)

## Features

- 📝 **Trip Management**: Record driving trips with date, time, duration, distance, and conditions
- 📊 **Advanced Analytics**: Interactive charts showing monthly trends, time of day patterns, and weather analysis
- 🎯 **Smart Filtering**: Filter trips by weather, time, road conditions, and maneouvring
- 📈 **Data Export**: Export to CSV, Excel, and PDF formats
- 🎨 **Modern UI**: Glassmorphism design with smooth animations and special effects
- 🌓 **Theme Toggle**: Switch between dark and light modes
- 📱 **Fully Responsive**: Optimized for desktop, tablet, and mobile devices
- 🔒 **Security First**: Session-based ID anonymization and SQL injection prevention
- ⚡ **Real-time Updates**: Instant statistics and dynamic data tables

## 📋 Table of Contents

- [Installation](#-installation)
- [Project Structure](#-project-structure)
- [Technology Stack](#-technology-stack)
- [Usage](#-usage)
- [Features Overview](#-features-overview)
- [Security](#-security)
- [Configuration](#-configuration)
- [API Endpoints](#-api-endpoints)
- [Development](#-development)
- [Troubleshooting](#-troubleshooting)

## 🚀 Installation

### Prerequisites

- **PHP**: 8.0 or higher (with PDO MySQL extension)
- **MySQL/MariaDB**: 5.7+ / 10.3+
- **Web Server**: Apache, Nginx, or built-in PHP server
- **Modern Browser**: Chrome, Firefox, Safari, or Edge

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd hwproject
   ```

2. **Configure database credentials**
   
   Create your local configuration file:
   ```bash
   cp config.local.example.php config.local.php
   ```
   
   Edit `config.local.php` with your database credentials:
   ```php
   <?php
   return [
       'DB_HOST' => 'localhost',        // Your database host
       'DB_PORT' => 3306,               // Database port
       'DB_NAME' => 'your_database',    // Your database name
       'DB_USER' => 'your_username',    // Your username
       'DB_PASS' => 'your_password',    // Your password
   ];
   ```
   
   **⚠️ SECURITY NOTE:** `config.local.php` is automatically excluded from Git to protect your credentials.

3. **Alternative: Environment Variables**
   
   Set credentials as environment variables:
   ```bash
   export DB_HOST="localhost"
   export DB_PORT="3306"
   export DB_NAME="your_database"
   export DB_USER="your_username"
   export DB_PASS="your_password"
   ```

4. **Launch the application**
   
   Option A - Using PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   Then visit: `http://localhost:8000/index.php`
   
   Option B - Using XAMPP/LAMP:
   - Place project in `htdocs` folder
   - Visit: `http://localhost/hwproject/index.php`
   
   **First run**: Database tables are created automatically!

## 📁 Project Structure

```
hwproject/
│
├── 🔧 Backend (PHP)
│   ├── index.php                 # Application entry point & bootstrap
│   ├── config.php                # Database configuration loader (safe to commit)
│   ├── config.local.php          # Local credentials (excluded from Git)
│   ├── config.local.example.php  # Configuration template
│   ├── database.php              # Schema initialization & setup
│   ├── session.php               # Session management & security
│   ├── models.php                # Domain models (DrivingExperience)
│   ├── repository.php            # Data access layer (CRUD operations)
│   ├── controllers.php           # API controllers (Trip, Statistics, View)
│   └── router.php                # Request routing & dispatching
│
├── 🎨 Frontend
│   └── view.php                  # Complete UI (HTML + CSS + JavaScript)
│
├── 📄 Documentation
│   ├── README.md                 # This file
│   ├── SECURITY_IMPLEMENTATION.md  # Security documentation
│   └── VALIDATION_GUIDE.md       # Validation guidelines
│
└── ⚙️ Configuration
    ├── .gitignore                # Git exclusions (credentials protected)
    └── css/                      # Additional stylesheets (if any)
```

### File Descriptions

#### **Core Backend Files**

- **`index.php`**: Application entry point that initializes session, creates database if needed, and routes requests
- **`config.php`**: Loads database credentials from `config.local.php` or environment variables with fallback defaults
- **`session.php`**: Manages user sessions with 1-hour timeout and ID anonymization (TRP_XXXXXXXX codes)
- **`database.php`**: Creates database schema and populates lookup tables automatically
- **`models.php`**: Defines `DrivingExperience` class with business logic and validation
- **`repository.php`**: Handles all database operations using PDO with prepared statements
- **`controllers.php`**: Contains `TripController`, `StatisticsController`, and `ViewController` for API endpoints
- **`router.php`**: Front controller that dispatches requests to appropriate controllers

#### **Frontend**

- **`view.php`**: Complete presentation layer with modern UI, animations, and interactive features

## 🛠️ Technology Stack

### Backend
- **PHP 8+**: Modern PHP with strict typing and object-oriented design
- **MySQL/MariaDB**: Relational database with automatic schema creation
- **PDO**: Secure database access with prepared statements
- **MVC Architecture**: Clean separation of concerns

### Frontend
- **HTML5 & CSS3**: Semantic markup with modern styling
- **JavaScript (ES6+)**: Modern JavaScript with async/await
- **jQuery 3.7.1**: DOM manipulation and AJAX requests
- **Chart.js 4.4.1**: Beautiful, responsive charts and graphs
- **DataTables 1.13.7**: Advanced table features with export capabilities

### Design Features
- **Glassmorphism**: Frosted glass effects with backdrop blur
- **Gradient Animations**: Smooth color transitions
- **Morphing Shapes**: Animated decorative elements
- **Magnetic Buttons**: Interactive hover effects
- **Neon Glow**: Glowing text and border effects
- **Particle System**: Floating particle animations

## 💻 Usage

### Adding a Trip

1. Navigate to **Dashboard** tab
2. Fill in the trip details:
   - Date and time
   - Duration and distance
   - Weather conditions
   - Road and surface conditions
   - Driver health status
   - Maneouvring (multiple selection)
   - Optional: GPS coordinates and notes
3. Click **Save Trip**

### Viewing Analytics

1. Go to **Summary** tab
2. View interactive charts:
   - Monthly trip distribution
   - Time of day patterns
   - Weather condition analysis
3. See overall statistics:
   - Total trips, distance, duration
   - Average values and trends

### Managing Trips

1. Navigate to **Trips** tab
2. Use DataTables features:
   - Search by any field
   - Sort by columns
   - Export to CSV/Excel/PDF
   - Edit or delete trips
3. Use filter panel for advanced filtering

## 🎯 Features Overview

### Dashboard
- Clean form interface for trip entry
- Real-time validation
- Date/time pickers
- Multi-select for maneouvring
- GPS coordinate input
- Rich text notes area

### Summary & Analytics
- **Monthly Trips Chart**: Bar chart showing trips per month
- **Time of Day Distribution**: Pie chart of morning/afternoon/evening/night
- **Weather Conditions**: Pie chart of weather patterns
- **KPI Cards**: Floating cards with key metrics
- **Live Statistics**: Real-time updates as data changes

### Trips Management
- **DataTables Integration**: 
  - Pagination (10/25/50/100 entries)
  - Live search across all fields
  - Column sorting
  - Export buttons (CSV, Excel, Copy, Print)
- **Trip Actions**:
  - Edit: Modify existing trips
  - Delete: Remove trips with confirmation
- **Filtering**:
  - Filter by weather
  - Filter by time of day
  - Filter by road conditions
  - Filter by maneouvring

### UI/UX Enhancements
- **Theme Toggle**: Switch between dark/light modes
- **Responsive Design**: Works on all devices
- **Smooth Animations**: Fade-ins, slide-ups, hover effects
- **Loading States**: Visual feedback during operations
- **Toast Notifications**: Success/error messages
- **Modal Dialogs**: Edit forms in overlay

## 🔐 Security

### Security Features

✅ **Session Management**
- 1-hour session timeout
- Automatic session cleanup
- Session regeneration on important actions

✅ **ID Anonymization**
- Database IDs never exposed to frontend
- Anonymous codes (TRP_XXXXXXXX) for all client operations
- Mapping stored in secure PHP session

✅ **SQL Injection Prevention**
- All queries use PDO prepared statements
- Parameter binding for all user inputs
- No direct SQL concatenation

✅ **XSS Protection**
- Input sanitization
- Output escaping where needed
- Content Security Policy ready

✅ **Credentials Protection**
- `config.local.php` excluded from version control
- Environment variable support
- Secure configuration loading priority

### Configuration Priority

Credentials are loaded in this order (highest to lowest priority):

1. **Environment Variables** (production)
2. **config.local.php** (local development)
3. **Default Values** (fallback only)

## ⚙️ Configuration

### Database Configuration

The application uses a three-tier configuration system:

**config.local.php** (create from example):
```php
<?php
return [
    'DB_HOST' => 'localhost',
    'DB_PORT' => 3306,
    'DB_NAME' => 'driving_experience',
    'DB_USER' => 'root',
    'DB_PASS' => 'your_password',
];
```

**Environment Variables** (production):
```bash
DB_HOST=your-production-host.com
DB_PORT=3306
DB_NAME=production_db
DB_USER=prod_user
DB_PASS=secure_password
```

### Application Configuration

Edit constants in respective files:

- **Session Timeout**: Modify `SESSION_TIMEOUT` in `session.php` (default: 3600 seconds)
- **Anonymous ID Prefix**: Change `TRP_` prefix in `session.php`
- **Theme**: Default theme in `view.php` localStorage

## 🔌 API Endpoints

All API requests use the format: `index.php?api={endpoint}`

### Trip Operations

**Add Trip**
```
POST index.php?api=add_trip
Body: {
  "anon_code": "TRP_abc123",
  "date": "2025-12-24",
  "time": "14:30",
  "duration_minutes": 45,
  "distance_km": 25.5,
  "weather_id": "W001",
  "tod_id": "T002",
  ...
}
Response: { "success": true, "message": "Trip added", "trip_id": "TRP_xyz789" }
```

**List All Trips**
```
GET index.php?api=list_trips
Response: [
  {
    "anon_code": "TRP_abc123",
    "date": "2025-12-24",
    "time": "14:30",
    ...
  }
]
```

**Get Single Trip**
```
GET index.php?api=get_trip&id=TRP_abc123
Response: { "anon_code": "TRP_abc123", ... }
```

**Update Trip**
```
POST index.php?api=update_trip
Body: { "anon_code": "TRP_abc123", "duration_minutes": 50, ... }
Response: { "success": true, "message": "Trip updated" }
```

**Delete Trip**
```
POST index.php?api=delete_trip
Body: { "anon_code": "TRP_abc123" }
Response: { "success": true, "message": "Trip deleted" }
```

### Statistics

**Get Dashboard Stats**
```
GET index.php?api=stats
Response: {
  "total_trips": 42,
  "total_km": 1250.5,
  "total_minutes": 1800,
  "avg_duration": 42.86,
  "avg_distance": 29.77
}
```

**Get Summary Charts**
```
GET index.php?api=summary
Response: {
  "monthly_trips": { "2025-01": 10, "2025-02": 15, ... },
  "time_of_day": { "Morning": 12, "Afternoon": 18, ... },
  "weather_conditions": { "Sunny": 25, "Rainy": 10, ... }
}
```

## 👨‍💻 Development

### Adding New Features

**1. Add a Model Property**
- Edit `models.php` → Add property to `DrivingExperience` class
- Update constructor and getters/setters

**2. Update Database Schema**
- Edit `database.php` → Modify `initializeSchema()` function
- Add column to CREATE TABLE statement

**3. Update Repository**
- Edit `repository.php` → Update CRUD methods
- Add new column to INSERT/UPDATE queries

**4. Update Controllers**
- Edit `controllers.php` → Handle new field in requests
- Update validation logic

**5. Update View**
- Edit `view.php` → Add form field or display element
- Update JavaScript to handle new field

### Code Style

- **PSR-12**: Follow PHP-FIG coding standards
- **Strict Types**: Use `declare(strict_types=1);` in all PHP files
- **Type Hints**: Add parameter and return type declarations
- **Comments**: Keep essential comments only
- **Naming**: Use descriptive variable and function names

### Testing Checklist

- [ ] Add trip with all fields
- [ ] Edit existing trip
- [ ] Delete trip
- [ ] View statistics
- [ ] Export data (CSV, Excel, PDF)
- [ ] Switch themes
- [ ] Test on mobile device
- [ ] Check session timeout
- [ ] Verify ID anonymization
- [ ] Test with invalid data

## 🐛 Troubleshooting

### Database Connection Errors

**Problem**: "SQLSTATE[HY000] [1045] Access denied"
- ✅ Check credentials in `config.local.php`
- ✅ Verify MySQL server is running
- ✅ Test connection: `mysql -h localhost -u username -p`

**Problem**: "SQLSTATE[HY000] [2002] Connection refused"
- ✅ Ensure MySQL is running: `sudo systemctl status mysql`
- ✅ Check host is correct (localhost vs 127.0.0.1)
- ✅ Verify firewall settings

### Session Issues

**Problem**: Session expires too quickly
- Edit `SESSION_TIMEOUT` in `session.php`
- Check PHP session configuration: `session.gc_maxlifetime`

**Problem**: "Session expired" after refresh
- Clear browser cookies
- Check PHP session save path has write permissions
- Verify `session_start()` is called before output

### Frontend Issues

**Problem**: Charts not displaying
- Check browser console for errors
- Verify Chart.js CDN is accessible
- Ensure data format is correct

**Problem**: DataTables not working
- Check jQuery and DataTables CDN links
- Verify table structure matches DataTables requirements
- Check for JavaScript errors

### Data Issues

**Problem**: Lookup tables are empty
- Database seeds automatically on first run
- Force re-seed: Drop tables and refresh page
- Check `seedLookupTables()` function in `database.php`

**Problem**: GPS coordinates not saving
- Ensure format is correct: "40.7128,-74.0060"
- Check database column allows NULL or empty values

## 📊 Database Schema

### Tables Created Automatically

**Lookup Tables**:
- `weather` - Weather conditions (Sunny, Rainy, Cloudy, etc.)
- `time_of_day` - Time periods (Morning, Afternoon, Evening, Night)
- `surface_cond` - Surface conditions (Dry, Wet, Icy, Snowy)
- `road_cond` - Road conditions (Good, Fair, Poor, Under Construction)
- `driver_health` - Health statuses (Excellent, Good, Fair, Poor, Ill)
- `maneouvring` - Maneouvring list (Lane Change, Overtaking, etc.)

**Data Tables**:
- `trips` - Main trip records with all details
- `trip_maneouvring` - Many-to-many relationship for maneouvring

## 🎨 UI Customization

### Changing Colors

Edit CSS variables in `view.php`:
```css
:root {
  --pri: #7C8CFF;      /* Primary color */
  --pri-2: #5DE1FF;    /* Secondary color */
  --bg: #0F1419;       /* Background */
  --fg: #E8ECF2;       /* Foreground text */
}
```

### Modifying Animations

Speed up/slow down animations:
```css
.kpi-card { animation: floatKPI 6s ease-in-out infinite; }
/* Change 6s to 3s for faster animation */
```

### Theme Toggle

Default theme is set in JavaScript:
```javascript
const currentTheme = localStorage.getItem('theme') || 'dark';
```

## 📝 License

This project is for educational purposes. Feel free to use and modify as needed.

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -m "Add feature"`
4. Push to branch: `git push origin feature-name`
5. Open a Pull Request

## 📧 Support

For issues, questions, or suggestions:
- Open an issue on GitHub
- Check existing documentation
- Review troubleshooting section

---

**Version**: 1.0  
**Architecture**: MVC with Repository Pattern  
**PHP Version**: 8.0+  
**Last Updated**: December 2025
