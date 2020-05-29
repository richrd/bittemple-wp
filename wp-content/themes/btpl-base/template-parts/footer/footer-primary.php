<footer>

    <div class="footer-wrapper">
        <div class="widgets widgets-top">
            <?php if (is_active_sidebar('footer-top-row')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-top-row'); ?></div>
            <?php endif; ?>
        </div>

        <div class="widgets widget-columns" data-max-columns="<?php echo btpl_base_theme()->get_setting_value('max_footer_columns') ?>">
            <?php if (is_active_sidebar('footer-column-1')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-1'); ?></div>
            <?php endif; ?>
            <?php if (is_active_sidebar('footer-column-2')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-2'); ?></div>
            <?php endif; ?>
            <?php if (is_active_sidebar('footer-column-3')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-3'); ?></div>
            <?php endif; ?>
            <?php if (is_active_sidebar('footer-column-4')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-4'); ?></div>
            <?php endif; ?>
            <?php if (is_active_sidebar('footer-column-5')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-5'); ?></div>
            <?php endif; ?>
            <?php if (is_active_sidebar('footer-column-6')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-column-6'); ?></div>
            <?php endif; ?>
        </div>

        <div class="widgets widgets-bottom">
            <?php if (is_active_sidebar('footer-bottom-row')): ?>
                <div class="widget-area"><?php dynamic_sidebar('footer-bottom-row'); ?></div>
            <?php endif; ?>
        </div>
    </div>

</footer>
