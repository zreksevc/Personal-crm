# 📇 Personal CRM

> Aplikasi manajemen kontak & relasi berbasis web — dibangun dengan PHP, MySQL, dan Bootstrap 5.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

---

## 📌 Deskripsi

Personal CRM adalah aplikasi web untuk mengelola kontak dan relasi bisnis secara personal. Cocok untuk **freelancer**, **sales**, **UMKM**, maupun siapapun yang perlu mengorganisir jaringan kontak mereka.

Setiap user hanya bisa melihat dan mengelola kontaknya sendiri — aman dan terisolasi per akun.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🔐 Autentikasi | Register, Login, Logout dengan session PHP |
| 📋 CRUD Kontak | Tambah, lihat, edit, hapus kontak |
| 🔍 Pencarian | Search by nama, email, atau kategori |
| 📄 Pagination | 10 kontak per halaman |
| 🏷️ Kategori | Client, Prospect, Partner, Vendor, Lainnya |
| 🛡️ Middleware | Proteksi halaman private dari akses tanpa login |
| ✅ Validasi | Frontend (JavaScript) + Backend (PHP) |
| 🔒 Keamanan | Password hashing, prepared statement, XSS protection |

---

## 🗂 Struktur Folder

```
personal-crm/
│
├── index.php                   # Entry point — auto redirect
├── database.sql                # Schema & struktur database
│
├── config/
│   └── database.php            # Konfigurasi koneksi MySQL
│
├── auth/
│   ├── login.php               # Halaman login
│   ├── register.php            # Halaman registrasi
│   └── logout.php              # Handler logout
│
├── contacts/
│   ├── index.php               # Dashboard — list + search + pagination
│   ├── create.php              # Form tambah kontak
│   ├── edit.php                # Form edit kontak
│   └── delete.php              # Handler hapus kontak
│
├── middleware/
│   └── auth.php                # Proteksi halaman private
│
├── assets/
│   ├── css/
│   │   └── style.css           # Custom styling global
│   └── js/
│       └── validation.js       # Validasi form frontend
│
└── uploads/                    # Folder foto kontak (fitur upgrade)
```

---

## 🗄 Skema Database

### Tabel `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `name` | VARCHAR(100) | Nama lengkap user |
| `email` | VARCHAR(150) | Email unik, dipakai login |
| `password` | VARCHAR(255) | Bcrypt hashed |
| `created_at` | TIMESTAMP | Waktu registrasi |

### Tabel `contacts`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | INT (PK) | Auto increment |
| `user_id` | INT (FK) | Relasi ke `users.id` |
| `nama` | VARCHAR(100) | Nama kontak |
| `no_hp` | VARCHAR(20) | Nomor HP |
| `email` | VARCHAR(150) | Email kontak |
| `kategori` | ENUM | Client / Prospect / Partner / Vendor / Lainnya |
| `alamat` | TEXT | Alamat kontak |
| `created_at` | TIMESTAMP | Waktu dibuat |

**Relasi:** `users (1) ──< contacts (many)` via `contacts.user_id`

---

## ⚙️ Cara Instalasi (XAMPP)

