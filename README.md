# 📋 TaskMaster - Laravel Task Manager

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel" alt="Laravel 11.x">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge&logo=bootstrap" alt="Bootstrap 5.3">
  <img src="https://img.shields.io/badge/MySQL-8.0+-orange?style=for-the-badge&logo=mysql" alt="MySQL 8.0+">
</p>

> A modern task management application with admin & user dashboards, rich text editing, and real-time AJAX interactions.

---

## 📌 About the Project

**TaskMaster** is a Laravel 11-based task management system designed for teams and organizations to efficiently assign, track, and manage tasks.
With dual authentication, real-time updates, and a clean responsive UI, it streamlines task workflows for both admins and users.

---

## ✅ Features

### 👨‍💼 Admin Panel

-   View all registered users
-   Assign tasks with rich text descriptions (TinyMCE)
-   Edit, delete, and update any task
-   Perform all actions via AJAX (no reload)

### 🙋‍♂️ User Dashboard

-   View tasks assigned by admin
-   Mark tasks as completed or undo them
-   View task details in a formatted modal

### 🛠 Technical Highlights

-   Dual Authentication (Admins & Users)
-   AJAX-based interactions with real-time UI updates
-   Rich text editing via TinyMCE
-   Enhanced task tables with DataTables (search, sort, paginate)
-   Fully responsive interface using Bootstrap 5.3

---

## 🛠 Tech Stack

-   **Backend:** Laravel 11.x
-   **Database:** MySQL 8.0+
-   **Frontend:** Bootstrap 5.3, jQuery
-   **Rich Text:** TinyMCE 6
-   **UI Components:** DataTables, SweetAlert2, Select2

---

## 📋 Requirements

-   PHP 8.1 or higher
-   Composer
-   MySQL 8.0+ or MariaDB 10.3+
-   Node.js & NPM
-   Web server (Apache/Nginx)

---

## 📦 Installation

### 1. Clone and Install Dependencies

```bash
git clone https://github.com/yourusername/taskmaster-laravel.git
cd taskmaster-laravel
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskmaster_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations and Build Assets

```bash
php artisan migrate
npm run build
php artisan serve
```

**Visit:** `http://localhost:8000`

---

## 👥 Admin Access

To create an admin account:

1. Register as a regular user through the application
2. Update the `users` table: set `is_admin = 1` for the user
3. Access admin dashboard at `/admin/dashboard`

**Alternative:** Create a record directly in the `admins` table

---

## � Usage

### Admin Features

-   **Dashboard:** `/admin/dashboard` - Complete task and user management
-   **Task Assignment:** Assign tasks with rich text descriptions using TinyMCE
-   **User Management:** Create, edit, and manage user accounts
-   **Task Operations:** View, edit, complete, and delete tasks via AJAX

### User Features

-   **Dashboard:** `/tasks` - View assigned tasks and personal statistics
-   **Task Management:** Mark tasks as complete/incomplete
-   **Rich Text Viewing:** View formatted task descriptions in modals

---

## 🔐 Authentication System

The application uses Laravel's multi-guard authentication:

-   **`web` guard:** Regular user access
-   **`admin` guard:** Administrator access with full system control

---

## 🎯 Key Features

-   **AJAX Operations:** All form submissions without page reloads
-   **Rich Text Support:** TinyMCE editor with HTML content rendering
-   **DataTables Integration:** Enhanced tables with search, sort, and pagination
-   **Responsive Design:** Mobile-first Bootstrap 5.3 interface
-   **Real-time Updates:** Dynamic content loading with user feedback

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](LICENSE).

---

<p align="center">Made with ❤️ using Laravel</p>
