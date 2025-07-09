# Contributing to TaskMaster

Thank you for your interest in contributing to TaskMaster! This document provides guidelines and instructions for contributing to the project.

## 🤝 How to Contribute

### 1. Fork the Repository

Fork the TaskMaster repository to your GitHub account.

### 2. Clone Your Fork

```bash
git clone https://github.com/yourusername/taskmaster-laravel.git
cd taskmaster-laravel
```

### 3. Create a Branch

Create a new branch for your feature or bug fix:

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b bugfix/your-bug-fix
```

### 4. Make Your Changes

-   Follow the existing code style and conventions
-   Write clear, concise commit messages
-   Test your changes thoroughly
-   Update documentation if necessary

### 5. Run Tests

Ensure all tests pass:

```bash
php artisan test
```

### 6. Submit a Pull Request

1. Push your branch to your fork
2. Create a pull request from your fork to the main repository
3. Fill out the pull request template completely
4. Wait for code review and address any feedback

## 📋 Code Style Guidelines

### PHP Code

-   Follow PSR-12 coding standards
-   Use Laravel conventions and best practices
-   Write meaningful variable and method names
-   Add comments for complex logic

### JavaScript Code

-   Use ES6+ features where appropriate
-   Follow consistent indentation (2 spaces)
-   Use meaningful variable names
-   Add comments for complex functions

### Blade Templates

-   Use proper indentation (4 spaces)
-   Follow Laravel Blade conventions
-   Keep templates clean and readable

## 🔍 Code Review Process

1. **Automated Checks**: PRs will be automatically checked for code style and tests
2. **Manual Review**: Maintainers will review your code for:
    - Code quality and best practices
    - Security considerations
    - Performance implications
    - Documentation completeness

## 🐛 Bug Reports

When reporting bugs, please include:

-   Clear description of the issue
-   Steps to reproduce
-   Expected vs actual behavior
-   Environment details (OS, PHP version, etc.)
-   Error logs if applicable

## 💡 Feature Requests

For new features, please:

-   Check if the feature already exists or is planned
-   Describe the use case and benefits
-   Provide examples or mockups if helpful
-   Consider backward compatibility

## 🚀 Development Setup

### Prerequisites

-   PHP 8.1+
-   Composer
-   Node.js & NPM
-   MySQL 8.0+

### Setup Steps

1. Install dependencies: `composer install && npm install`
2. Copy environment file: `cp .env.example .env`
3. Generate app key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Build assets: `npm run dev`

## 📝 Commit Message Guidelines

Use clear, descriptive commit messages:

```
type: Brief description of changes

More detailed explanation if needed.

- List specific changes
- Reference issues: Fixes #123
```

### Types:

-   `feat`: New feature
-   `fix`: Bug fix
-   `docs`: Documentation changes
-   `style`: Code style changes
-   `refactor`: Code refactoring
-   `test`: Adding or updating tests
-   `chore`: Maintenance tasks

## ❓ Questions?

If you have questions about contributing, please:

1. Check existing issues and discussions
2. Create a new issue with the `question` label
3. Reach out to maintainers

## 📄 License

By contributing to TaskMaster, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to TaskMaster! 🎉
