<?php
// includes/footer.php
?>
<?php if (is_logged_in() && !isset($plain_layout)): ?>
    </div> <!-- .main-content -->
    </main> <!-- .content-area -->
    </div> <!-- .app-container -->
<?php endif; ?>

<footer
    style="text-align: center; padding: 1.5rem; color: #6b7280; font-size: 0.875rem; border-top: 1px solid #f3f4f6; margin-top: auto;">
    <?php echo get_setting('footer_text', '© 2026 CBE Boilerplate'); ?>
</footer>

<script src="<?php echo $project_root; ?>assets/js/app.js"></script>
</body>

</html>