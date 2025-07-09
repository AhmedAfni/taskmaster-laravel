# 📋 TaskMaster - Laravel Task Manager

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge&logo=bootstrap">
</p>

> A modern task management app with admin & user dashboards, rich text editing, and real-time AJAX interactions.

---

## 📌 About the Project

**TaskMaster** is a Laravel 11-based task management system designed for teams and organizations to efficiently assign, track, and manage tasks. 
With dual authentication, real-time updates, and a clean responsive UI, it streamlines task workflows for both admins and users.

---

## ✅ Features

### 👨‍💼 Admin Panel
- View all registered users
- Assign tasks with rich text descriptions (TinyMCE)
- Edit, delete, and update any task
- Perform all actions via AJAX (no reload)

### 🙋‍♂️ User Dashboard
- View tasks assigned by admin
- Mark tasks as completed or undo them
- View task details in a formatted modal

### 🛠 Technical Highlights
- Dual Authentication (Admins & Users)
- AJAX-based interactions with real-time UI updates
- Rich text editing via TinyMCE
- Enhanced task tables with DataTables (search, sort, paginate)
- Fully responsive interface using Bootstrap 5.3

---

## 🛠 Tech Stack

- Laravel 11.x  
- MySQL  
- Bootstrap 5.3  
- jQuery + AJAX  
- TinyMCE + DataTables

---

## 📦 Installation

```bash
git clone https://github.com/yourusername/taskmaster-laravel.git
cd taskmaster-laravel
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Configure `.env` and run:

```bash
php artisan migrate
npm run build
php artisan serve
```

Visit: `http://localhost:8000`

---

## 👥 Admin Access

To create an admin:
1. Register normally via UI  
2. Update `users` table: set `is_admin = 1`  
   or insert into `admins` table directly

---

## 🔐 Auth Guards

- `web`: User access  
- `admin`: Admin access

---

## 📁 Highlights

- `/admin/dashboard` – Admin control panel  
- `/tasks` – User task list  
- AJAX for task actions: complete, undo, edit, delete  
- Rich text rendering in modals  
- SweetAlert2 for confirmation prompts

---

## 📄 License

MIT © Your Name or Org

---

<p align="center">Made with ❤️ using Laravel</p>
