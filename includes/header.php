<?php
// includes/header.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/theme.php';
require_once __DIR__ . '/../config/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo ($page_title ? $page_title . ' - ' : '') . get_setting('system_name', 'CBE System'); ?>
    </title>

    <!-- Dynamic Theme CSS -->
    <style>
        <?php echo get_theme_css($theme); ?>
    </style>

    <link rel="stylesheet" href="<?php echo $project_root; ?>assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        const PROJECT_ROOT = '<?php echo $project_root; ?>';
    </script>
</head>

<body class="<?php echo $body_class ?? ''; ?>">

    <?php if (is_logged_in() && !isset($plain_layout)): ?>
        <div class="app-container">
            <?php include __DIR__ . '/sidebar.php'; ?>
            <main class="content-area">
                <header class="top-nav">
                    <div class="nav-left">
                        <button id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                        <h2>
                            <?php echo $page_title ?? 'Dashboard'; ?>
                        </h2>
                    </div>
                    <div class="nav-right">
                        <div class="user-profile">
                            <span><i class="fas fa-user-circle"></i>
                                <?php echo $_SESSION['username']; ?>
                            </span>
                            <a href="<?php echo $project_root; ?>logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i></a>
                        </div>
                    </div>
                </header>
                <div class="main-content">
                    <?php
    else: ?>
                    <!-- Public Navigation Bar -->
                    <nav class="public-nav">
                        <div class="nav-container">
                            <div class="nav-logo">
                                <img src="<?php echo $project_root; ?>assets/images/CBE_Logo2.png" alt="Logo">
                                <span><?php echo get_setting('system_name', 'CBE System'); ?></span>
                            </div>
                            <div class="nav-links">
                                <a href="<?php echo $project_root; ?>index.php">Home</a>
                                <a href="#features">About </a>
                                <a href="#contact">Contact</a>
                                <?php if (is_logged_in()): ?>
                                    <a href="<?php echo $project_root; ?>dashboard/index.php"
                                        class="btn btn-primary btn-sm">Dashboard</a>
                                    <?php
                                else: ?>
                                    <a href="<?php echo $project_root; ?>login.php">Login</a>
                                    <a href="<?php echo $project_root; ?>register.php"
                                        class="btn btn-primary btn-sm">Register</a>
                                    <?php
                                endif; ?>
                            </div>
                        </div>
                    </nav>
                    <?php
    endif; ?>