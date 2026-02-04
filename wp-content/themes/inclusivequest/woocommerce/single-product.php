<?php
/**
 * Single product template with InclusiveQuest styling
 */
defined('ABSPATH') || exit;

get_header('shop');

while (have_posts()) : the_post(); ?>
  <section class="iq-product">
    <div class="iq-productGrid">
      <div class="iq-productMedia">
        <div class="iq-productMediaInner">
          <?php
            do_action('woocommerce_before_single_product_summary');
          ?>
        </div>
      </div>

      <div class="iq-productInfo">
        <?php
          do_action('woocommerce_single_product_summary');
        ?>
        <div class="iq-productHint iq-muted">
          After purchase, link this product to your IQ Video (WP Admin → IQ Videos → Product ID).
        </div>
      </div>
    </div>

    <div class="iq-productTabs">
      <?php do_action('woocommerce_after_single_product_summary'); ?>
    </div>
  </section>
<?php endwhile;

get_footer('shop');
