<?php
if (!defined('ABSPATH')) { exit; }

function iq_register_settings() {
  register_setting('iq_settings', 'iq_youtube_api_key', ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field']);
  register_setting('iq_settings', 'iq_default_youtube_channel', ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field']);
  register_setting('iq_settings', 'iq_youtube_channels', ['type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field']);
}
add_action('admin_init', 'iq_register_settings');

function iq_add_settings_page() {
  add_menu_page(
    'InclusiveQuest',
    'InclusiveQuest',
    'manage_options',
    'iq-settings',
    'iq_render_settings_page',
    'dashicons-universal-access-alt',
    58
  );
}
add_action('admin_menu', 'iq_add_settings_page');

function iq_render_settings_page() {
  if (!current_user_can('manage_options')) return;
  ?>
  <div class="wrap">
    <h1>InclusiveQuest Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('iq_settings'); ?>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="iq_youtube_api_key">YouTube API Key</label></th>
          <td>
            <input name="iq_youtube_api_key" id="iq_youtube_api_key" type="text" class="regular-text"
                   value="<?php echo esc_attr(get_option('iq_youtube_api_key', '')); ?>">
            <p class="description">Used to pull channel feeds via YouTube Data API v3.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="iq_default_youtube_channel">Default YouTube Channel ID</label></th>
          <td>
            <input name="iq_default_youtube_channel" id="iq_default_youtube_channel" type="text" class="regular-text"
                   value="<?php echo esc_attr(get_option('iq_default_youtube_channel', '')); ?>">
            <p class="description">Used when shortcode channel="" is not provided.</p>
          </td>
          </tr>
  <tr>
    <th scope="row"><label for="iq_youtube_channels">YouTube Channels (multiple)</label></th>
    <td>
      <textarea name="iq_youtube_channels" id="iq_youtube_channels" class="large-text" rows="5"
        placeholder="UCxxxx\nUCyyyy\nUCzzzz"><?php echo esc_textarea(get_option('iq_youtube_channels', '')); ?></textarea>
      <p class="description">Enter one Channel ID per line (or comma-separated). Used by the Channels page template.</p>
    </td>
  </tr>
</table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}
