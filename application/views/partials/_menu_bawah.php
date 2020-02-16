<?php $this->load->library('user_agent');
if ($this->agent->is_mobile()):?>
	 <?php if ($this->auth_check): ?>
  <div class="mb-footer-navbar signed-in" style="background-color: #FFFFFF;
    box-shadow: 0 -4px 20px 0 rgba(0,0,0,0.2);
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 400;
}">
<ul>
<li class="active">
<a href="<?php echo lang_base_url(); ?>">
<img src="https://image.flaticon.com/icons/svg/1828/1828684.svg" alt="Ic home">
<p class="icon-title">Home</p>
</a>
</li>
<li class="active">
<a href="<?php echo lang_base_url(); ?>favorites/<?php echo $this->auth_user->slug; ?>">
<img src="https://image.flaticon.com/icons/svg/400/400982.svg" alt="Ic favorites">
<p class="icon-title">Favorit</p>
</a>
</li>
<li class="active">
<a href="<?php echo lang_base_url(); ?>sell-now">
<div >
<img src="https://image.flaticon.com/icons/svg/702/702482.svg" alt="Ic favorites">
</div>
<p class="icon-title">Jual produk</p>
</a>
</li>
<li class="active">
<a href="<?php echo lang_base_url(); ?>messages" id="chat_dot_block">
<div class="counter-container chat-icon">
<img src="https://image.flaticon.com/icons/svg/491/491390.svg" alt="Ic chat off">

</div>
<p class="icon-title">Chat</p>
</a>
</li>
<li class="active">
<a href="<?php echo lang_base_url(); ?>profile/<?php echo $this->auth_user->slug; ?>" >
<img src="<?php echo get_user_avatar($this->auth_user); ?>" alt="Ic profile off">
<p class="icon-title">Profil</p>
</a>
</li>
</ul>
</div>
  <?php else: ?>
  <div class="mb-footer-navbar not-signed-in" style="background-color: #FFFFFF;
    box-shadow: 0 -4px 20px 0 rgba(0,0,0,0.2);
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 400;
}">
<ul>
<li class="active">
<a href="<?php echo lang_base_url(); ?>">
<img src="https://image.flaticon.com/icons/svg/1828/1828684.svg" alt="Ic home">
<p class="icon-title">Home</p>
</a>
</li>
<li class="pasang-iklan-container">
<a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" id="signinModalBtn">
<div class="pasang-iklan-btn" style="background-color:#00887a">
<span class="plus-icon"></span>
<span class="button-text">Jual produk</span>
</div>
</a>
</li>
<li class="active">
<a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" id="signinModalBtn">
<img src="https://image.flaticon.com/icons/svg/727/727399.svg" alt="Ic profile off">
<p class="icon-title">Masuk</p>
</a>
</li>
</ul>
</div>
   <?php endif; ?>
 <?php else: ?>
   
 <?php endif; ?>