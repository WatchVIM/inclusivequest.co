<?php
if (!defined('ABSPATH')) { exit; }
get_header();
?>

<section class="iq-pageHead">
  <h1>Titles</h1>
  <p class="iq-muted">Movies, series episodes, podcasts and more — with ASL avatar support.</p>
</section>

<div class="iq-row__grid">
  <?php if (have_posts()): while (have_posts()): the_post(); ?>
    <a class="iq-card" href="<?php the_permalink(); ?>">
      <div class="iq-card__thumb">
        <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large'); } else { $t = get_post_meta(get_the_ID(), 'iq_thumb_url', true); if (!empty($t)) { echo '<img src="'.esc_url($t).'" alt="" style="width:100%;height:100%;object-fit:cover;">'; } else { ?>
          <div class="iq-thumb--ph"></div>
        <?php } } ?>
        <span class="iq-badge">ASL</span>
      </div>
      <div class="iq-card__meta">
        <div class="iq-card__title"><?php the_title(); ?></div>
      </div>
    </a>
  <?php endwhile; else: ?>
    <p class="iq-muted">No titles yet.</p>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
