<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <?php
        $logo = $_SESSION['school']['logo'] ?? 'assets/images/CBE_Logo2.png';
        $school_name = $_SESSION['school']['name'] ?? get_setting('system_name', 'CBE SYSTEM');
        ?>
        <img src="<?php echo $project_root . $logo; ?>" alt="Logo"
            style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
        <span style="font-size: 0.85rem;"><?php echo strtoupper($school_name); ?></span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>dashboard/index.php"><i class="fas fa-tachometer-alt"></i>
                    Dashboard</a>
            </li>
            <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>index.php"><i class="fas fa-external-link-alt"></i> View
                    Website</a>
            </li>

            <li class="nav-label">Academic Management</li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/students') !== false ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>modules/students/list.php"><i class="fas fa-user-graduate"></i>
                    Students</a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/teachers') !== false ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>modules/teachers/list.php"><i
                        class="fas fa-chalkboard-teacher"></i> Teachers</a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/classes') !== false ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>modules/classes/list.php"><i class="fas fa-school"></i> Classes</a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/subjects') !== false ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>modules/subjects/list.php"><i class="fas fa-book"></i> Subjects</a>
            </li>

            <li class="nav-label">Accounting</li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/payments') !== false ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>modules/payments/list.php"><i class="fas fa-money-bill-wave"></i>
                    Student Payments</a>
            </li>

            <li class="nav-label">Account</li>
            <li class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <a href="<?php echo $project_root; ?>profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
            </li>

            <?php if (has_role('global_admin')): ?>
                <li class="nav-label">Global Administration</li>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/schools') !== false ? 'active' : ''; ?>">
                    <a href="<?php echo $project_root; ?>modules/schools/list.php"><i class="fas fa-university"></i> Manage
                        Schools</a>
                </li>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'modules/users') !== false ? 'active' : ''; ?>">
                    <a href="<?php echo $project_root; ?>modules/users/list.php"><i class="fas fa-users-cog"></i> User
                        Management</a>
                </li>
                <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <a href="<?php echo $project_root; ?>modules/settings/index.php"><i class="fas fa-sliders-h"></i> System
                        Settings</a>
                </li>
                <?php
            elseif (has_role('headmaster')): ?>
                <li class="nav-label">School Administration</li>
                <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <a href="<?php echo $project_root; ?>modules/settings/index.php"><i class="fas fa-school"></i> School
                        Settings</a>
                </li>
                <?php
            endif; ?>
        </ul>
    </nav>
</aside>