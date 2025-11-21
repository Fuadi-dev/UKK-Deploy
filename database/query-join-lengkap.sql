-- ============================================================
-- QUERY JOIN LENGKAP - SISTEM MANAJEMEN PROYEK
-- Database: ukk-management-proyek
-- Generated: 2025-11-20
-- ============================================================

-- ============================================================
-- 1. PROJECTS - JOIN DENGAN USERS (CREATOR)
-- ============================================================

-- Lihat semua project dengan info creator-nya
SELECT 
    p.id,
    p.project_name,
    p.slug,
    p.description,
    p.deadline,
    p.status,
    p.created_at,
    u.id as creator_id,
    u.name as creator_name,
    u.email as creator_email,
    u.role as creator_role
FROM projects p
INNER JOIN users u ON p.user_id = u.id
ORDER BY p.created_at DESC;


-- ============================================================
-- 2. PROJECTS - JOIN DENGAN PROJECT MEMBERS (TEAM)
-- ============================================================

-- Lihat project dengan semua member-nya
SELECT 
    p.id as project_id,
    p.project_name,
    p.status as project_status,
    pm.id as member_id,
    pm.role as member_role,
    pm.joined_at,
    u.id as user_id,
    u.name as user_name,
    u.email as user_email,
    u.status as user_status
FROM projects p
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN users u ON pm.user_id = u.id
WHERE p.status = 'active'
ORDER BY p.project_name, pm.role;


-- ============================================================
-- 3. PROJECTS - JOIN LENGKAP (PROJECT + CREATOR + MEMBERS)
-- ============================================================

-- Lihat project lengkap dengan creator dan members
SELECT 
    p.id as project_id,
    p.project_name,
    p.slug,
    p.status as project_status,
    p.deadline,
    creator.name as created_by,
    creator.email as creator_email,
    pm.role as member_role,
    member.name as member_name,
    member.email as member_email,
    member.status as member_status,
    pm.joined_at
FROM projects p
INNER JOIN users creator ON p.user_id = creator.id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN users member ON pm.user_id = member.id
ORDER BY p.created_at DESC, pm.role;


-- ============================================================
-- 4. BOARDS - JOIN DENGAN PROJECTS
-- ============================================================

-- Lihat semua boards dengan info project-nya
SELECT 
    b.id as board_id,
    b.board_name,
    b.description as board_description,
    b.position,
    p.id as project_id,
    p.project_name,
    p.status as project_status
FROM boards b
INNER JOIN projects p ON b.project_id = p.id
ORDER BY p.project_name, b.position;


-- ============================================================
-- 5. CARDS - JOIN DENGAN BOARDS DAN PROJECTS
-- ============================================================

-- Lihat semua cards dengan info board dan project
SELECT 
    c.id as card_id,
    c.card_title,
    c.slug,
    c.status as card_status,
    c.priority,
    c.due_date,
    c.estimated_hours,
    c.actual_hours,
    b.board_name,
    p.project_name,
    creator.name as created_by
FROM cards c
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
INNER JOIN users creator ON c.user_id = creator.id
WHERE c.deleted_at IS NULL
ORDER BY p.project_name, b.position, c.position;


-- ============================================================
-- 6. CARDS - JOIN LENGKAP (CARDS + BOARDS + PROJECTS + ASSIGNMENTS)
-- ============================================================

-- Lihat cards lengkap dengan board, project, dan assigned users
SELECT 
    c.id as card_id,
    c.card_title,
    c.status as card_status,
    c.priority,
    c.due_date,
    c.estimated_hours,
    c.actual_hours,
    b.board_name,
    p.project_name,
    creator.name as created_by,
    ca.assignment_status,
    ca.assigned_at,
    assigned_user.name as assigned_to,
    assigned_user.email as assigned_email
FROM cards c
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
INNER JOIN users creator ON c.user_id = creator.id
LEFT JOIN card_assignments ca ON c.id = ca.card_id
LEFT JOIN users assigned_user ON ca.user_id = assigned_user.id
WHERE c.deleted_at IS NULL
ORDER BY p.project_name, c.due_date;


-- ============================================================
-- 7. CARD ASSIGNMENTS - JOIN DENGAN CARDS DAN USERS
-- ============================================================

-- Lihat semua assignment dengan detail card dan user
SELECT 
    ca.id as assignment_id,
    ca.assignment_status,
    ca.assigned_at,
    ca.started_at,
    ca.completed_at,
    c.card_title,
    c.status as card_status,
    c.priority,
    u.name as assigned_to,
    u.email as user_email,
    u.status as user_status,
    p.project_name
