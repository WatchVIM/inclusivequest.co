<?php
if (!defined('ABSPATH')) { exit; }

function iq_add_meta_boxes() {
  add_meta_box(
    'iq_video_settings',
    'InclusiveQuest — Video Settings',
    'iq_video_settings_metabox',
    'iq_video',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'iq_add_meta_boxes');

function iq_video_settings_metabox($post) {
  wp_nonce_field('iq_video_settings_save', 'iq_video_settings_nonce');

  $source_type = get_post_meta($post->ID, 'iq_source_type', true) ?: 'mux';
  $mux_playback_id = get_post_meta($post->ID, 'iq_mux_playback_id', true);
  $youtube_url = get_post_meta($post->ID, 'iq_youtube_url', true);
  $asl_asset_url = get_post_meta($post->ID, 'iq_asl_asset_url', true);
  $asl_default_position = get_post_meta($post->ID, 'iq_asl_default_position', true) ?: 'below';
  $is_paid = (int) get_post_meta($post->ID, 'iq_is_paid', true);
  $product_id = (int) get_post_meta($post->ID, 'iq_product_id', true);

  ?>
  <style>
    .iq-field{margin:10px 0}
    .iq-field label{font-weight:700; display:block; margin-bottom:6px}
    .iq-field input[type="text"], .iq-field input[type="url"], .iq-field select{width:100%; max-width:760px}
    .iq-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px}
    @media (max-width: 900px){ .iq-grid{grid-template-columns:1fr} }
    .iq-help{color:#555;margin-top:4px}
  </style>

  <div class="iq-grid">
    <div>
      <div class="iq-field">
        <label for="iq_source_type">Source Type</label>
        <select name="iq_source_type" id="iq_source_type">
          <option value="mux" <?php selected($source_type, 'mux'); ?>>Mux HLS (Playback ID)</option>
          <option value="youtube" <?php selected($source_type, 'youtube'); ?>>YouTube URL</option>
        </select>
        <div class="iq-help">Mux uses Playback ID (HLS) for hosted titles. YouTube is for embedded channel videos.</div>
      </div>

      <div class="iq-field">
        <label for="iq_mux_playback_id">Mux Playback ID</label>
        <input type="text" name="iq_mux_playback_id" id="iq_mux_playback_id" value="<?php echo esc_attr($mux_playback_id); ?>" placeholder="e.g., abc123...">
      </div>

      <div class="iq-field">
        <label for="iq_youtube_url">YouTube URL</label>
        <input type="url" name="iq_youtube_url" id="iq_youtube_url" value="<?php echo esc_url($youtube_url); ?>" placeholder="https://www.youtube.com/watch?v=...">
      </div>
    </div>

    <div>
      <div class="iq-field">
        <label for="iq_asl_asset_url">ASL Avatar Video URL (MP4/WebM)</label>
        <input type="url" name="iq_asl_asset_url" id="iq_asl_asset_url" value="<?php echo esc_url($asl_asset_url); ?>" placeholder="https://.../asl_avatar.mp4">
        <div class="iq-help">This should be aligned to the main video timeline for basic sync.</div>
      </div>

      <div class="iq-field">
        <label for="iq_asl_default_position">ASL Default Placement</label>
        <select name="iq_asl_default_position" id="iq_asl_default_position">
          <option value="below" <?php selected($asl_default_position, 'below'); ?>>Below the video</option>
          <option value="side" <?php selected($asl_default_position, 'side'); ?>>Side-by-side</option>
        </select>
      </div>

      <div class="iq-field">
        <label>
          <input type="checkbox" name="iq_is_paid" value="1" <?php checked($is_paid, 1); ?>>
          Store / Paid title (requires WooCommerce purchase)
        </label>
      </div>

      <div class="iq-field">
        <label for="iq_product_id">WooCommerce Product ID (required if Paid)</label>
        <input type="text" name="iq_product_id" id="iq_product_id" value="<?php echo esc_attr($product_id); ?>" placeholder="e.g., 1234">
        <div class="iq-help">Create a WooCommerce product for the title, then paste the Product ID here.</div>
      </div>
    </div>
  </div>
  <?php
}

function iq_extract_youtube_id($url) {
  if (!$url) return '';
  if (preg_match('/[?&]v=([^&#]+)/', $url, $m)) return $m[1];
  if (preg_match('~youtu\.be/([^?&#]+)~', $url, $m)) return $m[1];
  if (preg_match('~youtube\.com/embed/([^?&#]+)~', $url, $m)) return $m[1];
  return '';
}

function iq_save_video_meta($post_id) {
  if (!isset($_POST['iq_video_settings_nonce']) || !wp_verify_nonce($_POST['iq_video_settings_nonce'], 'iq_video_settings_save')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $yt_url = esc_url_raw($_POST['iq_youtube_url'] ?? '');
$yt_id = iq_extract_youtube_id($yt_url);

$fields = [
  'iq_source_type' => sanitize_text_field($_POST['iq_source_type'] ?? 'mux'),
  'iq_mux_playback_id' => sanitize_text_field($_POST['iq_mux_playback_id'] ?? ''),
  'iq_youtube_url' => $yt_url,
  'iq_youtube_id' => sanitize_text_field($yt_id),
  'iq_asl_asset_url' => esc_url_raw($_POST['iq_asl_asset_url'] ?? ''),
  'iq_asl_default_position' => sanitize_text_field($_POST['iq_asl_default_position'] ?? 'below'),
  'iq_is_paid' => isset($_POST['iq_is_paid']) ? 1 : 0,
  'iq_product_id' => (int) ($_POST['iq_product_id'] ?? 0),
];

  foreach ($fields as $k => $v) {
    update_post_meta($post_id, $k, $v);
  }
}
add_action('save_post_iq_video', 'iq_save_video_meta');
