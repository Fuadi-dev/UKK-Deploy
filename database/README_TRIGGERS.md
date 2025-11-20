# Database Triggers Documentation

## 📋 Daftar File

1. **`triggers.sql`** - File utama yang berisi semua trigger definitions
2. **`triggers_rollback.sql`** - File untuk menghapus semua triggers
3. **`README_TRIGGERS.md`** - Dokumentasi penggunaan (file ini)

## 🚀 Cara Install Triggers

### Langkah 1: Masuk ke Database
```bash
# Via MySQL CLI
mysql -u root -p nama_database

# Atau buka phpMyAdmin dan pilih database
```

### Langkah 2: Jalankan Script Install
```sql
-- Pilih database terlebih dahulu
USE nama_database;

-- Jalankan script triggers
SOURCE D:/laragon/www/UKK-MANAGEMENT-PROYEK/website/database/triggers.sql;

-- Atau copy-paste isi file triggers.sql ke SQL query
```

### Langkah 3: Verifikasi Instalasi
```sql
-- Lihat semua triggers yang terinstall
SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE 
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE()
ORDER BY EVENT_OBJECT_TABLE, EVENT_MANIPULATION;
```

## 🔄 Cara Rollback (Hapus Triggers)

### Opsi 1: Menggunakan Script Rollback
```sql
-- Pilih database
USE nama_database;

-- Jalankan script rollback
SOURCE D:/laragon/www/UKK-MANAGEMENT-PROYEK/website/database/triggers_rollback.sql;
```

### Opsi 2: Manual via phpMyAdmin
1. Buka phpMyAdmin
2. Pilih database
3. Klik tab "Triggers"
4. Hapus trigger satu per satu dengan tombol "Drop"

### Opsi 3: Drop Semua Triggers via Query
```sql
-- Copy-paste isi file triggers_rollback.sql
```

## 📊 Ringkasan Triggers

### 1. Users Table (2 triggers)
- ✅ `before_users_update` - Validasi email format
- ✅ `before_users_delete` - Cegah hapus admin terakhir

### 2. Projects Table (3 triggers)
- ✅ `before_projects_insert` - Auto-generate slug
- ✅ `before_projects_update` - Auto-update status expired
- ✅ `after_projects_delete` - Logging

### 3. Boards Table (2 triggers)
- ✅ `before_boards_insert` - Auto-set position
- ✅ `after_boards_delete` - Reorder positions

### 4. Project Members Table (3 triggers)
- ✅ `before_project_members_insert` - Set joined_at
- ✅ `after_project_members_insert` - Update user status 'working'
- ✅ `after_project_members_delete` - Update user status 'free'

### 5. Cards Table (3 triggers)
- ✅ `before_cards_insert` - Auto-generate slug & position
- ✅ `before_cards_update` - Set started_at
- ✅ `after_cards_update_calculate_hours` - Calculate actual_hours

### 6. Card Assignments Table (3 triggers)
- ✅ `before_card_assignments_insert` - Set assigned_at
- ✅ `before_card_assignments_update` - Set started_at & completed_at
- ✅ `before_card_assignments_insert_validate` - Validasi duplikasi

### 7. Subtasks Table (3 triggers)
- ✅ `before_subtasks_insert` - Auto-set position
- ✅ `after_subtasks_update` - Update card status
- ✅ `after_subtasks_update_calculate_hours` - Calculate actual_hours

### 8. Time Logs Table (9 triggers)
- ✅ `before_time_logs_insert` - Validasi & calculate duration
- ✅ `before_time_logs_update` - Recalculate duration
- ✅ `after_time_logs_insert_update_card` - Update card hours
- ✅ `after_time_logs_update_update_card` - Update card hours
- ✅ `after_time_logs_delete_update_card` - Update card hours
- ✅ `after_time_logs_insert_update_subtask` - Update subtask hours
- ✅ `after_time_logs_update_update_subtask` - Update subtask hours
- ✅ `after_time_logs_delete_update_subtask` - Update subtask hours

