# InclusiveQuest.co — WordPress Repo Root (Theme + Core Plugin)

This repo contains:
- **WordPress theme**: `wp-content/themes/inclusivequest`
- **WordPress plugin**: `wp-content/plugins/inclusivequest-core`

The platform is designed as a **Hulu + YouTube fusion**:
- **Store / Paid titles**: use WooCommerce products (rent/buy modeled as products)
- **Hosted playback**: **Mux HLS** (via playbackId -> HLS URL)
- **YouTube channels feed**: pulls latest videos from channel(s) via YouTube Data API v3
- **ASL avatar panel**: displayed **below** or **side-by-side** with the video, with basic sync (video-to-video or YouTube-to-video)

> Note: For YouTube embeds, the ASL panel is rendered **beside or below** the YouTube player (not on top).

---

## Quick start (local dev)
### Option A: Docker (simple)
1. Install Docker Desktop
2. Run:
```bash
docker compose up -d
```
3. Visit: http://localhost:8080
4. Log in: admin / admin (set in docker-compose.yml)

### Option B: Existing WordPress hosting
Upload:
- `wp-content/themes/inclusivequest`
- `wp-content/plugins/inclusivequest-core`

Then in WordPress:
1. Activate **InclusiveQuest Core** plugin
2. Activate **InclusiveQuest** theme
3. (Optional) Install/Activate **WooCommerce** for paid titles
4. InclusiveQuest settings: set **YouTube API Key** (WP Admin → InclusiveQuest)

---

## Content model
A custom post type **IQ Videos** (`iq_video`) powers watch pages.

Each IQ Video has metadata:
- `iq_source_type`: `mux` or `youtube`
- `iq_mux_playback_id`: e.g. `abc123...` (Mux Playback ID)
- `iq_youtube_url`: full URL
- `iq_asl_asset_url`: URL to an ASL avatar video (MP4/WebM) aligned to the main content
- `iq_asl_default_position`: `below` or `side`
- `iq_is_paid`: 0/1
- `iq_product_id`: WooCommerce product ID for purchase gating

---

## Where to customize
- Watch page template: `wp-content/themes/inclusivequest/single-iq_video.php`
- Watch page behavior + ASL sync: `wp-content/themes/inclusivequest/assets/js/watch.js`
- YouTube feed shortcode + caching: `wp-content/plugins/inclusivequest-core/includes/youtube-feed.php`

---

## Shortcodes
- `[iq_youtube_channel_feed channel="UCxxxx" max="12" title="From Our Channels"]`
- `[iq_featured_row title="Popular" count="12"]`

---

## Deploy to GitHub
Push this repo to GitHub, then deploy the theme/plugin to your WordPress host (via SFTP, Git deploy, or a plugin like WP Pusher).

---

## Security note
Do not commit secrets. Set API keys in WP Admin → InclusiveQuest Settings.
