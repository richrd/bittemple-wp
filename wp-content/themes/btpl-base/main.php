<div id="main" class="main-wrapper">

    <div class="sidebar sidebar-left">
        <?php if (is_active_sidebar('main-sidebar-left')): ?>
            <div class="widget-area"><?php dynamic_sidebar('main-sidebar-left'); ?></div>
        <?php endif; ?>
    </div>

    <main>
        <?php
        if (have_posts()) {

            // Start the Loop
            while (have_posts()) {
                the_post();

                get_template_part( 'template-parts/content', get_post_type() );
            }

            the_posts_navigation();
            the_posts_pagination();

        } else {
            // No content found
            get_template_part("template-parts/content/post", "none");
        }
        ?>
    </main>

    <div class="sidebar sidebar-right">
        <?php if (is_active_sidebar('main-sidebar-right')): ?>
            <div class="widget-area"><?php dynamic_sidebar('main-sidebar-right'); ?></div>
        <?php endif; ?>
    </div>

</div><!-- #main -->
