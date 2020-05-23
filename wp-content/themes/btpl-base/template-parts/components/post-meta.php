<div class="post-meta">
    <div class="post-date">
        <?php
        # When there are multiple posts on a page published under the SAME DAY, the_date() only
        # displays the date for the first post. Thats why we use get_the_date().
        ?>
        <?php echo get_the_date();?>
    </div>

    <div class="post-categories">
        <?php the_category(); ?>
    </div>

    <?php if (has_tag()) { ?>
        <div class="post-tags">
            <?php the_tags( '', ' ', '' ); ?> 
        </div>
    <?php } ?>
</div>
