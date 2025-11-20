# 📦 Panduan Backup Otomatis Laravel

## ✅ Setup Selesai!

Package **spatie/laravel-backup** sudah terinstall dan dikonfigurasi.

---

## 📋 Jadwal Backup Otomatis

### 1. **Backup Per Jam** ⏰
- **Waktu:** Setiap jam
- **Isi:** Database saja
- **Command:** `backup:run --only-db`
- **Tujuan:** Backup berkala untuk keamanan data

### 2. **Backup Harian** 📅
- **Waktu:** Setiap hari jam 02:00
- **Isi:** Database + Files
- **Command:** `backup:run`
- **Tujuan:** Full backup lengkap

### 3. **Backup Mingguan** 📆
- **Waktu:** Setiap Minggu jam 03:00
- **Isi:** Database + Files
- **Command:** `backup:run`
- **Tujuan:** Backup mingguan untuk arsip

### 4. **Backup Bulanan** 🗓️
- **Waktu:** Setiap tanggal 1 jam 04:00
- **Isi:** Database + Files
- **Command:** `backup:run`
- **Tujuan:** Backup bulanan untuk long-term storage

### 5. **Cleanup Backup Lama** 🧹
- **Waktu:** Setiap hari jam 01:00
- **Command:** `backup:clean`
- **Tujuan:** Hapus backup lama sesuai policy

### 6. **Monitor Backup** 🔍
- **Waktu:** Setiap hari jam 05:00
- **Command:** `backup:monitor`
- **Tujuan:** Cek health backup

---

## 📝 Policy Penyimpanan Backup

Sudah dikonfigurasi di `config/backup.php`:

- **7 hari pertama:** Simpan semua backup
- **16 hari berikutnya:** Simpan backup harian
- **8 minggu berikutnya:** Simpan backup mingguan
- **4 bulan berikutnya:** Simpan backup bulanan
- **2 tahun berikutnya:** Simpan backup tahunan
- **Max size:** 5000 MB (5 GB)

---

## 🚀 Cara Manual Backup

### Backup Lengkap
```bash
php artisan backup:run
```

### Backup Hanya Database
```bash
php artisan backup:run --only-db
```

### Backup Hanya Files
```bash
php artisan backup:run --only-files
```

### Backup dengan Notifikasi Disabled
```bash
php artisan backup:run --disable-notifications
```

---

## 🗂️ Lokasi Backup

Backup disimpan di:
```
storage/app/nama-app/
```

Format nama file:
```
2025-11-19-14-30-00.zip
```

---

## 🧹 Cleanup Backup

### Manual Cleanup
```bash
php artisan backup:clean
```

### Lihat Daftar Backup
```bash
php artisan backup:list
```

---

## 🔍 Monitor Backup

### Check Health
```bash
php artisan backup:monitor
```

Output akan menunjukkan:
- Umur backup terakhir
- Total size backup
- Status health

---

## ⚙️ Konfigurasi Tambahan

### 1. Exclude Folder/File Tertentu

Edit `config/backup.php`:

```php
'exclude' => [
    base_path('vendor'),
    base_path('node_modules'),
    base_path('storage/logs'),
    base_path('storage/framework/cache'),
    // Tambahkan folder lain yang ingin di-exclude
],
```

### 2. Backup ke Cloud Storage

#### a. Google Drive

Install package:
```bash
composer require masbug/flysystem-google-drive-ext
```

Tambahkan di `config/filesystems.php`:
```php
'google' => [
    'driver' => 'google',
    'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
    'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
    'folder' => env('GOOGLE_DRIVE_FOLDER'),
],
```

Update `config/backup.php`:
```php
'disks' => [
    'local',
    'google',
],
```

#### b. Amazon S3

Tambahkan di `.env`:
```env
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your-bucket
```

Update `config/backup.php`:
```php
'disks' => [
    'local',
    's3',
],
```

#### c. Dropbox

Install package:
```bash
composer require spatie/flysystem-dropbox
```

Konfigurasi sama seperti di atas.

### 3. Email Notification

