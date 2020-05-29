<article <?php post_class(); ?>>

    <h1 class="post-title">
        <?php the_title(); ?>
    </h1>

    <?php if( has_excerpt() ) : ?>
    <div class="post-excerpt">
        <?php the_excerpt(); ?>
    </div>
    <?php endif; ?>

    <div class="post-content">
        <?php the_content(); ?>
    </div>

    <?php get_template_part('template-parts/components/post-meta') ?>

</article>
