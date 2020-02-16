<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/menu.css"/>
<!-- Wrapper -->
<div id="wrapper" class="index-wrapper">
    <div class="container container-slider" >
       <?php $this->load->library('user_agent');
if ($this->agent->is_mobile()):?>
       <div class="alert alert-green-all " style="display: block;background-color: #fff">
                        <div class="row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-8 text-center text-success" style="color: #00887a!important;">
                                <div class="block-sm"></div>
                                 <img style="height:64px;width:64px;float:left" alt="Pasang iklan gratis" src="<?php echo base_url(); ?>assets/img/iklan-gratis.png" class="img-responsive"><b>Ayo,segera jual produk anda di arenatani,GRATIS!</b>
                                <br>
                                <a href="https://arenatani.com/sell-now" class="btn btn-md btn-custom btn-sell-now"><i class="fa fa-plus"></i>Jual produk sekarang</a>
                            </div>
                        </div>
                    </div>
        <?php else: ?> <div class="alert alert-green-all display-none" style="display: block;background-color: #fff">
                        <div class="row">
                            <div class="col-sm-4">
                                <img style="height:200px;width:200px" alt="Pasang iklan gratis" src="<?php echo base_url(); ?>assets/img/iklan-gratis.png" class="img-responsive">
                            </div>
                            <div class="col-sm-8 text-center text-success" style="color: #00887a!important;">
                                <div class="block-sm"></div>
                                <h4><strong>Ayo segera pasang iklan produk anda di arenatani, GRATIS!</strong></h4>
                                <p>Jangkau lebih luas pemasaran produk agribisinis dengan memanfatkan teknologi pasar online dan dapatkan lebih banyak calon pembeli dengan Pasang iklan di arenatani gratis.</p><br>
                                <a href="https://arenatani.com/sell-now" class="btn btn-md btn-custom btn-sell-now"><i class="fa fa-plus"></i>Jual produk</a>
                            </div>
                        </div>
                    </div>
         <?php endif; ?>
    </div>
    <?php $this->load->library('user_agent');
if ($this->agent->is_mobile()):?><center>
<ul class="main-content__category-list">
<li class="main-content__category-item">
<a class="mb-home-cat"  href="category/pertanian-3">
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/wheat.png">
<span class="main-content__category-title">
Pertanian 
</span>
</a></li>
<li class="main-content__category-item">
<a class="mb-home-cat"  href="category/perkebunan-4"><span class="icon icon_m_populars_mobil icons_kategori"></span>
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/sugar-cane.png">
<span class="main-content__category-title">
Perkebunan 
</span>
</a></li>
<li class="main-content__category-item">
<a class="mb-home-cat"  href="category/perikanan"><span class="icon icon_m_populars_properti icons_kategori"></span>
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/china.png">
<span class="main-content__category-title">
Perikanan 
</span>
</a></li>
<li class="main-content__category-item">
<a class="mb-home-cat" href="category/peternakan"><span class="icon icon_m_populars_fashion icons_kategori"></span>
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/cow.png">
<span class="main-content__category-title">
Peternakan 
</span>
</a></li>
<li class="main-content__category-item">
<a class="mb-home-cat"  href="category/ukm"><span class="icon icon_m_populars_handphone-dan-gadget icons_kategori"></span>
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/market.png">
<span class="main-content__category-title">
UKM
</span>
</a></li>
<li class="main-content__category-item">
<a class="mb-home-cat"  href="category/jasa"><span class="icon icon_m_populars_handphone-dan-gadget icons_kategori"></span>
<img style="width:50px;height:50px;" src="https://arenatani.com/assets/img/tractor.png">
<span class="main-content__category-title">
Jasa
</span>
</a></li>
</ul>
</center>
<?php else: ?>
<center>
   <a class="mb-home-cat"  href="category/pertanian-3">
<img style="width:160px;height:110px;" src="https://arenatani.com/assets/img/pertanianfix.jpg">

