<?php if (!defined('ABSPATH')) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class('iq-body'); ?>>
<?php wp_body_open(); ?>

<header class="iq-header">
  <div class="iq-header__inner">
    <a class="iq-brand" href="<?php echo esc_url(home_url('/')); ?>">
      <span class="iq-brand__mark">Q</span>
      <span class="iq-brand__name">inclusivequest</span>
    </a>

    <nav class="iq-nav">
      <?php
        wp_nav_menu([
          'theme_location' => 'primary',
          'container' => false,
          'fallback_cb' => '__return_false',
          'items_wrap' => '<ul class="iq-nav__list">%3$s</ul>',
        ]);
      ?>
    </nav>

    <div class="iq-header__actions">
      <form class="iq-search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" name="s" placeholder="Search titles, channels..." value="<?php echo esc_attr(get_search_query()); ?>">
      </form>
      <?php if (is_user_logged_in()): ?>
        <a class="iq-btn iq-btn--ghost" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Log out</a>
      <?php else: ?>
        <a class="iq-btn" href="<?php echo esc_url(wp_login_url()); ?>">Sign in</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="iq-main">
