<?php
// api/auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'register_headmaster') {
        // Use $_POST for multipart/form-data
        $data = $_POST;
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $full_name = trim($data['full_name'] ?? '');

        // School details
        $school_name = trim($data['school_name'] ?? '');
        $reg_number = trim($data['reg_number'] ?? '');
        $address = trim($data['address'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($school_name) || empty($reg_number) || !isset($_FILES['logo'])) {
            echo json_encode(['success' => false, 'message' => 'All school details (including logo) and admin details are required']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
                exit();
            }

            // Handle Logo Upload
            $logo_path = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $ext;
                $target_dir = __DIR__ . '/../uploads/logos/';
                if (!is_dir($target_dir))
                    mkdir($target_dir, 0777, true);

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $filename)) {
                    $logo_path = 'uploads/logos/' . $filename;
                }
            }

            // 1. Create School
            $stmt = $pdo->prepare("INSERT INTO schools (name, reg_number, address, phone, email, logo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$school_name, $reg_number, $address, $phone, $email, $logo_path]);
            $school_id = $pdo->lastInsertId();

            // 2. Create Headmaster User
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, school_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $full_name, $school_id]);
            $user_id = $pdo->lastInsertId();

            // 3. Assign Headmaster Role (ID 2)
            $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$user_id, 2]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'School and Headmaster account created successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'update_school') {
        require_login();
        if (!has_role(['global_admin', 'headmaster'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $data = $_POST;
        $id = $data['id'] ?? null;
        $name = trim($data['name'] ?? '');
        $reg_number = trim($data['reg_number'] ?? '');
        $address = trim($data['address'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $email = trim($data['email'] ?? '');

        // Security: Ensure headmaster only updates THEIR school
        if (has_role('headmaster') && $id != $_SESSION['user']['school_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized school update']);
            exit();
        }

        try {
            // Handle Logo Update
            $logo_sql = "";
            $params = [$name, $reg_number, $address, $phone, $email];

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $ext;
                $target_dir = __DIR__ . '/../uploads/logos/';

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $filename)) {
                    $logo_sql = ", logo = ?";
                    $params[] = 'uploads/logos/' . $filename;
                }
            }

            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE schools SET name = ?, reg_number = ?, address = ?, phone = ?, email = ? $logo_sql WHERE id = ?");
            $stmt->execute($params);

            // Update session school data
            if ($id == ($_SESSION['user']['school_id'] ?? null)) {
                $stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['school'] = $stmt->fetch();
            }

            echo json_encode(['success' => true, 'message' => 'School updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    // Removed public registration action

    if ($action === 'register_student' || $action === 'register_teacher' || $action === 'register_staff') {
        require_login();
        // Restrict to admins (headmaster or global_admin)
        if (!has_role(['global_admin', 'headmaster'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized entry']);
            exit();
        }

        $full_name = trim($data['full_name'] ?? '');
        $school_id = $data['school_id'] ?? ($_SESSION['user']['school_id'] ?? null);

        // If it's a student, auto-generate credentials since they won't use the system
        if ($action === 'register_student') {
            $student_id = trim($data['student_id'] ?? '');
            $username = 'std_' . time() . '_' . rand(100, 999);
            $email = $username . '@internal.school';
            $password = bin2hex(random_bytes(10)); // Random secure password nobody will use
        } else {
            $username = trim($data['username'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? 'Staff123';
        }

        if (empty($school_id)) {
            echo json_encode(['success' => false, 'message' => 'School selection required']);
            exit();
        }

        $role_id = 3; // Default teacher
        if ($action === 'register_student')
            $role_id = 4;
        if ($action === 'register_teacher')
            $role_id = 3;
        if ($action === 'register_staff')
            $role_id = 6;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Account already exists']);
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, school_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $full_name, $school_id]);
            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $role_id]);

            if ($action === 'register_student') {
                $student_id = $data['student_id'] ?? '';
                $class_id = !empty($data['class_id']) ? $data['class_id'] : null;
                $ay_id = !empty($data['academic_year_id']) ? $data['academic_year_id'] : null;

                $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, class_id, academic_year_id, school_id, admission_date) VALUES (?, ?, ?, ?, ?, CURDATE())");
                $stmt->execute([$user_id, $student_id, $class_id, $ay_id, $school_id]);
            } elseif ($action === 'register_teacher') {
                $employee_id = $data['employee_id'] ?? '';
                $dept_id = !empty($data['department_id']) ? $data['department_id'] : null;
                $stmt = $pdo->prepare("INSERT INTO teachers (user_id, employee_id, department_id, school_id, hire_date) VALUES (?, ?, ?, ?, CURDATE())");
                $stmt->execute([$user_id, $employee_id, $dept_id, $school_id]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => ucfirst(str_replace('register_', '', $action)) . ' account created successfully!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'login') {
        $login = trim($data['login'] ?? ''); // Username or Email
        $password = $data['password'] ?? '';

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Get roles
                $stmt = $pdo->prepare("SELECT r.name FROM roles r JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = ?");
                $stmt->execute([$user['id']]);
                $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['roles'] = $roles;
                $_SESSION['user'] = $user;

                // Fetch school details if applicable
                if ($user['school_id']) {
                    $stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
                    $stmt->execute([$user['school_id']]);
                    $_SESSION['school'] = $stmt->fetch();
                }

                // Log activity
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
                $stmt->execute([$user['id'], 'Login']);

                echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => get_relative_dashboard_url()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
