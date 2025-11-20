-- ============================================================
-- ROLLBACK SCRIPT - HAPUS SEMUA TRIGGERS
-- Database: MySQL/MariaDB
-- Generated: 2025-11-19
-- ============================================================

-- Jalankan script ini untuk menghapus semua triggers yang telah dibuat

-- ============================================================
-- 1. DROP TRIGGERS UNTUK TABEL USERS
-- ============================================================
DROP TRIGGER IF EXISTS before_users_update;
DROP TRIGGER IF EXISTS before_users_delete;

-- ============================================================
-- 2. DROP TRIGGERS UNTUK TABEL PROJECTS
-- ============================================================
DROP TRIGGER IF EXISTS before_projects_insert;
DROP TRIGGER IF EXISTS before_projects_update;
DROP TRIGGER IF EXISTS after_projects_delete;

-- ============================================================
-- 3. DROP TRIGGERS UNTUK TABEL BOARDS
-- ============================================================
DROP TRIGGER IF EXISTS before_boards_insert;
DROP TRIGGER IF EXISTS after_boards_delete;

-- ============================================================
-- 4. DROP TRIGGERS UNTUK TABEL PROJECT_MEMBERS
-- ============================================================
DROP TRIGGER IF EXISTS before_project_members_insert;
DROP TRIGGER IF EXISTS after_project_members_insert;
DROP TRIGGER IF EXISTS after_project_members_delete;

-- ============================================================
-- 5. DROP TRIGGERS UNTUK TABEL CARDS
-- ============================================================
DROP TRIGGER IF EXISTS before_cards_insert;
DROP TRIGGER IF EXISTS before_cards_update;
DROP TRIGGER IF EXISTS after_cards_update_calculate_hours;

-- ============================================================
-- 6. DROP TRIGGERS UNTUK TABEL CARD_ASSIGNMENTS
-- ============================================================
DROP TRIGGER IF EXISTS before_card_assignments_insert;
DROP TRIGGER IF EXISTS before_card_assignments_update;
DROP TRIGGER IF EXISTS before_card_assignments_insert_validate;

-- ============================================================
-- 7. DROP TRIGGERS UNTUK TABEL SUBTASKS
-- ============================================================
DROP TRIGGER IF EXISTS before_subtasks_insert;
DROP TRIGGER IF EXISTS after_subtasks_update;
DROP TRIGGER IF EXISTS after_subtasks_update_calculate_hours;

-- ============================================================
-- 8. DROP TRIGGERS UNTUK TABEL TIME_LOGS
-- ============================================================
DROP TRIGGER IF EXISTS before_time_logs_insert;
DROP TRIGGER IF EXISTS before_time_logs_update;
DROP TRIGGER IF EXISTS after_time_logs_insert_update_card;
DROP TRIGGER IF EXISTS after_time_logs_update_update_card;
DROP TRIGGER IF EXISTS after_time_logs_delete_update_card;
DROP TRIGGER IF EXISTS after_time_logs_insert_update_subtask;
DROP TRIGGER IF EXISTS after_time_logs_update_update_subtask;
DROP TRIGGER IF EXISTS after_time_logs_delete_update_subtask;

-- ============================================================
-- 9. DROP TRIGGERS UNTUK TABEL COMMENTS
-- ============================================================
DROP TRIGGER IF EXISTS before_comments_insert;

-- ============================================================
-- VERIFIKASI PENGHAPUSAN
-- ============================================================
-- Jalankan query berikut untuk memastikan semua triggers sudah terhapus:
-- 
-- SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE 
-- FROM information_schema.TRIGGERS 
-- WHERE TRIGGER_SCHEMA = DATABASE()
-- ORDER BY EVENT_OBJECT_TABLE, EVENT_MANIPULATION;
-- ============================================================

SELECT '✓ Semua triggers berhasil dihapus!' AS status;
