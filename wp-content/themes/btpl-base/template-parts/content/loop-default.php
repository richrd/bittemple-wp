<?php
// Start the Loop
while (have_posts()) {
    the_post();
    get_template_part( 'template-parts/content', get_post_type() );
}

// TODO: check these:
the_posts_navigation();
the_posts_pagination();
