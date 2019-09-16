<article <?php post_class(); ?>>

    <h1 class="post-title">
        <?php the_title(); ?>
    </h1>

    <div class="post-content">
        <?php the_content(); ?>
    </div>

    <div class="post-metadata">
        <div class="post-date">
            <?php
            # When there are multiple posts on a page published under the SAME DAY, the_date() only
            # displays the date for the first post. Thats why we use get_the_date().
            ?>
            <i class="fa fa-calendar"></i> <?php echo get_the_date();?>
        </div>

        <div class="post-categories">
            <i class="fa fa-folder"></i> <?php the_category(); ?>
        </div>

        <?php if (has_tag()) { ?>
        <div class="post-tags">
            <i class="fa fa-tag"></i> <?php the_tags( '', ' ', '' ); ?> 
        </div>
        <?php } ?>
    </div>

</article>
