<header>
	<!--
    <?php get_template_part("template-parts/header", "logo"); ?>
    <?php get_template_part("template-parts/header", "menu-toggle"); ?>
    <?php get_template_part("template-parts/header", "menu"); ?>
	-->
    <h1>
		<a href="<?php echo get_option('home'); ?>">
       <?php bloginfo('name'); ?>
		</a>
	</h1>
	<div class="description">
		<?php bloginfo('description'); ?>
	</div>
</header>
