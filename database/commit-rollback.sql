-- ============================================================
-- CONTOH 1: MEMBUAT PROJECT + MENAMBAHKAN LEADER
-- ============================================================

-- Step 1: Mulai Transaction
START TRANSACTION;

-- Step 2: Insert Project Baru
INSERT INTO projects (
    user_id, 
    slug, 
    project_name, 
    description, 
    deadline, 
    status, 
    created_at, 
    updated_at
) VALUES (
    1,                                      -- ID user yang membuat project (admin/leader)
    'sistem-management-karyawan-2024',     -- slug (otomatis dari project_name)
    'Sistem Management Karyawan 2024',     -- nama project
    'Project untuk membuat sistem management karyawan dengan fitur absensi, payroll, dan performance tracking',  -- deskripsi
    '2024-12-31',                          -- deadline
    'active',                              -- status
    NOW(),                                 -- created_at
    NOW()                                  -- updated_at
);

-- Step 3: Ambil ID project yang baru dibuat
SET @project_id = LAST_INSERT_ID();

-- Step 4: Tambahkan Leader ke project
INSERT INTO project_members (
    project_id,
    user_id,
    role,
    joined_at,
    created_at,
    updated_at
) VALUES (
    @project_id,                           -- ID project yang baru dibuat
    5,                                     -- ID user yang akan jadi Project Manager
    'Project Manager',                     -- role
    NOW(),                                 -- joined_at
    NOW(),                                 -- created_at
    NOW()                                  -- updated_at
);

-- Step 5: Update status user menjadi 'working'
UPDATE users 
SET status = 'working', 
    updated_at = NOW() 
WHERE id = 5;

-- Step 6: Buat Default Boards untuk Project
INSERT INTO boards (project_id, board_name, description, position, created_at, updated_at) VALUES
(@project_id, 'To Do', 'Tasks to be done', 1, NOW(), NOW()),
(@project_id, 'In Progress', 'Tasks currently being worked on', 2, NOW(), NOW()),
(@project_id, 'Review', 'Tasks waiting for review', 3, NOW(), NOW()),
(@project_id, 'Done', 'Completed tasks', 4, NOW(), NOW());

-- Step 7: COMMIT jika semua berhasil
COMMIT;

-- Jika ada error, jalankan ini:
ROLLBACK;


-- ============================================================
-- CONTOH 2: MEMBUAT PROJECT + MENAMBAHKAN MULTIPLE MEMBERS
-- ============================================================

START TRANSACTION;

-- Insert Project
INSERT INTO projects (user_id, slug, project_name, description, deadline, status, created_at, updated_at) 
VALUES (
    1, 
    'aplikasi-mobile-ecommerce', 
    'Aplikasi Mobile E-Commerce', 
    'Aplikasi mobile marketplace dengan fitur payment gateway dan tracking order',
    '2024-11-30', 
    'active', 
    NOW(), 
    NOW()
);

SET @project_id = LAST_INSERT_ID();

-- Tambahkan Project Manager
INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 3, 'Project Manager', NOW(), NOW(), NOW());

-- Tambahkan Developer 1
INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 7, 'Developer', NOW(), NOW(), NOW());

-- Tambahkan Developer 2
INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 8, 'Developer', NOW(), NOW(), NOW());

-- Tambahkan Designer
INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 10, 'Designer', NOW(), NOW(), NOW());

-- Update status semua member menjadi 'working'
UPDATE users 
SET status = 'working', updated_at = NOW() 
WHERE id IN (3, 7, 8, 10);

-- Buat Default Boards
INSERT INTO boards (project_id, board_name, description, position, created_at, updated_at) VALUES
(@project_id, 'To Do', 'Tasks to be done', 1, NOW(), NOW()),
(@project_id, 'In Progress', 'Tasks currently being worked on', 2, NOW(), NOW()),
(@project_id, 'Review', 'Tasks waiting for review', 3, NOW(), NOW()),
(@project_id, 'Done', 'Completed tasks', 4, NOW(), NOW());

COMMIT;


-- ============================================================
-- CONTOH 3: CEK DATA SEBELUM INSERT (VALIDASI)
-- ============================================================

START TRANSACTION;

-- Cek apakah user_id ada dan role-nya sesuai
SELECT id, name, role, status FROM users WHERE id = 5;
-- Pastikan role = 'user' atau 'leader' dan status bisa 'free' atau 'working'

