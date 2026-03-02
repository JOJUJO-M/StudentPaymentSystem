<?php
// api/crud.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');
require_login();

$action = $_GET['action'] ?? '';
$entity = $_GET['entity'] ?? '';

// Security: White-list entities
$allowed_entities = [
    'students', 'teachers', 'classes', 'subjects', 'departments',
    'grades', 'attendance', 'activity_logs', 'settings', 'schools',
    'payments', 'academic_years', 'users', 'roles'
];

if (!in_array($entity, $allowed_entities)) {
    echo json_encode(['success' => false, 'message' => 'Invalid entity or unauthorized access']);
    exit();
}

$user = get_user();
$school_id = $user['school_id'] ?? null;
$is_global = is_global_admin();

// Authorization checks
if (!$is_global) {
    // Entities that ONLY global admin can touch
    $global_only = ['schools', 'roles'];
    if (in_array($entity, $global_only)) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Global Admin only']);
        exit();
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

try {
    switch ($action) {
        case 'list':
            $search = $_GET['search'] ?? '';
            $params = [];
            $where = [];

            if (!$is_global && in_array($entity, ['students', 'teachers', 'classes', 'subjects', 'departments', 'payments',
                'academic_years', 'users'])) {
                $where[] = "school_id = ?";
                $params[] = $school_id;
            }

            if (!empty($search)) {
                $search_query = "";
                if ($entity === 'users' || $entity === 'students' || $entity === 'teachers') {
                    $search_query = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                } else if (in_array($entity, ['schools', 'classes', 'subjects', 'departments'])) {
                    $search_query = "(name LIKE ?)";
                    $params[] = "%$search%";
                }

                if ($search_query) {
                    $where[] = $search_query;
                }
            }

            $sql = "SELECT * FROM $entity";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'create':
            // Automatically add school_id if not a global admin
            if (!$is_global && !isset($data['school_id'])) {
                $data['school_id'] = $school_id;
            }

            $fields = array_keys($data);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            $sql = "INSERT INTO $entity (" . implode(',', $fields) . ") VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $new_id = $pdo->lastInsertId();

            // Log activity
            $stmt_log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, school_id) VALUES (?, ?, ?, ?)");
            $stmt_log->execute([$_SESSION['user_id'], "Create $entity", "Created $entity ID: $new_id", $school_id]);

            echo json_encode(['success' => true, 'message' => ucfirst($entity) . ' created successfully', 'id' => $new_id]);
            break;

        case 'update':
            $id = $data['id'] ?? 0;
            unset($data['id']);

            // Security check for update
            if (!$is_global) {
                $check_stmt = $pdo->prepare("SELECT id FROM $entity WHERE id = ? AND school_id = ?");
                $check_stmt->execute([$id, $school_id]);
                if (!$check_stmt->fetch()) {
                    exit(json_encode(['success' => false, 'message' => 'Unauthorized entry update']));
                }
            }

            $sets = [];
            foreach (array_keys($data) as $key) {
                $sets[] = "$key = ?";
            }
            $sql = "UPDATE $entity SET " . implode(', ', $sets) . " WHERE id = ?";
            $params = array_values($data);
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => ucfirst($entity) . ' updated successfully']);
            break;

        case 'delete':
            $id = $data['id'] ?? $_GET['id'] ?? 0;

            // Security check for delete
            if (!$is_global) {
                $check_stmt = $pdo->prepare("SELECT id FROM $entity WHERE id = ? AND school_id = ?");
                $check_stmt->execute([$id, $school_id]);
                if (!$check_stmt->fetch()) {
                    exit(json_encode(['success' => false, 'message' => 'Unauthorized entry deletion']));
                }
            }

            $stmt = $pdo->prepare("DELETE FROM $entity WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => ucfirst($entity) . ' deleted successfully']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action not supported']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}