### Prasyarat
- [XAMPP](https://www.apachefriends.org/) (PHP 8.0+, MySQL 5.7+)
- Browser modern

### Langkah Instalasi

**1. Clone / ekstrak project**
```bash
# Opsi A — Clone dari GitHub
git clone https://github.com/username/personal-crm.git

# Opsi B — Ekstrak ZIP
# Ekstrak personal-crm.zip ke folder htdocs
```

**2. Pindahkan ke htdocs**
```
C:\xampp\htdocs\personal-crm\
```

**3. Import database**
- Buka `http://localhost/phpmyadmin`
- Klik **New** → buat database bernama `personal_crm`
- Pilih tab **Import** → pilih file `database.sql` → klik **Go**

**4. Konfigurasi database**

Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // sesuaikan
define('DB_PASS', '');           // sesuaikan
define('DB_NAME', 'personal_crm');
```

**5. Jalankan aplikasi**
```
http://localhost/personal-crm
```

---

## 🔄 Alur Sistem

```
Buka Aplikasi
     │
     ▼
Sudah login? ──── Tidak ──▶ Halaman Login ──▶ Register
     │                              │
    Ya                           Login
     │                              │
     ▼                              ▼
Dashboard ◀─────────────────── Session dibuat
     │
     ├── 🔍 Search / Filter Kontak
     ├── ➕ Tambah Kontak
     ├── ✏️  Edit Kontak
     ├── 🗑️  Hapus Kontak
     └── 🚪 Logout ──▶ Session dihapus
```

---

## 🔐 Keamanan

Beberapa lapisan keamanan yang diterapkan:

**1. Password Hashing**
```php
// Saat register
$hashed = password_hash($pass, PASSWORD_BCRYPT);

// Saat login
password_verify($input, $hashed_from_db);
```

**2. Prepared Statement** — mencegah SQL Injection
```php
$stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ? AND id = ?");
$stmt->bind_param("ii", $user_id, $id);
```

**3. Isolasi Data User** — user tidak bisa akses data user lain
```php
// Selalu tambahkan AND user_id = ? di semua query
WHERE id = ? AND user_id = ?
```

**4. XSS Protection** — semua output di-escape
```php
echo htmlspecialchars($data);
```

**5. Middleware Auth** — semua halaman private diproteksi
```php
// Di setiap halaman private
require_once '../middleware/auth.php';
```

---

## 📡 Deskripsi File Penting

### `middleware/auth.php`
Diinclude di semua halaman yang butuh login. Jika session tidak ada, otomatis redirect ke halaman login.

### `config/database.php`
Satu titik konfigurasi koneksi. Ubah kredensial di sini jika deployment ke hosting.

### `assets/js/validation.js`
Validasi frontend sebelum form dikirim:
- Cek field required
- Format email
- Panjang & kecocokan password
- Format nomor HP

---

## 🚀 Rencana Pengembangan (Upgrade)

Fitur yang bisa ditambahkan ke depannya:

- [ ] **⏰ Reminder Follow-Up** — notifikasi kontak yang perlu di-follow up hari ini
- [ ] **📤 Export CSV** — unduh semua kontak sebagai file spreadsheet
- [ ] **🖼️ Upload Foto Kontak** — foto profil per kontak
- [ ] **👥 Sharing Kontak** — bagikan kontak ke user lain
- [ ] **📊 Dashboard Statistik** — grafik jumlah kontak per kategori
- [ ] **🌙 Dark Mode** — tema gelap

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Frontend | HTML5, Bootstrap 5, Vanilla JS |
| Backend | PHP 8.0+ (Procedural) |
| Database | MySQL via MySQLi |
| Dev Tools | XAMPP, VSCode, phpMyAdmin |

---

## 📚 Skill yang Dipelajari

Dengan membangun project ini, kamu akan melatih:

- ✅ Authentication system (register, login, logout)
- ✅ Session management
- ✅ CRUD operations
- ✅ Pagination logic
- ✅ Search & filter query
- ✅ Database relasi (FK, JOIN)
- ✅ Basic security (hashing, prepared statement, XSS)
- ✅ MVC pattern dasar
- ✅ Frontend validation dengan JavaScript

---

## 🤝 Kontribusi

Pull request dan issue sangat diterima!

1. Fork repository ini
2. Buat branch baru: `git checkout -b fitur/nama-fitur`
3. Commit: `git commit -m "Tambah fitur X"`
4. Push: `git push origin fitur/nama-fitur`
5. Buat Pull Request

---

## 📄 Lisensi

Distributed under the **MIT License** — bebas digunakan, dimodifikasi, dan didistribusikan.

---

## 👨‍💻 Author

Dibuat sebagai project portfolio level freelancer awal.  
Cocok untuk dicantumkan di CV sebagai bukti kemampuan **Full-Stack PHP Development**.

---

> 💡 **Tips Deploy:** Untuk deploy ke hosting, ubah kredensial di `config/database.php`, import `database.sql` ke database hosting, dan pastikan folder `uploads/` memiliki permission `755`.
