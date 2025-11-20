-- ============================================================
-- TRIGGERS UNTUK SISTEM MANAJEMEN PROYEK
-- Database: MySQL/MariaDB
-- Generated: 2025-11-19
-- ============================================================

-- ============================================================
-- 1. TRIGGERS UNTUK TABEL USERS
-- ============================================================

-- Trigger untuk mencatat perubahan email user
DELIMITER $$
CREATE TRIGGER before_users_update
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    -- Validasi email format
    IF NEW.email NOT LIKE '%@%.%' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Format email tidak valid';
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Trigger untuk mencegah penghapusan admin terakhir
DELIMITER $$
CREATE TRIGGER before_users_delete
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    DECLARE admin_count INT;
    
    IF OLD.role = 'admin' THEN
        SELECT COUNT(*) INTO admin_count FROM users WHERE role = 'admin' AND deleted_at IS NULL;
        
        IF admin_count <= 1 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tidak dapat menghapus admin terakhir';
        END IF;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 2. TRIGGERS UNTUK TABEL PROJECTS
-- ============================================================

-- Trigger untuk auto-generate slug saat insert
DELIMITER $$
CREATE TRIGGER before_projects_insert
BEFORE INSERT ON projects
FOR EACH ROW
BEGIN
    IF NEW.slug IS NULL OR NEW.slug = '' THEN
        SET NEW.slug = CONCAT(
            LOWER(REPLACE(REPLACE(NEW.project_name, ' ', '-'), '.', '')),
            '-',
            SUBSTRING(MD5(RAND()), 1, 8)
        );
    END IF;
    
    -- Set default status jika null
    IF NEW.status IS NULL THEN
        SET NEW.status = 'active';
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update status project berdasarkan deadline
DELIMITER $$
CREATE TRIGGER before_projects_update
BEFORE UPDATE ON projects
FOR EACH ROW
BEGIN
    -- Auto-update status menjadi expired jika deadline terlewat
    IF NEW.deadline < CURDATE() AND NEW.status = 'active' THEN
        SET NEW.status = 'expired';
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Trigger untuk mencatat penghapusan project
DELIMITER $$
CREATE TRIGGER after_projects_delete
AFTER DELETE ON projects
FOR EACH ROW
BEGIN
    -- Log activity bisa ditambahkan di sini
    -- INSERT INTO activity_logs (...) VALUES (...);
    NULL;
END$$
DELIMITER ;

-- ============================================================
-- 3. TRIGGERS UNTUK TABEL BOARDS
-- ============================================================

-- Trigger untuk auto-set position board
DELIMITER $$
CREATE TRIGGER before_boards_insert
BEFORE INSERT ON boards
FOR EACH ROW
BEGIN
    DECLARE max_pos INT;
    
    -- Set position otomatis jika null
    IF NEW.position IS NULL THEN
        SELECT COALESCE(MAX(position), 0) + 1 INTO max_pos
        FROM boards
        WHERE project_id = NEW.project_id;
        
        SET NEW.position = max_pos;
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update position board lainnya saat delete
DELIMITER $$
CREATE TRIGGER after_boards_delete
AFTER DELETE ON boards
FOR EACH ROW
BEGIN
    -- Reorder position boards yang tersisa
    UPDATE boards
    SET position = position - 1
    WHERE project_id = OLD.project_id AND position > OLD.position;
END$$
DELIMITER ;

-- ============================================================
-- 4. TRIGGERS UNTUK TABEL PROJECT_MEMBERS
-- ============================================================

-- Trigger untuk set joined_at otomatis
DELIMITER $$
CREATE TRIGGER before_project_members_insert
BEFORE INSERT ON project_members
FOR EACH ROW
BEGIN
    IF NEW.joined_at IS NULL THEN
        SET NEW.joined_at = CURRENT_TIMESTAMP;
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update user status saat join/leave project
DELIMITER $$
CREATE TRIGGER after_project_members_insert
AFTER INSERT ON project_members
FOR EACH ROW
BEGIN
    -- Update user status menjadi 'working'
    UPDATE users
    SET status = 'working'
    WHERE id = NEW.user_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_project_members_delete
AFTER DELETE ON project_members
FOR EACH ROW
BEGIN
    DECLARE member_count INT;
    
    -- Cek apakah user masih ada di project lain
    SELECT COUNT(*) INTO member_count
    FROM project_members
    WHERE user_id = OLD.user_id;
    
    -- Jika tidak ada di project manapun, set status ke 'free'
    IF member_count = 0 THEN
        UPDATE users
        SET status = 'free'
        WHERE id = OLD.user_id;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 5. TRIGGERS UNTUK TABEL CARDS
-- ============================================================