FROM card_assignments ca
INNER JOIN cards c ON ca.card_id = c.id
INNER JOIN users u ON ca.user_id = u.id
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
ORDER BY ca.assigned_at DESC;


-- ============================================================
-- 8. SUBTASKS - JOIN DENGAN CARDS DAN PROJECTS
-- ============================================================

-- Lihat semua subtasks dengan info card dan project
SELECT 
    st.id as subtask_id,
    st.subtask_title,
    st.status as subtask_status,
    st.estimated_hours,
    st.actual_hours,
    st.position,
    c.card_title,
    c.status as card_status,
    p.project_name,
    u.name as created_by
FROM subtasks st
INNER JOIN cards c ON st.card_id = c.id
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
INNER JOIN users u ON st.user_id = u.id
ORDER BY p.project_name, c.card_title, st.position;


-- ============================================================
-- 9. TIME LOGS - JOIN LENGKAP (CARDS/SUBTASKS + USERS + PROJECTS)
-- ============================================================

-- Lihat semua time logs dengan detail lengkap
SELECT 
    tl.id as log_id,
    tl.start_time,
    tl.end_time,
    tl.duration_minutes,
    ROUND(tl.duration_minutes / 60, 2) as duration_hours,
    tl.description as log_description,
    u.name as logged_by,
    CASE 
        WHEN tl.card_id IS NOT NULL THEN c.card_title
        WHEN tl.subtask_id IS NOT NULL THEN st.subtask_title
    END as task_name,
    CASE 
        WHEN tl.card_id IS NOT NULL THEN 'Card'
        WHEN tl.subtask_id IS NOT NULL THEN 'Subtask'
    END as task_type,
    p.project_name
FROM time_logs tl
INNER JOIN users u ON tl.user_id = u.id
LEFT JOIN cards c ON tl.card_id = c.id
LEFT JOIN subtasks st ON tl.subtask_id = st.id
LEFT JOIN boards b ON c.board_id = b.id
LEFT JOIN projects p ON b.project_id = p.id
ORDER BY tl.start_time DESC;


-- ============================================================
-- 10. COMMENTS - JOIN DENGAN CARDS/SUBTASKS DAN USERS
-- ============================================================

-- Lihat semua comments dengan detail task dan user
SELECT 
    cm.id as comment_id,
    cm.comment_text,
    cm.comment_type,
    cm.created_at,
    u.name as commented_by,
    u.email as user_email,
    CASE 
        WHEN cm.comment_type = 'card' THEN c.card_title
        WHEN cm.comment_type = 'subtask' THEN st.subtask_title
    END as task_name,
    p.project_name
FROM comments cm
INNER JOIN users u ON cm.user_id = u.id
LEFT JOIN cards c ON cm.card_id = c.id
LEFT JOIN subtasks st ON cm.subtask_id = st.id
LEFT JOIN boards b ON c.board_id = b.id
LEFT JOIN projects p ON b.project_id = p.id
ORDER BY cm.created_at DESC;


-- ============================================================
-- 11. DASHBOARD - PROJECT OVERVIEW (STATISTIK)
-- ============================================================

-- Dashboard: Statistik project dengan member count
SELECT 
    p.id as project_id,
    p.project_name,
    p.status,
    p.deadline,
    creator.name as project_owner,
    COUNT(DISTINCT pm.user_id) as total_members,
    COUNT(DISTINCT b.id) as total_boards,
    COUNT(DISTINCT c.id) as total_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'done' THEN c.id END) as completed_cards,
    ROUND(
        COUNT(DISTINCT CASE WHEN c.status = 'done' THEN c.id END) * 100.0 / 
        NULLIF(COUNT(DISTINCT c.id), 0), 
    2) as completion_percentage
FROM projects p
INNER JOIN users creator ON p.user_id = creator.id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN boards b ON p.id = b.project_id
LEFT JOIN cards c ON b.id = c.board_id AND c.deleted_at IS NULL
WHERE p.deleted_at IS NULL
GROUP BY p.id, p.project_name, p.status, p.deadline, creator.name
ORDER BY p.created_at DESC;


-- ============================================================
-- 12. USER WORKLOAD - CARDS PER USER
-- ============================================================

-- Lihat beban kerja setiap user (berapa cards yang di-assign)
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    u.role,
    u.status,
    COUNT(DISTINCT ca.card_id) as total_assigned_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'in_progress' THEN ca.card_id END) as in_progress_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'done' THEN ca.card_id END) as completed_cards,
    SUM(c.estimated_hours) as total_estimated_hours,
    SUM(c.actual_hours) as total_actual_hours
FROM users u
LEFT JOIN card_assignments ca ON u.id = ca.user_id
LEFT JOIN cards c ON ca.card_id = c.id AND c.deleted_at IS NULL
WHERE u.role = 'user'
GROUP BY u.id, u.name, u.email, u.role, u.status
ORDER BY total_assigned_cards DESC;


