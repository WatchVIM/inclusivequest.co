<?php
/**
 * Template Name: Channels
 * Description: InclusiveQuest Channels directory (YouTube channel feed inside site)
 */
if (!defined('ABSPATH')) { exit; }
get_header();
?>

<section class="iq-pageHead">
  <h1>Channels</h1>
  <p class="iq-muted">Browse YouTube channels curated for InclusiveQuest. Open a video to watch with ASL panel below or side-by-side.</p>
</section>

<?php if (function_exists('iq_get_configured_channels')): ?>
  <?php
    $channels = iq_get_configured_channels(); // array of channel IDs
    if (empty($channels)) {
      echo '<div class="iq-block"><p class="iq-muted">No channels configured yet. Add Channel IDs in WP Admin → InclusiveQuest → YouTube Channels.</p></div>';
    } else {
      foreach ($channels as $cid):
        $channel_info = function_exists('iq_youtube_get_channel_info') ? iq_youtube_get_channel_info($cid) : null;
        $title = $channel_info['title'] ?? $cid;
        $thumb = $channel_info['thumb'] ?? '';
        $desc  = $channel_info['description'] ?? '';
  ?>
    <section class="iq-channelBlock">
      <div class="iq-channelHead">
        <div class="iq-channelId">
          <?php if (!empty($thumb)): ?>
            <img class="iq-channelThumb" src="<?php echo esc_url($thumb); ?>" alt="">
          <?php else: ?>
            <div class="iq-channelThumb iq-channelThumb--ph"></div>
          <?php endif; ?>
          <div>
            <h2 class="iq-channelTitle"><?php echo esc_html($title); ?></h2>
            <?php if (!empty($desc)): ?>
              <p class="iq-muted iq-channelDesc"><?php echo esc_html(wp_trim_words($desc, 18)); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="iq-channelActions">
          <a class="iq-btn iq-btn--ghost" href="<?php echo esc_url('https://www.youtube.com/channel/' . $cid); ?>" target="_blank" rel="noopener">View on YouTube</a>
        </div>
      </div>

      <div class="iq-block">
        <?php
          // Render internal feed (links to internal watch pages where possible)
          echo iq_youtube_channel_feed_render($cid, 12, true);
        ?>
      </div>
    </section>
  <?php endforeach; } ?>
<?php else: ?>
  <div class="iq-block">
    <p class="iq-muted">InclusiveQuest Core plugin is required for channel feeds.</p>
  </div>
<?php endif; ?>

<?php get_footer(); ?>
