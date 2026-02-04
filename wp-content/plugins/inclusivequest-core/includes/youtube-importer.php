<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Admin: YouTube Importer
 * Creates internal IQ Video posts from a channel feed, so videos can be watched inside InclusiveQuest watch pages
 * (with ASL panel below/side-by-side).
 */

function iq_add_importer_submenu() {
  add_submenu_page(
    'iq-settings',
    'YouTube Import',
    'YouTube Import',
    'manage_options',
    'iq-youtube-import',
    'iq_render_importer_page'
  );
}
add_action('admin_menu', 'iq_add_importer_submenu');

function iq_render_importer_page() {
  if (!current_user_can('manage_options')) return;

  $channel = sanitize_text_field($_GET['channel'] ?? '') ?: (iq_get_configured_channels()[0] ?? '');
  $max = isset($_POST['iq_import_max']) ? (int) $_POST['iq_import_max'] : 12;
  $do_import = isset($_POST['iq_do_import']);
  $sideload = isset($_POST['iq_sideload_thumb']) ? 1 : 0;

  echo '<div class="wrap"><h1>YouTube Import → IQ Videos</h1>';
  echo '<p>Import latest YouTube uploads as internal watch pages (IQ Videos). These pages can display ASL avatar video below or side-by-side.</p>';

  echo '<form method="post" style="margin-top:14px;">';
  echo '<table class="form-table" role="presentation">';
  echo '<tr><th scope="row"><label>Channel ID</label></th><td>';
  echo '<input type="text" name="iq_channel" value="' . esc_attr($channel) . '" class="regular-text" placeholder="UCxxxx">';
  echo '<p class="description">Use a Channel ID (starts with UC...).</p>';
  echo '</td></tr>';

  echo '<tr><th scope="row"><label>Max videos</label></th><td>';
  echo '<input type="number" name="iq_import_max" value="' . esc_attr($max) . '" min="1" max="24">';
  echo '</td></tr>';

  echo '<tr><th scope="row"><label>Featured images</label></th><td>';
  echo '<label><input type="checkbox" name="iq_sideload_thumb" value="1" ' . checked($sideload, 1, false) . '> Download YouTube thumbnails into Media Library</label>';
  echo '<p class="description">Optional. If disabled, we store a thumbnail URL in post meta for display.</p>';
  echo '</td></tr>';

  submit_button('Import latest videos', 'primary', 'iq_do_import');
  echo '</table></form>';

  // Handle import
  if ($do_import) {
    $channel = sanitize_text_field($_POST['iq_channel'] ?? $channel);
    $videos = iq_youtube_get_latest_videos($channel, $max);

    if (empty($videos)) {
      echo '<div class="notice notice-warning"><p>No videos found or API not configured.</p></div></div>';
      return;
    }

    $created = 0; $skipped = 0; $updated = 0;
    foreach ($videos as $v) {
      $vid = $v['videoId'];
      $existing = iq_find_iq_video_by_youtube_id($vid);

      $title = wp_strip_all_tags($v['title']);
      $thumb = $v['thumb'];
      $yt_url = 'https://www.youtube.com/watch?v=' . $vid;

      if ($existing) {
        // Update title/meta if needed
        wp_update_post([
          'ID' => $existing,
          'post_title' => $title
        ]);
        update_post_meta($existing, 'iq_source_type', 'youtube');
        update_post_meta($existing, 'iq_youtube_url', esc_url_raw($yt_url));
        update_post_meta($existing, 'iq_youtube_id', sanitize_text_field($vid));
        update_post_meta($existing, 'iq_thumb_url', esc_url_raw($thumb));
        $updated++;
        continue;
      }

      $post_id = wp_insert_post([
        'post_type' => 'iq_video',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_content' => '',
      ]);

      if (is_wp_error($post_id) || !$post_id) {
        $skipped++;
        continue;
      }

      update_post_meta($post_id, 'iq_source_type', 'youtube');
      update_post_meta($post_id, 'iq_youtube_url', esc_url_raw($yt_url));
      update_post_meta($post_id, 'iq_youtube_id', sanitize_text_field($vid));
      update_post_meta($post_id, 'iq_thumb_url', esc_url_raw($thumb));
      update_post_meta($post_id, 'iq_asl_default_position', 'below');

      if ($sideload && !empty($thumb)) {
        // Attempt to sideload and set featured image
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($thumb);
        if (!is_wp_error($tmp)) {
          $file_array = [
            'name' => 'yt-' . $vid . '.jpg',
            'tmp_name' => $tmp
          ];
          $id = media_handle_sideload($file_array, $post_id);
          if (!is_wp_error($id)) {
            set_post_thumbnail($post_id, $id);
          } else {
            @unlink($tmp);
          }
        }
      }

      $created++;
    }

    echo '<div class="notice notice-success"><p>Import complete. Created: ' . esc_html($created) . ' • Updated: ' . esc_html($updated) . ' • Skipped: ' . esc_html($skipped) . '</p></div>';

    echo '<p><a class="button button-secondary" href="' . esc_url(get_post_type_archive_link('iq_video')) . '">View IQ Videos</a></p>';
  }

  echo '</div>';
}