-- ============================================================
-- 13. PROJECT PROGRESS - DETAIL PER BOARD
-- ============================================================

-- Lihat progress project per board
SELECT 
    p.project_name,
    b.board_name,
    b.position as board_position,
    COUNT(c.id) as total_cards,
    SUM(c.estimated_hours) as total_estimated_hours,
    SUM(c.actual_hours) as total_actual_hours,
    COUNT(DISTINCT ca.user_id) as members_involved
FROM projects p
INNER JOIN boards b ON p.id = b.project_id
LEFT JOIN cards c ON b.id = c.board_id AND c.deleted_at IS NULL
LEFT JOIN card_assignments ca ON c.id = ca.card_id
WHERE p.status = 'active'
GROUP BY p.id, p.project_name, b.id, b.board_name, b.position
ORDER BY p.project_name, b.position;


-- ============================================================
-- 14. LATE TASKS - CARDS YANG TERLAMBAT
-- ============================================================

-- Lihat cards yang melewati due date tapi belum selesai
SELECT 
    c.id as card_id,
    c.card_title,
    c.due_date,
    DATEDIFF(CURDATE(), c.due_date) as days_overdue,
    c.status,
    c.priority,
    b.board_name,
    p.project_name,
    GROUP_CONCAT(u.name SEPARATOR ', ') as assigned_to
FROM cards c
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
LEFT JOIN card_assignments ca ON c.id = ca.card_id
LEFT JOIN users u ON ca.user_id = u.id
WHERE c.due_date < CURDATE()
  AND c.status != 'done'
  AND c.deleted_at IS NULL
GROUP BY c.id, c.card_title, c.due_date, c.status, c.priority, b.board_name, p.project_name
ORDER BY days_overdue DESC;


-- ============================================================
-- 15. TIME TRACKING - TOTAL JAM KERJA PER USER
-- ============================================================

-- Lihat total jam kerja setiap user
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    COUNT(tl.id) as total_logs,
    SUM(tl.duration_minutes) as total_minutes,
    ROUND(SUM(tl.duration_minutes) / 60, 2) as total_hours,
    MIN(tl.start_time) as first_log,
    MAX(tl.end_time) as last_log
FROM users u
LEFT JOIN time_logs tl ON u.id = tl.user_id
WHERE u.role = 'user'
GROUP BY u.id, u.name, u.email
ORDER BY total_hours DESC;


-- ============================================================
-- 16. RECENT ACTIVITIES - AKTIVITAS TERBARU
-- ============================================================

-- Lihat aktivitas terbaru dari semua tabel
SELECT 
    'Comment' as activity_type,
    cm.id as activity_id,
    cm.created_at as activity_time,
    u.name as user_name,
    CONCAT('Commented on "', c.card_title, '"') as activity_description,
    p.project_name
FROM comments cm
INNER JOIN users u ON cm.user_id = u.id
LEFT JOIN cards c ON cm.card_id = c.id
LEFT JOIN boards b ON c.board_id = b.id
LEFT JOIN projects p ON b.project_id = p.id

UNION ALL

SELECT 
    'Time Log' as activity_type,
    tl.id as activity_id,
    tl.start_time as activity_time,
    u.name as user_name,
    CONCAT('Logged ', ROUND(tl.duration_minutes/60, 2), ' hours on "', c.card_title, '"') as activity_description,
    p.project_name
FROM time_logs tl
INNER JOIN users u ON tl.user_id = u.id
LEFT JOIN cards c ON tl.card_id = c.id
LEFT JOIN boards b ON c.board_id = b.id
LEFT JOIN projects p ON b.project_id = p.id
WHERE tl.end_time IS NOT NULL

UNION ALL

SELECT 
    'Card Assignment' as activity_type,
    ca.id as activity_id,
    ca.assigned_at as activity_time,
    u.name as user_name,
    CONCAT('Assigned to "', c.card_title, '"') as activity_description,
    p.project_name
FROM card_assignments ca
INNER JOIN users u ON ca.user_id = u.id
INNER JOIN cards c ON ca.card_id = c.id
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id

ORDER BY activity_time DESC
LIMIT 50;


-- ============================================================
-- 17. PROJECT MEMBERS - DETAIL LENGKAP
-- ============================================================

-- Lihat detail lengkap member di setiap project
SELECT 
    p.project_name,
    p.status as project_status,
    pm.role as member_role,
    pm.joined_at,
    u.name as member_name,
    u.email,
    u.status as user_status,
    COUNT(DISTINCT ca.card_id) as assigned_cards,
    SUM(tl.duration_minutes) / 60 as total_hours_worked
