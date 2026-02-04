<?php
/**
 * Plugin Name: InclusiveQuest Core
 * Description: Core features for InclusiveQuest.co: IQ Videos CPT, meta fields, YouTube feed shortcode, and WooCommerce gating helpers.
 * Version: 0.1.0
 * Author: VIM Media / InclusiveQuest
 */

if (!defined('ABSPATH')) { exit; }

define('IQ_CORE_VERSION', '0.1.0');
define('IQ_CORE_PATH', plugin_dir_path(__FILE__));
define('IQ_CORE_URL', plugin_dir_url(__FILE__));

require_once IQ_CORE_PATH . 'includes/post-types.php';
require_once IQ_CORE_PATH . 'includes/meta-boxes.php';
require_once IQ_CORE_PATH . 'includes/settings.php';
require_once IQ_CORE_PATH . 'includes/youtube-api.php';
require_once IQ_CORE_PATH . 'includes/youtube-feed.php';
require_once IQ_CORE_PATH . 'includes/youtube-importer.php';
