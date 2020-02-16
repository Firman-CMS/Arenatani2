<?php $this->load->library('user_agent');
if ($this->agent->is_mobile()):?>

<div class="full-width fixed-footer">
<div class="container milan-container">
<div class="col-12 sticky-product-info">
<div class="row">
<div class="col-8">
<h1 class="product-name"><?php echo html_escape($product->title); ?></h1>

<div class="product-price"><span class="product-price-value"><?php if ($product->is_sold == 1): ?>
            <strong class="lbl-price" style="color: #9a9a9a;"><?php echo print_price($product->price, $product->currency); ?><span class="price-line"></span></strong>
            <strong class="lbl-sold"><?php echo trans("sold"); ?></strong>
        <?php else: ?>
            <strong class="lbl-price"><?php echo print_price($product->price, $product->currency); ?></strong>
        <?php endif; ?></span></div>
<div class="sticky-product-location">

</div>
</div>
<div class="col-4 sticky-round-btn-wrapper">
<a class="sticky-round-btn favourite-btn">
<i class="fa fa-heart-o detail-page-fav-icon" data-ad-id="1781071041" onclick="add_to_wishlist(this)"></i>
</a>
<a class="sticky-round-btn share-btn js-product-share-btn">
<i class="fa fa-share-alt"></i>
</a>
</div>
</div>
</div>
<div class="col-12">
<div class="product-detail__foot">
<div class="row product-detail__foot-button">
<?php if ($product->image):?>
        <a href="https://api.whatsapp.com/send?phone=<?php $anu = substr($product->hape,1); echo "62"; echo $anu; ?>&text= Hallo,saya tertarik dengan produk ini <?php echo generate_product_url($product); ?>" target="_blank" class="btn btn-favorite"><i class="icon-whatsapp"></i>Hubungi penjual via Whatsapp</a>
    <?php else: ?> 
	<a href="https://api.whatsapp.com/send?phone=<?php $anu = substr($user->phone_number,1); echo "62"; echo $anu; ?>&text= Hallo,saya tertarik dengan produk ini <?php echo generate_product_url($product); ?>" target="_blank" class="btn btn-favorite"><i class="icon-whatsapp"></i>Hubungi penjual via Whatsapp</a>
    <?php endif; ?>
<i class="fa fa-whatsapp"></i>
<span class="btn-text">Whatsapp</span>
</a>
<?php if ($product->image):?>
        <a href="<?php echo html_escape($product->hape); ?>" target="_blank" class="btn btn-favorite"><i class="icon-phone"></i>Hubungi penjual : <?php echo html_escape($product->hape); ?></a>
     <?php else: ?> 
	 <a href="<?php echo html_escape($user->phone_number); ?>" target="_blank" class="btn btn-favorite"><i class="icon-phone"></i>Hubungi penjual :<?php echo html_escape($user->phone_number); ?></a>
	  <?php endif; ?>
<i class="fa fa-phone"></i>
<span class="btn-text">Telpon </span>
</a>
<a class="sticky-btn nego-btn open-mobile-tawar-modal enabled-sticky-btn" href="#">
<span class="btn-text">Kembali</span>
</a>

</a>
</div>
</div>
</div>
</div>
</div>
 
 <?php else: ?>
   
 <?php endif; ?>