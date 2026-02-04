<?php
if (!defined('ABSPATH')) { exit; }
get_header();

$post_id = get_the_ID();
$meta = iq_get_video_meta($post_id);

$source_type = $meta['source_type'];
$mux_playback_id = $meta['mux_playback_id'];
$youtube_url = $meta['youtube_url'];
$asl_asset_url = $meta['asl_asset_url'];
$asl_default_position = $meta['asl_default_position'];
$is_paid = $meta['is_paid'];
$product_id = $meta['product_id'];

// Determine if user can watch (paid gating)
$can_watch = true;
if ($is_paid && $product_id > 0) {
  $can_watch = false;
  if (function_exists('wc_customer_bought_product') && is_user_logged_in()) {
    $user = wp_get_current_user();
    $can_watch = wc_customer_bought_product($user->user_email, $user->ID, $product_id);
  }
}

?>
<section class="iq-watch">
  <div class="iq-watch__main">
    <div class="iq-watch__playerShell <?php echo $asl_default_position === 'side' ? 'iq-asl--side' : 'iq-asl--below'; ?>"
         data-iq-source="<?php echo esc_attr($source_type); ?>"
         data-iq-mux="<?php echo esc_attr($mux_playback_id); ?>"
         data-iq-youtube="<?php echo esc_attr($youtube_url); ?>"
         data-iq-asl="<?php echo esc_url($asl_asset_url); ?>"
         data-iq-asl-default="<?php echo esc_attr($asl_default_position); ?>">

      <?php if (!$can_watch): ?>
        <div class="iq-gate">
          <h2>Purchase required</h2>
          <p class="iq-muted">This title is available in the store. Purchase to unlock playback.</p>
          <?php if (function_exists('wc_get_product') && $product_id > 0): ?>
            <a class="iq-btn" href="<?php echo esc_url(get_permalink($product_id)); ?>">Go to Purchase</a>
          <?php else: ?>
            <p class="iq-muted">WooCommerce is not active or product not linked.</p>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="iq-playerGrid">
          <div class="iq-videoWrap">
            <?php if ($source_type === 'youtube'): ?>
              <div id="iq-yt" class="iq-yt"></div>
            <?php else: ?>
              <video id="iq-main-video" class="iq-video" controls playsinline></video>
            <?php endif; ?>
          </div>

          <aside class="iq-aslPanel" aria-label="ASL avatar panel">
            <div class="iq-aslHead">
              <div class="iq-aslLabel">ASL</div>
              <div class="iq-aslControls">
                <button class="iq-pill" data-iq-action="asl-toggle" type="button">ASL On</button>
                <select class="iq-select" data-iq-action="asl-position" aria-label="ASL position">
                  <option value="below">Below</option>
                  <option value="side">Side</option>
                </select>
                <select class="iq-select" data-iq-action="asl-size" aria-label="ASL size">
                  <option value="sm">Small</option>
                  <option value="md" selected>Medium</option>
                  <option value="lg">Large</option>
                </select>
              </div>
            </div>
            <div class="iq-aslBody">
              <?php if (!empty($asl_asset_url)): ?>
                <video id="iq-asl-video" class="iq-aslVideo" muted playsinline></video>
              <?php else: ?>
                <div class="iq-aslEmpty">
                  <p><strong>ASL asset not linked yet.</strong></p>
                  <p class="iq-muted">Add an ASL avatar video URL in the IQ Video settings.</p>
                </div>
              <?php endif; ?>
            </div>
          </aside>
        </div>
      <?php endif; ?>

    </div>

    <div class="iq-watch__meta">
      <h1 class="iq-title"><?php the_title(); ?></h1>
      <div class="iq-metaRow">
        <span class="iq-muted"><?php echo esc_html(get_the_date()); ?></span>
        <span class="iq-dot">•</span>
        <span class="iq-muted"><?php echo esc_html(ucfirst($source_type)); ?></span>
        <?php if ($is_paid): ?>
          <span class="iq-dot">•</span>
          <span class="iq-badge iq-badge--store">Store</span>
        <?php endif; ?>
      </div>

      <div class="iq-desc">
        <?php the_content(); ?>
      </div>
    </div>
  </div>

  <div class="iq-watch__side">
    <div class="iq-sideCard">
      <h3>More to watch</h3>
      <?php
        $more = new WP_Query([
          'post_type' => 'iq_video',
          'posts_per_page' => 6,
          'post__not_in' => [$post_id],
          'post_status' => 'publish',
        ]);
        if ($more->have_posts()):
          echo '<div class="iq-sideList">';
          while ($more->have_posts()): $more->the_post();
            echo '<a class="iq-sideItem" href="'.esc_url(get_permalink()).'">';
            if (has_post_thumbnail()) { the_post_thumbnail('thumbnail'); } else { echo '<span class="iq-sidePh"></span>'; }
            echo '<span class="iq-sideTitle">'.esc_html(get_the_title()).'</span>';
            echo '</a>';
          endwhile;
          echo '</div>';
          wp_reset_postdata();
        else:
          echo '<p class="iq-muted">Add more titles to see recommendations here.</p>';
        endif;
      ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
