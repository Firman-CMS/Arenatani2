<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                        <?php if (!empty($category)): ?>
                            <li class="breadcrumb-item"><a href="<?php echo generate_category_url($category); ?>"><?php echo html_escape($category->name); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($subcategory)): ?>
                            <li class="breadcrumb-item"><a href="<?php echo generate_category_url($subcategory); ?>"><?php echo html_escape($subcategory->name); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($third_category)): ?>
                            <li class="breadcrumb-item"><a href="<?php echo generate_category_url($third_category); ?>"><?php echo html_escape($third_category->name); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo html_escape($product->title); ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="row row-product-details">
                    <div class="col-12 col-sm-12 col-md-7 col-lg-8">
                        <?php $this->load->library('user_agent');
if ($this->agent->is_mobile()):?>
                        <div class="product-content-left">
                            <?php else: ?>
    <div class="product-content-left" style="background-color: #fff;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 0px;">
   
 <?php endif; ?>
                            <div class="row">
                                <div class="col-12">
                                    <?php $this->load->view("product/details/_preview"); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="product-content-details product-content-details-mobile" style="display: none">
                                        <?php $this->load->view("product/details/_product_details_mobile"); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="product-description-container">
                                <?php $this->load->view("product/details/_description"); ?>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <?php if ($general_settings->product_reviews == 1 || $general_settings->product_comments == 1 || $general_settings->facebook_comment_status == 1): ?>
                                        <div class="product-reviews">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs">
                                                <?php if ($general_settings->product_reviews == 1): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-toggle="tab" href="#reviews"><?php echo trans("reviews"); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($general_settings->product_comments == 1): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link <?php echo ($general_settings->product_reviews != 1) ? 'active' : ''; ?>" data-toggle="tab" href="#comments">
                                                            <?php echo trans("comments"); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($general_settings->facebook_comment_status == 1): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link <?php echo ($general_settings->product_reviews != 1 && $general_settings->product_comments != 1) ? 'active' : ''; ?>" data-toggle="tab" href="#facebook_comments">
                                                            <?php echo trans("facebook_comments"); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <?php if ($general_settings->product_reviews == 1): ?>
                                                    <div class="tab-pane container active" id="reviews">
                                                        <!-- include reviews -->
                                                        <div id="review-result">
                                                            <?php $this->load->view('product/details/_make_review'); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($general_settings->product_comments == 1): ?>
                                                    <div class="tab-pane container <?php echo ($general_settings->product_reviews != 1) ? 'active' : 'fade'; ?>" id="comments">
                                                        <!-- include comments -->
                                                        <?php $this->load->view('product/details/_make_comment'); ?>
                                                        <div id="comment-result">
                                                            <?php $this->load->view('product/details/_comments'); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($general_settings->facebook_comment_status == 1): ?>
                                                    <div class="tab-pane container <?php echo ($general_settings->product_reviews != 1 && $general_settings->product_comments != 1) ? 'active' : 'fade'; ?>" id="facebook_comments">
                                                        <div class="fb-comments" data-href="<?php echo current_url(); ?>" data-width="100%" data-numposts="5"
                                                             data-colorscheme="light"></div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-5 col-lg-4">
                        <div class="product-content-right">
                            <div class="row">
                                <div class="col-12">
                                    <div class="product-content-details" style ="background-color: #fff;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 0px;">
                                        <?php $this->load->view("product/details/_product_details"); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <?php $this->load->view("product/details/_seller"); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <?php if (!empty($product->country_id)): ?>
                                        <div class="widget-location">
                                            <h4 class="sidebar-title"><?php echo trans("location"); ?></h4>
                                            <div class="sidebar-map">
                                                <!--load map-->
												<?php if ($product->image):?>
                                                <iframe src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=<?php echo html_escape($product->provinsi); ?>,<?php echo html_escape($product->kabupaten); ?>,<?php echo html_escape($product->kecamatan); ?>&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                            <?php else:?>
											<iframe src="https://maps.google.com/maps?width=100%&height=600&hl=en&q=<?php echo get_location($product); ?>&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
											<?php endif; ?>
											</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="row-custom">
                                        <!--Include banner-->
                                     <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- artikelister -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:250px"
     data-ad-client="ca-pub-4312695197724803"
     data-ad-slot="7905826063"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="related-products">
                    <h4 class="section-title"><?php echo trans("related_products"); ?></h4>
                    <div class="row row-product">
                        <!--print related posts-->
                        <?php foreach ($related_products as $item): ?>
                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-product">
                                <?php $this->load->view('product/_product_item', ['product' => $item]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Wrapper End-->
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
    <?php if ($this->auth_check): ?>
<?php if ($product->image):?>
        <a href="https://api.whatsapp.com/send?phone=<?php $anu = substr($product->hape,1); echo "62"; echo $anu; ?>&text= Hallo,saya tertarik dengan produk ini <?php echo generate_product_url($product); ?>" target="_blank" class="enabled-sticky-btnx sticky-btn whatsapp-btn" id="mobile-whatsapp_button">
    <?php else: ?> 
	<a href="https://api.whatsapp.com/send?phone=<?php $anu = substr($user->phone_number,1); echo "62"; echo $anu; ?>&text= Hallo,saya tertarik dengan produk ini <?php echo generate_product_url($product); ?>" target="_blank" class="enabled-sticky-btnx sticky-btn whatsapp-btn"  id="mobile-whatsapp_button">
    <?php endif; ?>

  <?php else: ?>
   <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal"  class="enabled-sticky-btnx sticky-btn whatsapp-btn" id="mobile-whatsapp_button">
 <?php endif; ?>
 <i class="fa fa-whatsapp"></i>
<span class="btn-text">Whatsapp</span>
</a>
<?php if ($this->auth_check): ?>
<a href="tel:<?php echo html_escape($user->phone_number); ?>" class="sticky-btn call-sms-btn js-show-call-sms-options" id="ad_mobile">
 <?php else: ?>
  <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="sticky-btn call-sms-btn js-show-call-sms-options" id="ad_mobile">
      <?php endif; ?>
<i class="fa fa-phone"></i>
<span class="btn-text">Telpon </span>
</a>
<a class="sticky-btn nego-btn open-mobile-tawar-modal enabled-sticky-btn" href="/products">
    <i class="fa fa-home"></i>
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
<!-- include send message modal -->
<?php $this->load->view("partials/_modal_send_message", ["subject" => $product->title]); ?>
<script>
    $(".fb-comments").attr("data-href", window.location.href);
</script>
<?php
if ($general_settings->facebook_comment_status == 1) {
    echo $general_settings->facebook_comment;
} ?>

<script src="<?php echo base_url(); ?>assets/vendor/touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script>
    $("#quantity_touchspin").TouchSpin({
        min: 1,
        max: <?php echo $product->quantity; ?>,
        verticalbuttons: true,
        verticalupclass: 'icon-arrow-up',
        verticaldownclass: 'icon-arrow-down'
    });
    $("#quantity_touchspin").change(function () {
        var count = $(this).val();
        $("#form_add_cart input[name='product_quantity']").val(count);
    });
    $("#quantity_touchspin_mobile").TouchSpin({
        min: 1,
        max: <?php echo $product->quantity; ?>,
        verticalbuttons: true,
        verticalupclass: 'icon-arrow-up',
        verticaldownclass: 'icon-arrow-down'
    });
    $("#quantity_touchspin_mobile").change(function () {
        var count = $(this).val();
        $("#form_add_cart_mobile input[name='product_quantity']").val(count);
    });
</script>

<script>
    $(function () {
        $('.product-description iframe').wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
        $('.product-description iframe').addClass('embed-responsive-item');
    });
</script>