-- Cek apakah slug sudah dipakai
SELECT id, project_name, slug FROM projects WHERE slug = 'sistem-management-karyawan-2024';
-- Jika ada hasil, ganti slug dengan yang lain

-- Cek apakah user sudah jadi member di project lain yang aktif
SELECT pm.*, p.project_name, p.status 
FROM project_members pm
JOIN projects p ON pm.project_id = p.id
WHERE pm.user_id = 5 AND p.status IN ('active', 'on_hold');
-- Jika ada hasil, user sedang working di project lain

-- Jika validasi OK, lanjut insert seperti contoh 1
-- ...

COMMIT;


-- ============================================================
-- CONTOH 4: ROLLBACK JIKA ADA ERROR
-- ============================================================

START TRANSACTION;

-- Insert Project
INSERT INTO projects (user_id, slug, project_name, description, deadline, status, created_at, updated_at) 
VALUES (1, 'test-project', 'Test Project', 'Testing', '2024-12-31', 'active', NOW(), NOW());

SET @project_id = LAST_INSERT_ID();

-- Misalkan ada error di sini (user_id tidak ada)
INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 999999, 'Project Manager', NOW(), NOW(), NOW());
-- ERROR: user_id 999999 tidak ada

-- Karena ada error, jalankan ROLLBACK
ROLLBACK;

-- Cek: Project tidak jadi dibuat karena di-rollback
SELECT * FROM projects WHERE slug = 'test-project';
-- Hasilnya kosong (0 rows)


-- ============================================================
-- CONTOH 5: CARA PRAKTIS - SATU QUERY LANGSUNG
-- ============================================================

-- Ganti nilai-nilai ini sesuai kebutuhan Anda:
-- @user_creator_id    : ID user yang membuat project
-- @leader_id          : ID user yang jadi Project Manager
-- @project_name       : Nama project
-- @project_slug       : Slug project (lowercase, pakai dash)
-- @description        : Deskripsi project
-- @deadline           : Deadline project (YYYY-MM-DD)

START TRANSACTION;

INSERT INTO projects (user_id, slug, project_name, description, deadline, status, created_at, updated_at) 
VALUES (
    1,                                    -- Ganti dengan ID user pembuat
    'website-company-profile',           -- Ganti dengan slug
    'Website Company Profile',           -- Ganti dengan nama project
    'Membuat website company profile dengan CMS',  -- Ganti dengan deskripsi
    '2024-12-15',                        -- Ganti dengan deadline
    'active', 
    NOW(), 
    NOW()
);

SET @project_id = LAST_INSERT_ID();

INSERT INTO project_members (project_id, user_id, role, joined_at, created_at, updated_at) 
VALUES (@project_id, 4, 'Project Manager', NOW(), NOW(), NOW());  -- Ganti 4 dengan ID leader

UPDATE users SET status = 'working', updated_at = NOW() WHERE id = 4;  -- Ganti 4 dengan ID leader

INSERT INTO boards (project_id, board_name, description, position, created_at, updated_at) VALUES
(@project_id, 'To Do', 'Tasks to be done', 1, NOW(), NOW()),
(@project_id, 'In Progress', 'Tasks currently being worked on', 2, NOW(), NOW()),
(@project_id, 'Review', 'Tasks waiting for review', 3, NOW(), NOW()),
(@project_id, 'Done', 'Completed tasks', 4, NOW(), NOW());

COMMIT;


-- ============================================================
-- CONTOH 6: QUERY UNTUK CEK HASIL
-- ============================================================

-- Lihat project yang baru dibuat
SELECT * FROM projects ORDER BY id DESC LIMIT 5;

-- Lihat members dari project terakhir
SELECT 
    pm.*,
    u.name as user_name,
    u.email,
    u.status as user_status,
    p.project_name
FROM project_members pm
JOIN users u ON pm.user_id = u.id
JOIN projects p ON pm.project_id = p.id
ORDER BY pm.id DESC LIMIT 10;

-- Lihat boards dari project terakhir
SELECT 
    b.*,
    p.project_name
FROM boards b
JOIN projects p ON b.project_id = p.id
ORDER BY b.id DESC LIMIT 10;

-- Lihat status users yang baru di-assign
SELECT id, name, email, role, status 
FROM users 
WHERE status = 'working' 
ORDER BY updated_at DESC 
LIMIT 10;