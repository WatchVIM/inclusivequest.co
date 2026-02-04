<?php
// Fallback template
if (!defined('ABSPATH')) { exit; }
get_header();
?>

<section class="iq-pageHead">
  <h1><?php bloginfo('name'); ?></h1>
  <p class="iq-muted"><?php bloginfo('description'); ?></p>
</section>

<?php if (have_posts()): while (have_posts()): the_post(); ?>
  <article class="iq-block" style="margin:12px 0;">
    <h2 style="margin:0 0 8px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="iq-muted"><?php the_excerpt(); ?></div>
  </article>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
