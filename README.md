# 📔 Yunman's Diariy

A lightweight, elegant personal diary website built with PHP and SQLite.

![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-%3E%3D7.2-blue)
![Framework](https://img.shields.io/badge/CodeIgniter-3.x-orange)

---

## ✨ Features

**Frontend**
- 📅 Timeline view with monthly grouping
- 📖 Book-style modal for reading entries
- 🔍 Full-text search by title and tags
- 🏷️ Tag cloud with entry counts
- 📂 Monthly archive sidebar
- 🔐 Optional site-wide password gate
- 📱 Fully responsive (Tailwind CSS)

**Admin Panel**
- ✏️ Markdown editor (SimpleMDE) for writing entries
- 😊 Mood & weather tagging per entry
- 📋 Entry list with pagination and batch delete
- ⚙️ Configurable site name, description, ICP备案号
- 🔑 Admin password management
- 🛡️ CAPTCHA on login (GD library)

**Under the Hood**
- SQLite database — zero configuration, single file
- bcrypt password hashing
- CSRF protection enabled
- Parsedown for secure Markdown rendering

---

## 📋 Requirements

| Component | Minimum Version |
|-----------|----------------|
| PHP | 7.2+ |
| PHP Extensions | `pdo`, `pdo_sqlite`, `gd` (recommended for CAPTCHA) |
| Web Server | Apache with `mod_rewrite`, or PHP built-in server |

---

## 🚀 Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/your-username/riji.git
cd riji
```

### 2. Run the installer

Start your web server:

```bash
# PHP built-in server
php -S localhost:8080

# Or place under Apache/Nginx document root
```

Visit `http://localhost:8080/install.php` and follow the 3-step wizard.

### 3. Delete the installer

```bash
rm install.php   # Linux / macOS
del install.php  # Windows
```

### 4. Access the site

| URL | Description |
|-----|-------------|
| `/` | Frontend timeline |
| `/admin` | Admin login |
| `/gate` | Site password gate |

---

## 🔐 Default Credentials

| Account | Username | Password |
|---------|----------|----------|
| Admin Panel | `admin` | `admin123` |
| Site Access | — | `Yunman_diariy_2026` |

> ⚠️ **Change all default passwords immediately** via Admin → Site Settings.

---

## 📁 Project Structure

```
riji/
├── app/
│   ├── config/              # CI3 configuration files
│   │   ├── autoload.php     # Auto-loaded libraries & helpers
│   │   ├── config.php       # Base URL, encryption, session, CSRF
│   │   ├── database.php     # SQLite PDO connection
│   │   └── routes.php       # URL routing rules
│   ├── controllers/
│   │   ├── Home.php         # Frontend: index, detail, search, tag, gate
│   │   └── Admin.php        # Backend: login, CRUD, settings, captcha
│   ├── helpers/
│   │   └── riji_helper.php  # Mood/weather emoji, date formatting
│   ├── libraries/
│   │   └── Parsedown.php    # Markdown parser (safe mode)
│   ├── models/
│   │   ├── Diary_model.php  # Entry queries (CRUD, search, archive, tags)
│   │   ├── Setting_model.php# Key-value settings store
│   │   └── Admin_model.php  # Admin authentication
│   └── views/
│       ├── templates/       # Layout: header, footer, sidebar, admin shell
│       ├── home/            # index, detail, search, password_gate
│       └── admin/           # login, dashboard, diary_list, diary_form, settings
├── public/
│   └── data/                # SQLite database (auto-created)
├── sys/                     # CodeIgniter 3 system core
├── docs/                    # Requirements & code review (not tracked)
├── .htaccess                # URL rewriting
├── .gitignore
├── README.md
└── index.php                # Entry point
```

---

## ⚙️ Configuration

### Site Settings (via Admin Panel)

| Setting | Default | Description |
|---------|---------|-------------|
| Site Name | Yunman's Diariy | Displayed in header & title |
| Description | 记录生活的点点滴滴 | Meta description |
| Entries Per Page | 10 | Pagination limit |
| ICP备案号 | (empty) | Footer ICP number |

### Environment

Rename or copy `.env.example` to `.env` for environment-specific overrides:

```env
# Not currently used — reserved for future use
```

---

## 🛡️ Security

- Passwords stored as bcrypt hashes (`PASSWORD_BCRYPT`)
- CSRF protection enabled on all POST forms
- Parsedown `setSafeMode(true)` prevents XSS in Markdown
- CI3 Query Builder prevents SQL injection
- CAPTCHA on admin login prevents brute-force
- Site access password gate (optional)

---

## 🧑‍💻 Development

```bash
# Run with PHP built-in server
php -S localhost:8080

# Run installer
http://localhost:8080/install.php

# Access admin
http://localhost:8080/index.php/admin
```

### Adding Seed Data

Seed data can be inserted directly into `public/data/riji.db` or via the admin panel.

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

Built with ❤️ and ☕
