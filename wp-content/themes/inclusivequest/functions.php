<?php
/**
 * InclusiveQuest Theme functions
 */

if (!defined('ABSPATH')) { exit; }

function iq_theme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('woocommerce');
  register_nav_menus([
    'primary' => __('Primary Menu', 'inclusivequest')
  ]);
}
add_action('after_setup_theme', 'iq_theme_setup');

function iq_enqueue_assets() {
  $ver = wp_get_theme()->get('Version');
  wp_enqueue_style('iq-app', get_template_directory_uri() . '/assets/css/app.css', [], $ver);

  // Watch page JS (sync + toggles)
  if (is_singular('iq_video')) {
    wp_enqueue_script('iq-watch', get_template_directory_uri() . '/assets/js/watch.js', [], $ver, true);
  }
}
add_action('wp_enqueue_scripts', 'iq_enqueue_assets');

/**
 * Helper: Get IQ Video meta with defaults
 */
function iq_get_video_meta($post_id) {
  return [
    'source_type' => get_post_meta($post_id, 'iq_source_type', true) ?: 'mux',
    'mux_playback_id' => get_post_meta($post_id, 'iq_mux_playback_id', true),
    'youtube_url' => get_post_meta($post_id, 'iq_youtube_url', true),
    'asl_asset_url' => get_post_meta($post_id, 'iq_asl_asset_url', true),
    'asl_default_position' => get_post_meta($post_id, 'iq_asl_default_position', true) ?: 'below',
    'is_paid' => (int) get_post_meta($post_id, 'iq_is_paid', true),
    'product_id' => (int) get_post_meta($post_id, 'iq_product_id', true),
  ];
}

/**
 * Optional: Shortcode to render a row of IQ Videos.
 * Usage: [iq_featured_row title="Popular" count="12"]
 */
function iq_featured_row_shortcode($atts) {
  $atts = shortcode_atts([
    'title' => 'Featured',
    'count' => 10,
  ], $atts);

  $q = new WP_Query([
    'post_type' => 'iq_video',
    'posts_per_page' => (int) $atts['count'],
    'post_status' => 'publish',
  ]);

  ob_start(); ?>
  <section class="iq-row">
    <div class="iq-row__head">
      <h2><?php echo esc_html($atts['title']); ?></h2>
    </div>
    <div class="iq-row__grid">
      <?php while ($q->have_posts()): $q->the_post(); ?>
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
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </section>
  <?php
  return ob_get_clean();
}
add_shortcode('iq_featured_row', 'iq_featured_row_shortcode');
