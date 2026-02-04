# Content Setup Checklist

## 1) Create navigation
WP Admin → Appearance → Menus
- Home: /
- Store: /store (WooCommerce shop page)
- Channels: /channels (create a page and place [iq_youtube_channel_feed])
- My Watchlist: optional page

## 2) Configure InclusiveQuest settings
WP Admin → InclusiveQuest
- YouTube API Key
- Default YouTube Channel ID

## 3) Add IQ Videos
WP Admin → IQ Videos → Add New
- Source type: Mux or YouTube
- Mux Playback ID OR YouTube URL
- ASL avatar video URL (MP4/WebM aligned to timeline)
- Default ASL placement: below or side
- Store / Paid: set + WooCommerce Product ID

## 4) Optional: Store / purchases
Install WooCommerce:
- Create products for paid titles
- Paste product ID into IQ Video meta

## 5) Channels page template (no shortcode)
Create a new Page:
- Title: Channels
- Template: Channels
- Slug: channels

Then add it to your main menu.

You can manage the channel list in:
WP Admin → InclusiveQuest → YouTube Channels
