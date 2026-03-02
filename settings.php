<?php
// public/settings.php
$page_title = 'System Settings';
include __DIR__ . '/includes/header.php';
require_admin();

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings_raw = $stmt->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Default settings if empty
$defaults = [
    'system_name' => 'CBE Management System',
    'contact_email' => 'support@cbe.ac.tz',
    'footer_text' => '© 2026 CBE Boilerplate'
];

foreach ($defaults as $key => $val) {
    if (!isset($settings[$key])) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
        $settings[$key] = $val;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($defaults as $key => $val) {
        if (isset($_POST[$key])) {
            $value = trim($_POST[$key]);
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
            $settings[$key] = $value;
        }
    }
    $success = "Settings updated successfully!";
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>System Configuration</h3>
    </div>
    <div style="padding: 1.5rem;">
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="system_name">System Name</label>
                <input type="text" id="system_name" name="system_name" class="form-control" required
                    value="<?php echo htmlspecialchars($settings['system_name']); ?>">
            </div>

            <div class="form-group">
                <label for="contact_email">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control" required
                    value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
            </div>

            <div class="form-group">
                <label for="footer_text">Footer Text</label>
                <input type="text" id="footer_text" name="footer_text" class="form-control" required
                    value="<?php echo htmlspecialchars($settings['footer_text']); ?>">
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
