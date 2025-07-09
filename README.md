# 📋 TaskMaster - Advanced Task Management System

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel" alt="Laravel 11.x">
    <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php" alt="PHP 8.1+">
    <img src="https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge&logo=bootstrap" alt="Bootstrap 5.3">
    <img src="https://img.shields.io/badge/MySQL-8.0+-orange?style=for-the-badge&logo=mysql" alt="MySQL 8.0+">
</p>

<p align="center">
    A modern, feature-rich task management application built with Laravel 11, featuring dual authentication systems, rich text editing, and real-time AJAX interactions.
</p>

## 🚀 Features

### 👨‍💼 Admin Features

-   **Dashboard Overview**: Real-time statistics and user management
-   **Task Assignment**: Assign tasks to users with rich text descriptions
-   **User Management**: Create, edit, and manage user accounts
-   **Task Monitoring**: View, edit, complete, and delete tasks
-   **Advanced Search**: DataTables integration with search and pagination

### 👤 User Features

-   **Personal Dashboard**: View assigned tasks and personal statistics
-   **Task Management**: Mark tasks as complete/incomplete
-   **Rich Text Viewing**: View task descriptions with formatted content
-   **Task Filtering**: Filter tasks by status and search functionality

### 🎨 Modern UI/UX

-   **Responsive Design**: Mobile-first Bootstrap 5.3 design
-   **Rich Text Editor**: TinyMCE integration for task descriptions
-   **Interactive Elements**: SweetAlert2 for beautiful notifications
-   **AJAX Operations**: Seamless user experience without page reloads
-   **DataTables**: Advanced table functionality with sorting and searching

## 🛠️ Technology Stack

-   **Backend**: Laravel 11.x
-   **Frontend**: Bootstrap 5.3, jQuery 3.6
-   **Database**: MySQL 8.0+
-   **Rich Text**: TinyMCE 6
-   **UI Components**:
    -   SweetAlert2 for notifications
    -   Select2 for enhanced dropdowns
    -   DataTables for advanced tables
    -   Bootstrap Icons

## 📋 Requirements

-   PHP 8.1 or higher
-   Composer
-   MySQL 8.0+ or MariaDB 10.3+
-   Node.js & NPM (for asset compilation)
-   Web server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/taskmaster-laravel.git
cd taskmaster-laravel
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskmaster_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Migration

```bash
# Run migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed
```

### 6. Build Assets

```bash
# Build frontend assets
npm run build

# Or for development with watching
npm run dev
```

### 7. Start the Application

```bash
# Start Laravel development server
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 👥 Default Accounts

After running migrations, you can create admin and user accounts through the application interface.

### Creating an Admin Account

1. Access the application
2. Register as a regular user
3. Update the `users` table to set `is_admin = 1` for admin access
4. Or create an admin record in the `admins` table

## 📁 Project Structure

```
taskmaster-laravel/
├── app/
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/             # Eloquent models
│   └── Policies/           # Authorization policies
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── views/             # Blade templates
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
├── routes/
│   └── web.php           # Web routes
└── public/
    ├── css/              # Compiled CSS
    └── js/               # Compiled JavaScript
```

## 🔐 Authentication System

The application features a dual authentication system:

-   **User Authentication**: Regular users with task management capabilities
-   **Admin Authentication**: Administrators with full system access

### Guards Configuration

-   `web` guard for regular users
-   `admin` guard for administrators

## 📊 Database Schema

### Core Tables

-   **users**: User accounts and profiles
-   **admins**: Administrator accounts
-   **tasks**: Task records with relationships

### Key Relationships

-   Users can have many tasks
-   Admins can assign tasks to users
-   Tasks belong to users and are assigned by admins

## 🎯 Key Features Breakdown

### AJAX Integration

-   Form submissions without page reloads
-   Real-time task status updates
-   Dynamic content loading
-   Error handling with user feedback

### Rich Text Support

-   TinyMCE editor for task descriptions
-   HTML content rendering
-   Formatted text display in modals

### Data Management

-   DataTables for enhanced table functionality
-   Search and filtering capabilities
-   Pagination and sorting
-   Responsive table design

## 🔧 Configuration

### TinyMCE Setup

The application uses TinyMCE for rich text editing with a minimal toolbar:

-   Bold, Italic, Underline
-   Bullet and numbered lists
-   Link insertion
-   Format removal
-   Undo/Redo functionality

### Database Policies

-   Task policies for authorization
-   Admin-only access controls
-   User-specific data filtering

## 🚀 Development

### Running in Development Mode

```bash
# Start the Laravel server
php artisan serve

# Watch for file changes (in another terminal)
npm run dev

# Or run Vite for hot reloading
npm run dev
```

### Code Style

The project follows Laravel coding standards and PSR-12 guidelines.

## 📝 API Endpoints

### Admin Routes

-   `GET /admin/dashboard` - Admin dashboard
-   `POST /admin/tasks/assign` - Assign new task
-   `POST /admin/tasks/{task}/edit` - Edit task
-   `POST /admin/tasks/{task}/delete` - Delete task
-   `POST /admin/users/store` - Create new user

### User Routes

-   `GET /tasks` - User task dashboard
-   `POST /tasks/{task}/complete` - Mark task complete
-   `POST /tasks/{task}/undo` - Mark task incomplete

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

-   [Laravel](https://laravel.com) - The PHP framework
-   [Bootstrap](https://getbootstrap.com) - CSS framework
-   [TinyMCE](https://www.tiny.cloud) - Rich text editor
-   [SweetAlert2](https://sweetalert2.github.io) - Beautiful alerts
-   [DataTables](https://datatables.net) - Advanced table functionality

## 📞 Support

If you encounter any issues or have questions, please [open an issue](https://github.com/yourusername/taskmaster-laravel/issues) on GitHub.

---

<p align="center">Made with ❤️ using Laravel</p>
