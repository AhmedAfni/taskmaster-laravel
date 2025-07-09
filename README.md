# 📋 TaskMaster - Task Management System

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel" alt="Laravel 11.x">
    <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php" alt="PHP 8.1+">
    <img src="https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge&logo=bootstrap" alt="Bootstrap 5.3">
</p>

<p align="center">
    A modern task management application built with Laravel 11, featuring dual authentication, rich text editing, and AJAX interactions.
</p>

## ✨ Features

-   **Admin Dashboard**: Manage users and assign tasks with rich text descriptions
-   **User Dashboard**: View and manage assigned tasks
-   **Rich Text Editor**: TinyMCE integration for detailed task descriptions
-   **Real-time Updates**: AJAX operations without page reloads
-   **Responsive Design**: Mobile-friendly Bootstrap interface
-   **Advanced Tables**: Search, sort, and paginate with DataTables

## 🛠️ Tech Stack

-   **Laravel 11.x** - Backend framework
-   **Bootstrap 5.3** - Frontend styling
-   **MySQL** - Database
-   **TinyMCE** - Rich text editor
-   **jQuery** - JavaScript interactions

## 🚀 Quick Start

### Requirements

-   PHP 8.1+
-   Composer
-   MySQL
-   Node.js & NPM

### Installation

1. **Clone and install**

```bash
git clone https://github.com/yourusername/taskmaster-laravel.git
cd taskmaster-laravel
composer install
npm install
```

2. **Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure database**
   Edit `.env` with your database credentials:

```env
DB_DATABASE=taskmaster_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. **Run migrations and start**

```bash
php artisan migrate
npm run build
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## � Usage

### Admin Access

1. Register as a user
2. Update the `users` table: set `is_admin = 1`
3. Access admin dashboard at `/admin`

### User Access

-   Register/login to view assigned tasks
-   Mark tasks complete/incomplete
-   View rich text task descriptions

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

<p align="center">Made with ❤️ using Laravel</p>
