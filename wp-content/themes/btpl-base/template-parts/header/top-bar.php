<div class="top-bar content-max-width">

    <?php if (is_active_sidebar('top-bar-1')): ?>
        <div class="widget-area"><?php dynamic_sidebar('top-bar-1'); ?></div>
    <?php endif; ?>

    <?php if (has_nav_menu('top-bar-menu')): ?>
        <div class="top-bar-menu">
            <?php wp_nav_menu(array(
                'theme_location' => 'top-bar-menu',
                'menu_class' => 'menu',
            )); ?>
        </div>
    <?php endif; ?>

    <?php if (is_active_sidebar('top-bar-2')): ?>
        <div class="widget-area"><?php dynamic_sidebar('top-bar-2'); ?></div>
    <?php endif; ?>
</div>