</a>

<a class="mb-home-cat"  href="category/perkebunan-4"><span class="icon icon_m_populars_mobil icons_kategori"></span>
<img  style="width:160px;height:110px;" src="https://arenatani.com/assets/img/perkebunanfix.jpg">

</a>

<a class="mb-home-cat"  href="category/perikanan"><span class="icon icon_m_populars_properti icons_kategori"></span>
<img  style="width:160px;height:110px;" src="https://arenatani.com/assets/img/perikananfix.jpg">

</a>

<a class="mb-home-cat" href="category/peternakan"><span class="icon icon_m_populars_fashion icons_kategori"></span>
<img  style="width:160px;height:110px;" src="https://arenatani.com/assets/img/peternakanfix.jpg">

</a>

<a class="mb-home-cat"  href="category/ukm"><span class="icon icon_m_populars_handphone-dan-gadget icons_kategori"></span>
<img  style="width:160px;height:110px;" src="https://arenatani.com/assets/img/ukmfix.jpg">

</a>

<a class="mb-home-cat"  href="category/jasa"><span class="icon icon_m_populars_kamera icons_kategori"></span>
<img  style="width:160px;height:110px;" src="https://arenatani.com/assets/img/jasafix.jpg">

</a>
</center>
 <?php endif; ?>
    <div class="container">
        
        <div class="row">
            
            <h1 class="index-title"><?php echo html_escape($settings->site_title); ?></h1>
            <?php if ($featured_category_count > 0 && $general_settings->index_categories == 1): ?>
                <div class="col-12 section section-categories">
                    <!-- featured categories -->
                    <?php $this->load->view("partials/_featured_categories"); ?>
                    
                </div>
            <?php endif; ?>
            <div class="col-12">
                <div class="row-custom row-bn">
                    <!--Include banner-->
                    <?php $this->load->view("partials/_ad_spaces", ["ad_space" => "index_1", "class" => ""]); ?>
                </div>
            </div>
            <?php if ($general_settings->index_promoted_products == 1 && $promoted_products_enabled == 1 && !empty($promoted_products)): ?>
                <div class="col-12 section section-promoted">
                    <!-- promoted products -->
                    <?php $this->load->view("product/_promoted_products"); ?>
                </div>
            <?php endif; ?>
            <?php if ($general_settings->index_latest_products == 1 && !empty($latest_products)): ?>
                <div class="col-12 section section-latest-products">
                   <img style="width:32px;height:32px;" src="https://arenatani.com/assets/img/new.png"> <b><?php echo trans("latest_products"); ?></b>
                    <p><?php echo trans("latest_products_exp"); ?>.</p>
                    <div class="row row-product">
                        <!--print products-->
                        <?php foreach ($latest_products as $product): ?>
                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-product">
                                <?php $this->load->view('product/_product_item', ['product' => $product, 'promoted_badge' => false]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row-custom text-center">
                        <a href="<?php echo lang_base_url() . "products"; ?>"  class="btn btn-md btn-custom btn-sell-now"><span><?php echo trans("see_more"); ?>&nbsp;</span><i class="icon-arrow-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-12">
                <div class="row-custom row-bn">
                  
                    <!--Include banner-->
                    <?php $this->load->view("partials/_ad_spaces", ["ad_space" => "index_2", "class" => ""]); ?>
                </div>
            </div>
            <?php if ($general_settings->index_blog_slider == 1 && !empty($blog_slider_posts)): ?>
                <div class="col-12 section section-blog m-0">
                    <h3 class="title"><?php echo trans("latest_blog_posts"); ?></h3>
                    <p class="title-exp"><?php echo trans("latest_blog_posts_exp"); ?></p>
                    <div class="row-custom">
                        <!-- main slider -->
                        <?php $this->load->view("blog/_blog_slider", ['blog_slider_posts' => $blog_slider_posts]); ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
<!-- Wrapper End-->



