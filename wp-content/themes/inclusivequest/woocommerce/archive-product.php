<?php
/**
 * WooCommerce Archive Product (Shop)
 */
defined('ABSPATH') || exit;
get_header('shop');
?>

<section class="iq-pageHead">
  <h1>Store</h1>
  <p class="iq-muted">Purchase or rent select titles. Paid titles unlock playback inside InclusiveQuest.</p>
</section>

<?php if (woocommerce_product_loop()): ?>

  <?php woocommerce_output_all_notices(); ?>

  <div class="iq-storeBar">
    <div class="iq-storeFilters">
      <?php woocommerce_catalog_ordering(); ?>
    </div>
    <div class="iq-storeCount iq-muted">
      <?php woocommerce_result_count(); ?>
    </div>
  </div>

  <?php woocommerce_product_loop_start(); ?>

    <?php if (wc_get_loop_prop('total')): ?>
      <?php while (have_posts()): the_post(); ?>
        <?php wc_get_template_part('content', 'product'); ?>
      <?php endwhile; ?>
    <?php endif; ?>

  <?php woocommerce_product_loop_end(); ?>

  <?php do_action('woocommerce_after_shop_loop'); ?>

<?php else: ?>
  <?php do_action('woocommerce_no_products_found'); ?>
<?php endif; ?>

<?php
get_footer('shop');