Update `.env`:
```env
BACKUP_MAIL_TO=admin@yourdomain.com

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Password Protection

Tambahkan di `.env`:
```env
BACKUP_ARCHIVE_PASSWORD=your-strong-password
```

Backup akan di-encrypt dengan password ini.

### 5. Database Compression

Edit `config/backup.php`:
```php
'database_dump_compressor' => \Spatie\DbDumper\Compressors\GzipCompressor::class,
```

---

## 🏃 Jalankan Scheduler

### Development (Manual)
```bash
php artisan schedule:work
```

### Production (Cron Job)

Tambahkan di crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk Windows (Task Scheduler):
1. Buka Task Scheduler
2. Create Basic Task
3. Trigger: Daily, every 1 minute
4. Action: Start a program
   - Program: `php`
   - Arguments: `artisan schedule:run`
   - Start in: `D:\laragon\www\UKK-MANAGEMENT-PROYEK\website`

---

## ✅ Testing

### Test Backup
```bash
php artisan backup:run
```

Output sukses:
```
Starting backup...
Dumping database...
Creating zip archive...
Copying zip to disk(s)...
  Copying zip to disk named local...
Backup completed!
```

### Test Cleanup
```bash
php artisan backup:clean
```

### Test Monitor
```bash
php artisan backup:monitor
```

### Lihat Log
```bash
tail -f storage/logs/laravel.log
```

---

## 📊 View Scheduled Tasks

```bash
php artisan schedule:list
```

Output:
```
0 2 * * * backup:run ......... Next Due: 8 hours from now
0 1 * * * backup:clean ........ Next Due: 7 hours from now
0 5 * * * backup:monitor ...... Next Due: 11 hours from now
0 * * * * backup:run --only-db  Next Due: 23 minutes from now
```

---

## 🐛 Troubleshooting

### Error: "mysqldump not found"

**Solusi Windows:**
Tambahkan MySQL ke PATH atau set di `.env`:
```env
DB_DUMP_PATH=D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin
```

### Error: "Permission denied"

**Solusi:**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

### Backup Tidak Jalan Otomatis

**Cek:**
1. Apakah scheduler berjalan?
   ```bash
   php artisan schedule:work
   ```

2. Cek cron job (production):
   ```bash
   crontab -l
   ```

3. Lihat log:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Backup Terlalu Besar

**Solusi:**
1. Exclude folder besar di `config/backup.php`
2. Backup hanya database: `--only-db`
3. Enable compression:
   ```php
   'database_dump_compressor' => \Spatie\DbDumper\Compressors\GzipCompressor::class,
   ```

---

## 📞 Commands Reference

| Command | Deskripsi |
|---------|-----------|
| `php artisan backup:run` | Full backup |
| `php artisan backup:run --only-db` | Backup database |
| `php artisan backup:run --only-files` | Backup files |
| `php artisan backup:clean` | Cleanup old backups |
| `php artisan backup:list` | List all backups |
| `php artisan backup:monitor` | Check backup health |
| `php artisan schedule:list` | View scheduled tasks |
| `php artisan schedule:work` | Run scheduler (dev) |

---

## 🔐 Security Best Practices

1. **Password Protect Archives**
   ```env
   BACKUP_ARCHIVE_PASSWORD=strong-password-here
   ```

2. **Backup ke Cloud Storage** (tidak hanya local)

3. **Rotate Backups** (sudah di-setup otomatis)

4. **Monitor Backup Health** (sudah scheduled)

5. **Test Restore Regularly**
   ```bash
   # Extract backup
   unzip storage/app/nama-app/2025-11-19-14-30-00.zip
   
   # Import database
   mysql -u root -p database_name < db-dumps/mysql-database_name.sql
   ```

6. **Secure .env**
   ```bash
   chmod 600 .env
   ```

---

## 📚 Resources

- [Spatie Laravel Backup Docs](https://spatie.be/docs/laravel-backup)
- [Laravel Task Scheduling](https://laravel.com/docs/scheduling)
- [DB Dumper Package](https://github.com/spatie/db-dumper)

---

## ✨ Custom Backup Schedule

Jika ingin mengubah jadwal, edit `routes/console.php`:

```php
// Backup setiap 6 jam
Schedule::command('backup:run --only-db')
    ->cron('0 */6 * * *');

// Backup setiap Senin & Jumat jam 10 malam
Schedule::command('backup:run')
    ->days([1, 5])
    ->at('22:00');

// Backup setiap 15 menit (testing)
Schedule::command('backup:run --only-db')
    ->everyFifteenMinutes();
```

---

**🎉 Setup Selesai! Backup Otomatis Sudah Berjalan!**

*Last Updated: November 19, 2025*
