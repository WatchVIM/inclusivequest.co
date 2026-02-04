<?php
if (!defined('ABSPATH')) { exit; }
get_header();
?>

<section class="iq-hero">
  <div class="iq-hero__copy">
    <h1>Watch accessible shows with ASL</h1>
    <p>InclusiveQuest brings together a Hulu-style store and a YouTube-style channel feed — with ASL avatar support shown below or beside the video.</p>
    <div class="iq-hero__cta">
      <a class="iq-btn" href="<?php echo esc_url(get_post_type_archive_link('iq_video')); ?>">Browse Titles</a>
      <a class="iq-btn iq-btn--ghost" href="<?php echo esc_url(home_url('/channels')); ?>">Explore Channels</a>
    </div>
  </div>
  <div class="iq-hero__art">
    <div class="iq-hero__panel">
      <div class="iq-hero__panelTop"></div>
      <div class="iq-hero__panelBottom"></div>
    </div>
  </div>
</section>

<?php echo do_shortcode('[iq_featured_row title="Popular on InclusiveQuest" count="12"]'); ?>

<section class="iq-row">
  <div class="iq-row__head">
    <h2>From Our Channels</h2>
    <p class="iq-muted">YouTube channel feed rendered inside InclusiveQuest.</p>
  </div>
  <div class="iq-block">
  <?php if (function_exists('iq_get_configured_channels')): ?>
    <?php $chs = iq_get_configured_channels(); $cid = $chs[0] ?? ''; ?>
    <?php if (!empty($cid) && function_exists('iq_youtube_channel_feed_render')): ?>
      <?php echo iq_youtube_channel_feed_render($cid, 10, true); ?>
    <?php else: ?>
      <?php echo do_shortcode('[iq_youtube_channel_feed channel="" max="10" title="Latest uploads"]'); ?>
    <?php endif; ?>
  <?php else: ?>
    <?php echo do_shortcode('[iq_youtube_channel_feed channel="" max="10" title="Latest uploads"]'); ?>
  <?php endif; ?>
  <p class="iq-muted" style="margin-top:10px;">Set your YouTube API Key + Channel IDs in WP Admin → InclusiveQuest.</p>
</div>
</section>

<?php get_footer(); ?>
