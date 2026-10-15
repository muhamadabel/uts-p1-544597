# UTS Konstruksi & Evolusi Perangkat Lunak (SVPL261501)
**Nama:** Muhammad Abel Abhinaya Riananto  
**NIM:** 24/544597/SV/25468 (NIU: 544597)  
**Program Studi:** D4 Teknologi Rekayasa Perangkat Lunak  
**Departemen:** Teknik Elektro dan Informatika, Sekolah Vokasi, Universitas Gadjah Mada  
**Tema:** Sistem Inventaris Peralatan Produksi Multimedia & Laboratorium

---

## Deskripsi Sistem
Sistem manajemen inventaris alat produksi film berbasis arsitektur client-server decoupled:
- **Backend:** Laravel 11 dengan autentikasi token Laravel Sanctum dan server aplikasi modern FrankenPHP (Caddy engine).
- **Frontend:** Vue.js 3 + Vite dengan Axios interceptor dan reverse proxy Nginx.
- **Database:** MySQL 8.0 dengan Docker named volume persistence.
- **CI/CD:** GitHub Actions dengan automated test Laravel, build Vue.js, dan continuous deployment ke GitHub Container Registry (GHCR).

---

## Menjalankan Sistem (Docker Compose)

### 1. Salin Konfigurasi Environment
```bash
cp .env.example .env
```

### 2. Jalankan Seluruh Container
```bash
docker compose up -d --build
```

### 3. Migrasi & Seeding Data Awal
```bash
docker compose exec backend php artisan migrate:fresh --seed --force
```

### 4. Akses Aplikasi
- **Frontend Web UI:** [http://localhost:3000](http://localhost:3000)
- **Backend API:** [http://localhost:8000/api](http://localhost:8000/api)
- **Akun Default Admin:**
  - Email: `admin@hongfilms.com`
  - Password: `password123`

---

## Pengujian Lokal

### Backend Tests (10 Feature Tests)
```bash
cd backend
php artisan test
```

### Frontend Tests (Vitest)
```bash
cd frontend
npm test
```