FROM project_members pm
INNER JOIN projects p ON pm.project_id = p.id
INNER JOIN users u ON pm.user_id = u.id
LEFT JOIN card_assignments ca ON u.id = ca.user_id
LEFT JOIN cards c ON ca.card_id = c.id AND c.board_id IN (
    SELECT id FROM boards WHERE project_id = p.id
)
LEFT JOIN time_logs tl ON u.id = tl.user_id AND (
    tl.card_id IN (SELECT id FROM cards WHERE board_id IN (
        SELECT id FROM boards WHERE project_id = p.id
    ))
)
GROUP BY p.id, p.project_name, p.status, pm.id, pm.role, pm.joined_at, u.id, u.name, u.email, u.status
ORDER BY p.project_name, pm.role, u.name;


-- ============================================================
-- 18. CARDS WITH SUBTASKS - PROGRESS TRACKING
-- ============================================================

-- Lihat progress cards dengan subtasks-nya
SELECT 
    c.id as card_id,
    c.card_title,
    c.status as card_status,
    p.project_name,
    COUNT(st.id) as total_subtasks,
    COUNT(CASE WHEN st.status = 'done' THEN 1 END) as completed_subtasks,
    ROUND(
        COUNT(CASE WHEN st.status = 'done' THEN 1 END) * 100.0 / 
        NULLIF(COUNT(st.id), 0), 
    2) as subtask_completion_percentage
FROM cards c
INNER JOIN boards b ON c.board_id = b.id
INNER JOIN projects p ON b.project_id = p.id
LEFT JOIN subtasks st ON c.id = st.card_id
WHERE c.deleted_at IS NULL
GROUP BY c.id, c.card_title, c.status, p.project_name
HAVING total_subtasks > 0
ORDER BY p.project_name, c.card_title;


-- ============================================================
-- 19. USERS AVAILABILITY - CEK USER YANG TERSEDIA
-- ============================================================

-- Lihat users yang tersedia (tidak sedang di project aktif)
SELECT 
    u.id,
    u.name,
    u.email,
    u.role,
    u.status,
    COUNT(pm.id) as active_projects,
    GROUP_CONCAT(p.project_name SEPARATOR ', ') as current_projects
FROM users u
LEFT JOIN project_members pm ON u.id = pm.user_id
LEFT JOIN projects p ON pm.project_id = p.id AND p.status IN ('active', 'on_hold')
WHERE u.role = 'user'
GROUP BY u.id, u.name, u.email, u.role, u.status
HAVING active_projects = 0 OR u.status = 'free'
ORDER BY u.name;


-- ============================================================
-- 20. COMPLEX JOIN - FULL PROJECT REPORT
-- ============================================================

-- Report lengkap project dengan semua detail
SELECT 
    p.id as project_id,
    p.project_name,
    p.status as project_status,
    p.deadline,
    creator.name as created_by,
    
    -- Member stats
    COUNT(DISTINCT pm.user_id) as total_members,
    
    -- Board stats
    COUNT(DISTINCT b.id) as total_boards,
    
    -- Card stats
    COUNT(DISTINCT c.id) as total_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'todo' THEN c.id END) as todo_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'in_progress' THEN c.id END) as inprogress_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'review' THEN c.id END) as review_cards,
    COUNT(DISTINCT CASE WHEN c.status = 'done' THEN c.id END) as done_cards,
    
    -- Time stats
    SUM(c.estimated_hours) as total_estimated_hours,
    SUM(c.actual_hours) as total_actual_hours,
    
    -- Completion
    ROUND(
        COUNT(DISTINCT CASE WHEN c.status = 'done' THEN c.id END) * 100.0 / 
        NULLIF(COUNT(DISTINCT c.id), 0), 
    2) as completion_percentage,
    
    -- Overdue cards
    COUNT(DISTINCT CASE 
        WHEN c.due_date < CURDATE() AND c.status != 'done' 
        THEN c.id 
    END) as overdue_cards

FROM projects p
INNER JOIN users creator ON p.user_id = creator.id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN boards b ON p.id = b.project_id
LEFT JOIN cards c ON b.id = c.board_id AND c.deleted_at IS NULL

WHERE p.deleted_at IS NULL

GROUP BY 
    p.id, 
    p.project_name, 
    p.status, 
    p.deadline, 
    creator.name

ORDER BY p.created_at DESC;


-- ============================================================
-- SELESAI - TOTAL 20 QUERY JOIN LENGKAP
-- ============================================================
-- Semua query di atas bisa langsung dijalankan di phpMyAdmin
-- Tinggal copy-paste sesuai kebutuhan!
-- ============================================================
