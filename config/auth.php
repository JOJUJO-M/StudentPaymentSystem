<?php
// config/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate project root path relative to the domain root
$base_dir = str_replace('\\', '/', realpath(dirname(__DIR__)));
$doc_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$project_path = str_ireplace($doc_root, '', $base_dir);
$project_root = rtrim($project_path, '/') . '/';
$project_root = str_replace('//', '/', $project_root);
if ($project_root === '/')
    $project_root = '/'; // Ensure consistency

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has a specific role
 */
function has_role($role_name)
{
    if (!is_logged_in())
        return false;
    $user_roles = $_SESSION['roles'] ?? [];
    if (is_array($role_name)) {
        return !empty(array_intersect($role_name, $user_roles));
    }
    return in_array($role_name, $user_roles);
}

/**
 * Middleware: Redirect to login if not authenticated
 */
function require_login()
{
    global $project_root;
    if (!is_logged_in()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
            exit();
        }
        header('Location: ' . $project_root . 'login.php');
        exit();
    }
}

/**
 * Get the specific dashboard URL for the current user's role
 */
function get_dashboard_url()
{
    global $project_root;
    return $project_root . get_relative_dashboard_url();
}

/**
 * Get dashboard path relative to project root
 */
function get_relative_dashboard_url()
{
    if (!is_logged_in())
        return 'login.php';

    if (has_role('global_admin'))
        return 'dashboard/super_dashboard.php';
    if (has_role('headmaster'))
        return 'dashboard/admin_dashboard.php';
    if (has_role('staff'))
        return 'dashboard/staff_dashboard.php';
    if (has_role('teacher'))
        return 'dashboard/staff_dashboard.php'; // Map teachers to staff dashboard if they exist

    return 'index.php';
}

/**
 * Middleware: Redirect if not admin (global_admin or headmaster)
 */
function require_admin()
{
    require_login();
    if (!has_role(['global_admin', 'headmaster'])) {
        header('Location: ' . get_dashboard_url() . '?error=unauthorized');
        exit();
    }
}

/**
 * Middleware: Redirect if not global_admin
 */
function require_global_admin()
{
    require_login();
    if (!has_role('global_admin')) {
        header('Location: ' . get_dashboard_url() . '?error=unauthorized');
        exit();
    }
}

/**
 * Middleware: Redirect if not headmaster or global_admin
 */
function require_headmaster()
{
    require_login();
    if (!has_role(['global_admin', 'headmaster'])) {
        header('Location: ' . get_dashboard_url() . '?error=unauthorized');
        exit();
    }
}

/**
 * Check if current user is Global Admin
 */
function is_global_admin()
{
    return has_role('global_admin');
}

/**
 * Get current user data
 */
function get_user()
{
    return $_SESSION['user'] ?? null;
}