-- Trigger untuk auto-generate slug dan set position
DELIMITER $$
CREATE TRIGGER before_cards_insert
BEFORE INSERT ON cards
FOR EACH ROW
BEGIN
    DECLARE max_pos INT;
    
    -- Generate slug jika null
    IF NEW.slug IS NULL OR NEW.slug = '' THEN
        SET NEW.slug = CONCAT(
            LOWER(REPLACE(REPLACE(NEW.card_title, ' ', '-'), '.', '')),
            '-',
            SUBSTRING(MD5(RAND()), 1, 8)
        );
    END IF;
    
    -- Set position otomatis
    IF NEW.position IS NULL THEN
        SELECT COALESCE(MAX(position), 0) + 1 INTO max_pos
        FROM cards
        WHERE board_id = NEW.board_id;
        
        SET NEW.position = max_pos;
    END IF;
    
    -- Set default status
    IF NEW.status IS NULL THEN
        SET NEW.status = 'todo';
    END IF;
    
    -- Initialize actual_hours
    IF NEW.actual_hours IS NULL THEN
        SET NEW.actual_hours = 0;
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update started_at saat status berubah
DELIMITER $$
CREATE TRIGGER before_cards_update
BEFORE UPDATE ON cards
FOR EACH ROW
BEGIN
    -- Set started_at saat status berubah ke in_progress
    IF OLD.status != 'in_progress' AND NEW.status = 'in_progress' THEN
        IF NEW.started_at IS NULL THEN
            SET NEW.started_at = CURRENT_TIMESTAMP;
        END IF;
    END IF;
    
    -- Validasi actual_hours tidak negatif
    IF NEW.actual_hours < 0 THEN
        SET NEW.actual_hours = 0;
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Trigger untuk recalculate actual_hours dari time_logs
DELIMITER $$
CREATE TRIGGER after_cards_update_calculate_hours
AFTER UPDATE ON cards
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    -- Calculate total hours dari time_logs
    SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
    FROM time_logs
    WHERE card_id = NEW.id;
    
    -- Update actual_hours jika berbeda
    IF total_hours != NEW.actual_hours THEN
        UPDATE cards
        SET actual_hours = total_hours
        WHERE id = NEW.id;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 6. TRIGGERS UNTUK TABEL CARD_ASSIGNMENTS
-- ============================================================

-- Trigger untuk set assigned_at otomatis
DELIMITER $$
CREATE TRIGGER before_card_assignments_insert
BEFORE INSERT ON card_assignments
FOR EACH ROW
BEGIN
    IF NEW.assigned_at IS NULL THEN
        SET NEW.assigned_at = CURRENT_TIMESTAMP;
    END IF;
    
    -- Set default status
    IF NEW.assignment_status IS NULL THEN
        SET NEW.assignment_status = 'pending_confirmation';
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update timestamp berdasarkan status
DELIMITER $$
CREATE TRIGGER before_card_assignments_update
BEFORE UPDATE ON card_assignments
FOR EACH ROW
BEGIN
    -- Set started_at saat status berubah ke in_progress
    IF OLD.assignment_status != 'in_progress' AND NEW.assignment_status = 'in_progress' THEN
        IF NEW.started_at IS NULL THEN
            SET NEW.started_at = CURRENT_TIMESTAMP;
        END IF;
    END IF;
    
    -- Set completed_at saat status berubah ke assigned (completed)
    IF OLD.assignment_status != 'assigned' AND NEW.assignment_status = 'assigned' THEN
        IF NEW.completed_at IS NULL THEN
            SET NEW.completed_at = CURRENT_TIMESTAMP;
        END IF;
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Trigger untuk validasi: tidak boleh assign user yang sama 2x di card yang sama
DELIMITER $$
CREATE TRIGGER before_card_assignments_insert_validate
BEFORE INSERT ON card_assignments
FOR EACH ROW
BEGIN
    DECLARE assignment_count INT;
    
    SELECT COUNT(*) INTO assignment_count
    FROM card_assignments
    WHERE card_id = NEW.card_id AND user_id = NEW.user_id;
    
    IF assignment_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'User sudah di-assign ke card ini';
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 7. TRIGGERS UNTUK TABEL SUBTASKS
-- ============================================================

