<?php
/**
 * Product content card (Hulu-like)
 */
defined('ABSPATH') || exit;

global $product;
if (empty($product) || !$product->is_visible()) { return; }
?>
<li <?php wc_product_class('iq-storeCard', $product); ?>>
  <a href="<?php the_permalink(); ?>" class="iq-storeLink">
    <div class="iq-storeThumb">
      <?php
        if (has_post_thumbnail()) {
          the_post_thumbnail('medium_large', ['class' => 'iq-storeImg']);
        } else {
          echo '<div class="iq-thumb--ph"></div>';
        }
      ?>
      <span class="iq-badge iq-badge--store">Store</span>
    </div>
    <div class="iq-storeMeta">
      <div class="iq-storeTitle"><?php the_title(); ?></div>
      <div class="iq-storePrice"><?php echo $product->get_price_html(); ?></div>
    </div>
  </a>
</li>
