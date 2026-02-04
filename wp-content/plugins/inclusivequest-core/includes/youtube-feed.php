<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Shortcode wrapper for channel feed renderer.
 * [iq_youtube_channel_feed channel="UCxxxx" max="12" title="Latest uploads"]
 */
function iq_youtube_channel_feed_shortcode($atts) {
  $atts = shortcode_atts([
    'channel' => '',
    'max' => 12,
    'title' => '',
  ], $atts);

  $channel = $atts['channel'] ?: get_option('iq_default_youtube_channel', '');
  $max = max(1, min(24, (int) $atts['max']));

  if (empty($channel)) {
    return '<div class="iq-muted">YouTube feed not configured. Add a Channel ID in WP Admin → InclusiveQuest.</div>';
  }

  $html = '';
  if (!empty($atts['title'])) {
    $html .= '<h3 style="margin:0 0 10px">' . esc_html($atts['title']) . '</h3>';
  }

  if (!function_exists('iq_youtube_channel_feed_render')) {
    return $html . '<div class="iq-muted">YouTube feed renderer not loaded.</div>';
  }

  $html .= iq_youtube_channel_feed_render($channel, $max, true);
  return $html;
}
add_shortcode('iq_youtube_channel_feed', 'iq_youtube_channel_feed_shortcode');
