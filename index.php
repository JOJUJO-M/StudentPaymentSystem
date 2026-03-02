<?php
// public/index.php
require_once __DIR__ . '/config/auth.php';
$page_title = '';
$plain_layout = true;
$body_class = 'landing-page';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">Welcome to Academic Excellence</div>
        <h1><?php echo get_setting('system_name', 'CBE School Management'); ?></h1>
        <p>The unified platform for administrators, teachers, and students. Manage grades, attendance, and payments with
            unprecedented ease.</p>
        <div class="hero-btns">
            <?php if (is_logged_in()): ?>
                <a href="dashboard/index.php" class="btn btn-primary"><i class="fas fa-th-large"></i> Go to Dashboard</a>
                <?php
            else: ?>
                <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Portal Login</a>
                <a href="register.php" class="btn btn-outline-white"><i class="fas fa-user-plus"></i> Join as Student</a>
                <?php
            endif; ?>
        </div>
    </div>
</section>

<section id="features" class="features">
    <div class="section-header">
        <span class="sub-title">Why Choose Us</span>
        <h2>Complete Academic Ecosystem</h2>
        <p>A modular system designed to adapt to the specific needs of modern educational institutions.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Student Centric</h3>
            <p>Comprehensive student profiles, academic history tracking, and automated performance analytics.</p>
        </div>
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3>Teacher Empowerment</h3>
            <p>Digital gradebooks, attendance management, and lesson planning tools at your fingertips.</p>
        </div>
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Role-Based Security</h3>
            <p>Granular access control for Admins, Headmasters, and Staff to ensure data integrity.</p>
        </div>
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>Payment Hub</h3>
            <p>Streamlined fee collections with instant receipt generation and financial reporting.</p>
        </div>
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>Academic Cycles</h3>
            <p>Effortlessly manage multiple academic years, terms, and promotion cycles.</p>
        </div>
        <div class="feature-card">
            <div class="card-icon-wrapper">
                <i class="fas fa-print"></i>
            </div>
            <h3>Automated Reporting</h3>
            <p>Generate professional reports, student transcripts, and payment receipts with one click.</p>
        </div>
    </div>
</section>

<section id="contact" class="contact-section">
    <div class="section-header">
        <h2>Get In Touch</h2>
        <p>Have questions about our multi-school platform? Our support team is here to help.</p>
    </div>
    <div class="contact-container">
        <div class="contact-info">
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <h3>Email Support</h3>
                <p><?php echo get_setting('contact_email', 'support@cbe.ac.tz'); ?></p>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <h3>Call Center</h3>
                <p>+255 123 456 789</p>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Headquarters</h3>
                <p>Dar es Salaam, Tanzania</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-banner">
    <div class="cta-content">
        <h2>Modernize Your Institution Today</h2>
        <p>Join the growing network of schools using CBE to digitize their legacy systems.</p>
        <div class="cta-actions">
            <a href="<?php echo $project_root; ?>register.php" class="btn btn-accent"><i class="fas fa-rocket"></i> Get
                Started for Free</a>
            <a href="<?php echo $project_root; ?>login.php" class="btn btn-outline-white"><i
                    class="fas fa-sign-in-alt"></i> Existing Account</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>