-- Trigger untuk set position subtask
DELIMITER $$
CREATE TRIGGER before_subtasks_insert
BEFORE INSERT ON subtasks
FOR EACH ROW
BEGIN
    DECLARE max_pos INT;
    
    -- Set position otomatis
    IF NEW.position IS NULL THEN
        SELECT COALESCE(MAX(position), 0) + 1 INTO max_pos
        FROM subtasks
        WHERE card_id = NEW.card_id;
        
        SET NEW.position = max_pos;
    END IF;
    
    -- Set default status
    IF NEW.status IS NULL THEN
        SET NEW.status = 'in_progress';
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update card status berdasarkan subtasks
DELIMITER $$
CREATE TRIGGER after_subtasks_update
AFTER UPDATE ON subtasks
FOR EACH ROW
BEGIN
    DECLARE total_subtasks INT;
    DECLARE completed_subtasks INT;
    
    -- Hitung total dan completed subtasks
    SELECT COUNT(*) INTO total_subtasks
    FROM subtasks
    WHERE card_id = NEW.card_id;
    
    SELECT COUNT(*) INTO completed_subtasks
    FROM subtasks
    WHERE card_id = NEW.card_id AND status = 'done';
    
    -- Jika semua subtasks done, update card status
    IF total_subtasks > 0 AND total_subtasks = completed_subtasks THEN
        UPDATE cards
        SET status = 'review'
        WHERE id = NEW.card_id AND status != 'done';
    END IF;
END$$
DELIMITER ;

-- Trigger untuk recalculate subtask actual_hours dari time_logs
DELIMITER $$
CREATE TRIGGER after_subtasks_update_calculate_hours
AFTER UPDATE ON subtasks
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    -- Calculate total hours dari time_logs
    SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
    FROM time_logs
    WHERE subtask_id = NEW.id;
    
    -- Update actual_hours jika berbeda
    IF total_hours != COALESCE(NEW.actual_hours, 0) THEN
        UPDATE subtasks
        SET actual_hours = total_hours
        WHERE id = NEW.id;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 8. TRIGGERS UNTUK TABEL TIME_LOGS
-- ============================================================

-- Trigger untuk validasi time log
DELIMITER $$
CREATE TRIGGER before_time_logs_insert
BEFORE INSERT ON time_logs
FOR EACH ROW
BEGIN
    -- Validasi: harus ada card_id atau subtask_id
    IF NEW.card_id IS NULL AND NEW.subtask_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Time log harus terkait dengan card atau subtask';
    END IF;
    
    -- Calculate duration jika end_time sudah ada
    IF NEW.end_time IS NOT NULL THEN
        SET NEW.duration_minutes = TIMESTAMPDIFF(MINUTE, NEW.start_time, NEW.end_time);
    END IF;
    
    -- Validasi: start_time tidak boleh di masa depan
    IF NEW.start_time > CURRENT_TIMESTAMP THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Start time tidak boleh di masa depan';
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update duration saat update
DELIMITER $$
CREATE TRIGGER before_time_logs_update
BEFORE UPDATE ON time_logs
FOR EACH ROW
BEGIN
    -- Recalculate duration jika end_time berubah
    IF NEW.end_time IS NOT NULL THEN
        SET NEW.duration_minutes = TIMESTAMPDIFF(MINUTE, NEW.start_time, NEW.end_time);
    END IF;
    
    -- Validasi: end_time tidak boleh sebelum start_time
    IF NEW.end_time IS NOT NULL AND NEW.end_time < NEW.start_time THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'End time tidak boleh sebelum start time';
    END IF;
    
    -- Validasi: duration tidak boleh negatif
    IF NEW.duration_minutes IS NOT NULL AND NEW.duration_minutes < 0 THEN
        SET NEW.duration_minutes = 0;
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- Trigger untuk update actual_hours di card
DELIMITER $$
CREATE TRIGGER after_time_logs_insert_update_card
AFTER INSERT ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF NEW.card_id IS NOT NULL THEN
        -- Calculate total hours untuk card
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE card_id = NEW.card_id;
        
        -- Update card actual_hours
        UPDATE cards
        SET actual_hours = total_hours
        WHERE id = NEW.card_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_time_logs_update_update_card
AFTER UPDATE ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF NEW.card_id IS NOT NULL THEN
        -- Calculate total hours untuk card
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE card_id = NEW.card_id;
        
        -- Update card actual_hours
        UPDATE cards
        SET actual_hours = total_hours
        WHERE id = NEW.card_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_time_logs_delete_update_card
AFTER DELETE ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF OLD.card_id IS NOT NULL THEN
        -- Calculate total hours untuk card
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE card_id = OLD.card_id;
        
        -- Update card actual_hours
        UPDATE cards
        SET actual_hours = total_hours
        WHERE id = OLD.card_id;
    END IF;
END$$
DELIMITER ;

-- Trigger untuk update actual_hours di subtask
DELIMITER $$
CREATE TRIGGER after_time_logs_insert_update_subtask
AFTER INSERT ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF NEW.subtask_id IS NOT NULL THEN
        -- Calculate total hours untuk subtask
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE subtask_id = NEW.subtask_id;
        
        -- Update subtask actual_hours
        UPDATE subtasks
        SET actual_hours = total_hours
        WHERE id = NEW.subtask_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_time_logs_update_update_subtask
