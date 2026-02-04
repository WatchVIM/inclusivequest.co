<?php
if (!defined('ABSPATH')) { exit; }

/**
 * YouTube API helpers + caching.
 * Uses YouTube Data API v3.
 */

function iq_get_configured_channels() {
  $raw = get_option('iq_youtube_channels', '');
  $raw = trim($raw);
  if (empty($raw)) {
    $single = trim(get_option('iq_default_youtube_channel', ''));
    return $single ? [$single] : [];
  }
  // split by commas/newlines/spaces
  $parts = preg_split('/[\s,]+/', $raw);
  $out = [];
  foreach ($parts as $p) {
    $p = trim($p);
    if ($p) $out[] = $p;
  }
  return array_values(array_unique($out));
}

/**
 * Fetch channel info (title, thumbnail, description)
 */
function iq_youtube_get_channel_info($channel_id) {
  $api_key = get_option('iq_youtube_api_key', '');
  if (!$api_key || !$channel_id) return null;

  $cache_key = 'iq_yt_channel_' . md5($channel_id);
  $cached = get_transient($cache_key);
  if ($cached) return $cached;

  $url = add_query_arg([
    'part' => 'snippet',
    'id' => $channel_id,
    'key' => $api_key,
  ], 'https://www.googleapis.com/youtube/v3/channels');

  $res = wp_remote_get($url, ['timeout' => 12]);
  if (is_wp_error($res)) return null;

  $json = json_decode(wp_remote_retrieve_body($res), true);
  $items = $json['items'] ?? [];
  if (empty($items)) return null;

  $snip = $items[0]['snippet'] ?? [];
  $info = [
    'title' => $snip['title'] ?? $channel_id,
    'description' => $snip['description'] ?? '',
    'thumb' => $snip['thumbnails']['default']['url'] ?? ($snip['thumbnails']['medium']['url'] ?? ''),
  ];

  set_transient($cache_key, $info, 6 * HOUR_IN_SECONDS);
  return $info;
}

/**
 * Fetch latest videos for channel. Returns array of {videoId,title,thumb,publishedAt}
 */
function iq_youtube_get_latest_videos($channel_id, $max = 12) {
  $api_key = get_option('iq_youtube_api_key', '');
  if (!$api_key || !$channel_id) return [];

  $max = max(1, min(24, (int)$max));
  $cache_key = 'iq_yt_latest_' . md5($channel_id . '|' . $max);
  $cached = get_transient($cache_key);
  if ($cached) return $cached;

  $url = add_query_arg([
    'part' => 'snippet',
    'channelId' => $channel_id,
    'maxResults' => $max,
    'order' => 'date',
    'type' => 'video',
    'key' => $api_key,
  ], 'https://www.googleapis.com/youtube/v3/search');

  $res = wp_remote_get($url, ['timeout' => 12]);
  if (is_wp_error($res)) return [];

  $json = json_decode(wp_remote_retrieve_body($res), true);
  $items = $json['items'] ?? [];
  $out = [];

  foreach ($items as $item) {
    $vid = $item['id']['videoId'] ?? '';
    if (!$vid) continue;
    $snip = $item['snippet'] ?? [];
    $out[] = [
      'videoId' => $vid,
      'title' => $snip['title'] ?? 'Untitled',
      'thumb' => $snip['thumbnails']['medium']['url'] ?? ($snip['thumbnails']['default']['url'] ?? ''),
      'publishedAt' => $snip['publishedAt'] ?? '',
      'channelTitle' => $snip['channelTitle'] ?? '',
    ];
  }

  set_transient($cache_key, $out, 15 * MINUTE_IN_SECONDS);
  return $out;
}

/**
 * Render a channel feed block.
 * $internal_links = true => link to internal IQ Video pages if available; otherwise link to importer.
 */
function iq_youtube_channel_feed_render($channel_id, $max = 12, $internal_links = true) {
  $videos = iq_youtube_get_latest_videos($channel_id, $max);
  if (empty($videos)) {
    return '<div class="iq-muted">No videos found.</div>';
  }

  $out = '<div class="iq-ytFeed">';
  foreach ($videos as $v) {
    $vid = $v['videoId'];
    $title = $v['title'];
    $thumb = $v['thumb'];
    $watch_url = esc_url(add_query_arg(['v' => $vid], 'https://www.youtube.com/watch'));

    $internal = null;
    if ($internal_links) {
      $internal = iq_find_iq_video_by_youtube_id($vid);
    }

    if ($internal) {
      $href = esc_url(get_permalink($internal));
      $tag = 'Watch';
    } else {
      // send to importer page (admin) if logged-in admin; else fall back to YouTube
      if (current_user_can('manage_options')) {
        $href = esc_url(admin_url('admin.php?page=iq-youtube-import&channel=' . urlencode($channel_id)));
        $tag = 'Import';
      } else {
        $href = $watch_url;
        $tag = 'YouTube';
      }
    }

    $out .= '<a class="iq-ytItem" href="' . $href . '"' . (!$internal && !current_user_can('manage_options') ? ' target="_blank" rel="noopener"' : '') . '>';
    $out .= '<span class="iq-ytThumb" style="background-image:url(' . esc_url($thumb) . ')"></span>';
    $out .= '<span class="iq-ytMeta">';
    $out .= '<span class="iq-ytTitle">' . esc_html($title) . '</span>';
    $out .= '<span class="iq-ytTag">' . esc_html($tag) . '</span>';
    $out .= '</span>';
    $out .= '</a>';
  }
  $out .= '</div>';

  $out .= '<style>
    .iq-ytFeed{display:grid; grid-template-columns:repeat(4,1fr); gap:10px}
    .iq-ytItem{border:1px solid rgba(255,255,255,.08); border-radius:14px; overflow:hidden; background:rgba(255,255,255,.03)}
    .iq-ytItem:hover{background:rgba(255,255,255,.05)}
    .iq-ytThumb{display:block; aspect-ratio:16/9; background-size:cover; background-position:center}
    .iq-ytMeta{display:block; padding:10px}
    .iq-ytTitle{display:block; font-weight:900; font-size:13px; line-height:1.2}
    .iq-ytTag{display:inline-block; margin-top:6px; font-size:12px; color:#94a3b8}
    @media (max-width: 980px){.iq-ytFeed{grid-template-columns:repeat(2,1fr)}}
  </style>';

  return $out;
}

/**
 * Find existing IQ Video post by YouTube video ID
 */
function iq_find_iq_video_by_youtube_id($youtube_id) {
  $q = new WP_Query([
    'post_type' => 'iq_video',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'meta_query' => [
      [
        'key' => 'iq_youtube_id',
        'value' => $youtube_id,
        'compare' => '='
      ]
    ]
  ]);
  if ($q->have_posts()) {
    $q->the_post();
    $id = get_the_ID();
    wp_reset_postdata();
    return $id;
  }
  wp_reset_postdata();
  return null;
}