### 9. Comments Table (1 trigger)
- ✅ `before_comments_insert` - Validasi comment

**Total: 29 Triggers**

## 🔍 Testing Triggers

### Test 1: Auto-generate Slug
```sql
-- Insert project tanpa slug
INSERT INTO projects (user_id, project_name, description) 
VALUES (1, 'Test Project', 'Description');

-- Cek hasilnya (slug harus tergenerate otomatis)
SELECT id, slug, project_name FROM projects ORDER BY id DESC LIMIT 1;
```

### Test 2: Auto-set Position
```sql
-- Insert board tanpa position
INSERT INTO boards (project_id, board_name) 
VALUES (1, 'Test Board');

-- Cek hasilnya (position harus terisi otomatis)
SELECT id, board_name, position FROM boards WHERE project_id = 1 ORDER BY position;
```

### Test 3: User Status Update
```sql
-- Tambah user ke project
INSERT INTO project_members (project_id, user_id, role) 
VALUES (1, 2, 'Developer');

-- Cek status user (harus 'working')
SELECT id, name, status FROM users WHERE id = 2;

-- Hapus user dari project
DELETE FROM project_members WHERE user_id = 2;

-- Cek status user (harus 'free' jika tidak di project lain)
SELECT id, name, status FROM users WHERE id = 2;
```

### Test 4: Time Log Calculation
```sql
-- Insert time log
INSERT INTO time_logs (card_id, user_id, start_time, end_time, description) 
VALUES (1, 1, '2025-11-19 09:00:00', '2025-11-19 11:30:00', 'Development');

-- Cek duration (harus 150 minutes)
SELECT id, duration_minutes FROM time_logs ORDER BY id DESC LIMIT 1;

-- Cek actual_hours di card (harus terupdate otomatis)
SELECT id, card_title, actual_hours FROM cards WHERE id = 1;
```

### Test 5: Validasi Email
```sql
-- Coba update email dengan format salah (harus error)
UPDATE users SET email = 'emailsalah' WHERE id = 1;
-- Error: Format email tidak valid

-- Update dengan email valid (harus berhasil)
UPDATE users SET email = 'valid@email.com' WHERE id = 1;
```

## ⚠️ Catatan Penting

1. **Backup Database**: Selalu backup database sebelum install triggers
2. **Testing Environment**: Test di development dulu sebelum production
3. **Performance**: Beberapa triggers melakukan kalkulasi, monitor performance
4. **Recursive Triggers**: MySQL secara default tidak mengizinkan recursive triggers
5. **Permissions**: Pastikan user database memiliki privilege `TRIGGER`

## 🐛 Troubleshooting

### Error: "Access denied; you need the TRIGGER privilege"
```sql
-- Grant privilege trigger ke user
GRANT TRIGGER ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

### Error: "Trigger already exists"
```sql
-- Hapus trigger yang bentrok
DROP TRIGGER IF EXISTS trigger_name;
-- Lalu install ulang
```

### Lihat Error di Trigger
```sql
-- Lihat detail trigger
SHOW CREATE TRIGGER trigger_name;

-- Lihat semua triggers di database
SHOW TRIGGERS;
```

### Disable Sementara Semua Triggers
```sql
-- Tidak ada cara langsung, harus drop satu-satu
-- Atau gunakan script rollback, lalu install ulang nanti
```

## 📝 Changelog

### Version 1.0 (2025-11-19)
- ✅ Initial release
- ✅ 29 triggers untuk 9 tabel
- ✅ Auto-generate slug dan position
- ✅ Auto-calculate hours dari time logs
- ✅ Validasi data integrity
- ✅ Status management otomatis

## 📞 Support

Jika ada masalah atau pertanyaan:
1. Cek error log MySQL: `/var/log/mysql/error.log`
2. Review trigger definition: `SHOW CREATE TRIGGER trigger_name`
3. Cek dokumentasi MySQL: https://dev.mysql.com/doc/refman/8.0/en/triggers.html

---

**Developed for UKK Management Proyek System**  
*Last Updated: November 19, 2025*
