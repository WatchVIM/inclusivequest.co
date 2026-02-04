<?php
if (!defined('ABSPATH')) { exit; }

function iq_register_post_types() {
  register_post_type('iq_video', [
    'labels' => [
      'name' => 'IQ Videos',
      'singular_name' => 'IQ Video',
      'add_new_item' => 'Add New IQ Video',
      'edit_item' => 'Edit IQ Video',
      'new_item' => 'New IQ Video',
      'view_item' => 'View IQ Video',
      'search_items' => 'Search IQ Videos',
    ],
    'public' => true,
    'has_archive' => true,
    'menu_icon' => 'dashicons-video-alt3',
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    'rewrite' => ['slug' => 'watch'],
    'show_in_rest' => true,
  ]);

  register_taxonomy('iq_category', ['iq_video'], [
    'labels' => [
      'name' => 'Categories',
      'singular_name' => 'Category',
    ],
    'public' => true,
    'hierarchical' => true,
    'show_in_rest' => true,
  ]);
}
add_action('init', 'iq_register_post_types');