AFTER UPDATE ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF NEW.subtask_id IS NOT NULL THEN
        -- Calculate total hours untuk subtask
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE subtask_id = NEW.subtask_id;
        
        -- Update subtask actual_hours
        UPDATE subtasks
        SET actual_hours = total_hours
        WHERE id = NEW.subtask_id;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_time_logs_delete_update_subtask
AFTER DELETE ON time_logs
FOR EACH ROW
BEGIN
    DECLARE total_hours DECIMAL(5,2);
    
    IF OLD.subtask_id IS NOT NULL THEN
        -- Calculate total hours untuk subtask
        SELECT COALESCE(SUM(duration_minutes) / 60, 0) INTO total_hours
        FROM time_logs
        WHERE subtask_id = OLD.subtask_id;
        
        -- Update subtask actual_hours
        UPDATE subtasks
        SET actual_hours = total_hours
        WHERE id = OLD.subtask_id;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- 9. TRIGGERS UNTUK TABEL COMMENTS
-- ============================================================

-- Trigger untuk validasi comment
DELIMITER $$
CREATE TRIGGER before_comments_insert
BEFORE INSERT ON comments
FOR EACH ROW
BEGIN
    -- Validasi: harus ada card_id atau subtask_id berdasarkan comment_type
    IF NEW.comment_type = 'card' AND NEW.card_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Comment tipe card harus memiliki card_id';
    END IF;
    
    IF NEW.comment_type = 'subtask' AND NEW.subtask_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Comment tipe subtask harus memiliki subtask_id';
    END IF;
    
    -- Validasi: comment tidak boleh kosong
    IF NEW.comment_text IS NULL OR TRIM(NEW.comment_text) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Comment tidak boleh kosong';
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- SCRIPT UNTUK MENGHAPUS SEMUA TRIGGERS (ROLLBACK)
-- ============================================================

/*
-- Uncomment baris-baris di bawah untuk menghapus semua triggers

-- Users triggers
DROP TRIGGER IF EXISTS before_users_update;
DROP TRIGGER IF EXISTS before_users_delete;

-- Projects triggers
DROP TRIGGER IF EXISTS before_projects_insert;
DROP TRIGGER IF EXISTS before_projects_update;
DROP TRIGGER IF EXISTS after_projects_delete;

-- Boards triggers
DROP TRIGGER IF EXISTS before_boards_insert;
DROP TRIGGER IF EXISTS after_boards_delete;

-- Project Members triggers
DROP TRIGGER IF EXISTS before_project_members_insert;
DROP TRIGGER IF EXISTS after_project_members_insert;
DROP TRIGGER IF EXISTS after_project_members_delete;

-- Cards triggers
DROP TRIGGER IF EXISTS before_cards_insert;
DROP TRIGGER IF EXISTS before_cards_update;
DROP TRIGGER IF EXISTS after_cards_update_calculate_hours;

-- Card Assignments triggers
DROP TRIGGER IF EXISTS before_card_assignments_insert;
DROP TRIGGER IF EXISTS before_card_assignments_update;
DROP TRIGGER IF EXISTS before_card_assignments_insert_validate;

-- Subtasks triggers
DROP TRIGGER IF EXISTS before_subtasks_insert;
DROP TRIGGER IF EXISTS after_subtasks_update;
DROP TRIGGER IF EXISTS after_subtasks_update_calculate_hours;

-- Time Logs triggers
DROP TRIGGER IF EXISTS before_time_logs_insert;
DROP TRIGGER IF EXISTS before_time_logs_update;
DROP TRIGGER IF EXISTS after_time_logs_insert_update_card;
DROP TRIGGER IF EXISTS after_time_logs_update_update_card;
DROP TRIGGER IF EXISTS after_time_logs_delete_update_card;
DROP TRIGGER IF EXISTS after_time_logs_insert_update_subtask;
DROP TRIGGER IF EXISTS after_time_logs_update_update_subtask;
DROP TRIGGER IF EXISTS after_time_logs_delete_update_subtask;

-- Comments triggers
DROP TRIGGER IF EXISTS before_comments_insert;
*/

-- ============================================================
-- CATATAN PENGGUNAAN:
-- ============================================================
-- 1. Jalankan script ini di MySQL/MariaDB client atau phpMyAdmin
-- 2. Pastikan database sudah dipilih: USE nama_database;
-- 3. Triggers akan otomatis berjalan saat ada operasi INSERT/UPDATE/DELETE
-- 4. Untuk menghapus triggers, uncomment bagian rollback di atas
-- ============================================================
