<?php
// public/profile.php
$page_title = 'User Profile';
include __DIR__ . '/includes/header.php';
require_login();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = trim($_POST['email'] ?? '');
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $user_id]);
                $success = "Profile updated successfully!";
                $user['email'] = $email;
                $_SESSION['user']['email'] = $email;
            } catch (Exception $e) {
                $error = "Error updating profile: " . $e->getMessage();
            }
        } else {
            $error = "Please provide a valid email address.";
        }
    } elseif (isset($_POST['change_password'])) {
        $current_pw = $_POST['current_password'] ?? '';
        $new_pw = $_POST['new_password'] ?? '';
        $confirm_pw = $_POST['confirm_password'] ?? '';

        if (password_verify($current_pw, $user['password'])) {
            if ($new_pw === $confirm_pw) {
                if (strlen($new_pw) >= 6) {
                    $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed, $user_id]);
                    $success = "Password changed successfully!";
                    // Update the cached user object in case it has the hashed password
                    $user['password'] = $hashed;
                } else {
                    $error = "New password must be at least 6 characters.";
                }
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<div style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">
    <!-- Profile Info Card -->
    <div class="card">
        <div class="card-header">
            <h3>My Profile</h3>
        </div>
        <div style="padding: 1.5rem;">
            <?php if (isset($success) && isset($_POST['update_profile'])): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error) && isset($_POST['update_profile'])): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small style="color: #6b7280;">Username cannot be changed.</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                        value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Change Card -->
    <div class="card">
        <div class="card-header">
            <h3>Change Password</h3>
        </div>
        <div style="padding: 1.5rem;">
            <?php if (isset($success) && isset($_POST['change_password'])): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error) && isset($_POST['change_password'])): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <button type="submit" name="change_password" class="btn btn-secondary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
