<article <?php post_class(btpl_base_theme()->get_post_classes()); ?>>
    <?php $h_tag = is_singular() ? 'h1' : 'h2';?>
    <<?php echo $h_tag; ?> class="post-title">
        <?php if ( is_singular() ) : ?>
            <?php the_title(); ?>
        <?php else : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        <?php endif; ?>
    </<?php echo $h_tag; ?>>

    <?php if( ! is_singular() || has_excerpt() ) : ?>
        <div class="post-excerpt">
            <?php the_excerpt(); ?>
        </div>
    <?php endif; ?>

    <?php if ( is_singular() ) : ?>
        <div class="post-content">
            <?php the_content(); ?>
        </div>
    <?php endif; ?>

    <?php get_template_part('template-parts/components/post-meta') ?>

</article>
