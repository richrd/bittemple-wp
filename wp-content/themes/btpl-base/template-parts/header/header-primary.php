<header>

    <?php get_template_part('template-parts/header/top-bar'); ?>

    <div class="header-content content-max-width">
        <h1 class="site-title">
            <a href="<?php echo get_option('home'); ?>">
                <?php
                // Use the logo if it's set in customizer, otherwise just show a text header
                $custom_logo_id = get_theme_mod('custom_logo');
                $logo = wp_get_attachment_image_src($custom_logo_id , 'full');

                if (has_custom_logo()) {
                    echo '<img class="custom-logo" src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
                } else {
                    echo '<span>'. get_bloginfo('name') .'</span>';
                }
                ?>
            </a>
        </h1>

        <div class="site-description">
            <?php bloginfo('description'); ?>
        </div>

        <?php get_template_part('template-parts/components/main-menu-toggle'); ?>
        <?php get_template_part('template-parts/components/main-menu'); ?>

        <?php
        // TODO: optionally add search control
        // get_template_part( 'template-parts/components/search-form-default' );
        ?>
    </div>

</header>
