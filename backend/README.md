# evolusi-pl-544597

Aplikasi web sederhana (Laravel + Vue 3) untuk praktikum **Konstruksi & Evolusi Perangkat Lunak**.

> **PENTING — baca dulu.** Deadline semua tugas sudah lewat saat repo ini disiapkan.
> Langkah pertama: hubungi dosen (Galih Damaraji) dan minta izin ngumpul telat.
> Tanpa izin, kerjaan di repo ini belum tentu dinilai.

## Isi repo

| Berkas | Fungsi |
|---|---|
| `app/`, `routes/`, `resources/views/` | Aplikasi Laravel: CRUD satu tabel `tugas` + endpoint JSON `/api/tugas` |
| `frontend/` | Aplikasi Vue 3 (router, halaman daftar tugas + tentang, unit test Vitest) |
| `.github/workflows/ci.yml` | Pipeline backend (build → test → staging → production) + frontend (lint → test → build → deploy) |
| `deploy.sh` | Tujuh langkah deploy (Pertemuan 03, slide 6), dengan `set -e` |
| `Dockerfile`, `.dockerignore` | Container Laravel dengan urutan cache yang benar |

## Tahap 0 — Akun & repo (Tugas 1, wajib pakai akunmu sendiri)

1. Cek email GitHub kamu, cari undangan organisasi **KEPL2026**, klik Accept.
2. Bikin repo **baru** di akun kamu, nama persis: `evolusi-pl-544597` (ganti NIM), visibilitas **Public**
   (supaya kuota GitHub Actions gratis).
3. Push kode dari repo ini ke situ dengan riwayat commit Conventional Commits, minimal 5, contoh urutan:
   - `feat: inisialisasi aplikasi Laravel dengan CRUD tugas`
   - `feat: tambah endpoint JSON /api/tugas untuk frontend`
   - `ci: tambah workflow build dan test`
   - `feat: tambah frontend Vue 3 dengan router`
   - `ci: tambah pipeline staging dan production`
   - `build: tambah Dockerfile dan .dockerignore`
4. **Jangan pernah push langsung ke `main`.** Pola yang benar:
   - bikin branch `dev` dari `main`
   - bikin `feature/<sesuatu>` dari `dev`
   - buka Pull Request `feature/<sesuatu>` → `dev`, merge
   - buka Pull Request `dev` → `main`, merge
5. Pasang branch protection: **Settings → Branches → Add branch ruleset** untuk `main` dan `dev`
   (centang "Require a pull request before merging", tidak boleh bypass).
6. Tambahkan dosen sebagai **collaborator** dengan peran **Read**: **Settings → Collaborators**.

## Tahap 1 — Jalankan Laravel di laptop

```bash
composer install          # juga menghasilkan composer.lock — WAJIB di-commit
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # atau atur DB di .env
php artisan migrate
php artisan serve         # http://localhost:8000
php artisan test          # HARUS hijau semua sebelum lanjut
```

## Tahap 2 — Jalankan Vue di laptop

```bash
cd frontend
cp .env.example .env      # sesuaikan VITE_API_URL=http://localhost:8000
npm install
npm run dev               # http://localhost:5173
npm test                  # unit test Vitest, jalan tanpa Laravel
npm run lint
npm run build
```

## Tahap 3 — Bukti pipeline (screenshot dari tab Actions di repo kamu)

- Push ke branch fitur → pipeline hijau untuk build & test, job `production` **skipped**.
- Push/merge ke `main` → semua job jalan, `production` menunggu approval reviewer.
- Gagalkan satu test sengaja (mis. ubah ekspektasi di test) → pipeline **merah**,
  screenshot, lalu perbaiki dan screenshot hijau lagi.

## Tahap 4 — Docker (Tugas 4)

```bash
docker build -t evolusi-pl .
# build 1: penuh (composer install jalan)
docker build -t evolusi-pl .   # build 2: semua layer dari cache
# ubah SATU huruf di kode aplikasi, lalu:
docker build -t evolusi-pl .   # build 3: harus JAUH lebih cepat — screenshot waktunya
docker run -p 8000:8000 -e APP_KEY=<hasil key:generate> evolusi-pl
docker ps    # screenshot bersama browser http://localhost:8000
```

Uji API dari luar container dengan Postman: `GET http://localhost:8000/api/tugas`
— screenshot harus menunjukkan URL, status **200**, dan isi JSON.

## Tahap 5 — Laporan

Template laporan ada di folder `laporan/` (PDF dengan kotak placeholder).
Isi NIM, nama, tanggal, dan tempel screenshot bukti, lalu kumpulkan sesuai instruksi dosen.
