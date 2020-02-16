<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="product-item" style="position: relative;
    float: left;
    margin: 0 26px 26px 0;
    border-radius: 5px;
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
    box-shadow: 0 1px 1px rgba(0,0,0,0.15);
    background-color: #fff;
   ">
    <div class="row-custom">
	<?php if ($product->image):?>
        <a class="item-favorite-button item-favorite-enable <?php echo (is_product_in_favorites($product->id) == true) ? 'item-favorited' : ''; ?>" data-product-id="<?php echo $product->id; ?>"></a>
        <a href="<?php echo generate_product_url($product); ?>">
            <div class="img-product-container">
                <img src="<?php echo html_escape($product->image); ?>" data-src="<?php echo html_escape($product->image); ?>" alt="<?php echo html_escape($product->title); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?php echo $img_bg_product_small; ?>'">
            </div>
        </a>
		 <?php else: ?>
   <a class="item-favorite-button item-favorite-enable <?php echo (is_product_in_favorites($product->id) == true) ? 'item-favorited' : ''; ?>" data-product-id="<?php echo $product->id; ?>"></a>
        <a href="<?php echo generate_product_url($product); ?>">
            <div class="img-product-container">
                <img src="<?php echo $img_bg_product_small; ?>" data-src="<?php echo get_product_image($product->id, 'image_small'); ?>" alt="<?php echo html_escape($product->title); ?>" class="lazyload img-fluid img-product" onerror="this.src='<?php echo $img_bg_product_small; ?>'">
            </div>
        </a>
 <?php endif; ?>
        <?php if ($product->is_promoted && $promoted_products_enabled == 1 && isset($promoted_badge) && $promoted_badge == true): ?>
            <span class="badge badge-dark badge-promoted"><?php echo trans("promoted"); ?></span>
        <?php endif; ?>
    </div>
    <div class="row-custom item-details" style="padding: 10px 10px;">
        <h3 class="product-title">
            <a href="<?php echo generate_product_url($product); ?>"><?php echo html_escape($product->title); ?></a>
        </h3>
        <p class="product-user text-truncate">
           <?php if ($product->image):?>
             <i class="icon-user"> </i> <?php echo html_escape($product->penjual); ?>
			 <?php else: ?>
			  <a href="<?php echo lang_base_url() . "profile" . '/' . html_escape($product->user_slug); ?>">
               <i class="icon-user"> </i>  <?php echo get_shop_name_product($product); ?>
            </a>
             <?php endif; ?>
        </p>
        <!--stars-->
        <?php if ($general_settings->product_reviews == 1) {
            $this->load->view('partials/_review_stars', ['review' => $product->rating]);
        } ?>
        <div class="item-meta">
		<?php if ($product->image):?>
		 <span class="price"><?php echo print_price($product->price, $product->currency); ?></span>
		 <?php else: ?>
            <span class="price"><?php echo print_price($product->price, $product->currency); ?></span>
			<?php endif; ?>
          
        </div>
    </div>
</div>

