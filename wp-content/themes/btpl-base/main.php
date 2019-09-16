<div id="main">
    <div class="content-max-width">

        <?php
        if (have_posts()) {

            // Start the Loop
            while (have_posts()) {
                the_post();

                // TODO: figure out a good template selection strategy to use here
                get_template_part("template-parts/content/post", "default");

                /*
                 * Include the Post-Format-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                 */
                //if(is_front_page()) {
                //  get_template_part( "template-parts/content", "frontpage" );
                //} else {
                //  get_template_part( "template-parts/content" );
                //}
            }

            the_posts_navigation();
            the_posts_pagination();

        } else {

            get_template_part("template-parts/content/post", "none");

        }
        ?>

    </div>
</div><!-- #main -->
