<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="widget-seller" style="background-color: #fff;
    border: 1px solid #eee;
    padding FONT-WEIGHT: 100;
    padding: 10px;
    top: 30px;">
    <h4 class="sidebar-title">INFORMASI <?php echo trans("seller"); ?></h4>
<?php if ($product->image):?>
<div class="widget-content">
        <div class="left">
            <div class="user-avatar">
               
                    <img src="<?php echo html_escape($product->photo_profile); ?>" alt="<?php echo html_escape($product->penjual); ?>">
                
            </div>
        </div>
        <div class="right">
            <p>
                <a href="#">
                    <span class="user"><?php echo html_escape($product->penjual); ?></span>
                </a>
            </p>
            
             <p>
                    <span class="info"><i class="icon-envelope"></i><?php echo html_escape($product->email); ?></span>
                </p>

            
        </div>
    </div>
    
    <?php else: ?>
	<div class="widget-content">
        <div class="left">
            <div class="user-avatar">
                <a href="<?php echo lang_base_url() . 'profile/' . $product->user_slug; ?>">
                    <img src="<?php echo get_user_avatar($user); ?>" alt="<?php echo get_shop_name($user); ?>">
                </a>
            </div>
        </div>
        <div class="right">
            <p>
                <a href="<?php echo lang_base_url() . 'profile/' . $product->user_slug; ?>">
                    <span class="user"><?php echo get_shop_name($user); ?></span>
                </a>
            </p>
            <p>
                <span class="last-seen <?php echo (is_user_online($user->last_seen)) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?php echo trans("last_seen"); ?>&nbsp;<?php echo time_ago($user->last_seen); ?></span>
            </p>
            <?php if (!empty($user->phone_number) && $user->show_phone == 1): ?>
                <p>
                <span class="info"><i class="icon-phone"></i>
                    <a href="javascript:void(0)" id="show_phone_number"><?php echo trans("show"); ?></a>
                    <a href="tel:<?php echo html_escape($user->phone_number); ?>" id="phone_number" class="display-none"><?php echo html_escape($user->phone_number); ?></a>
                </span>
                </p>
            <?php elseif (!empty($user->email) && $user->show_email == 1): ?>
                <p>
                    <span class="info"><i class="icon-envelope"></i><?php echo html_escape($user->email); ?></span>
                </p>
            <?php endif; ?>

            <?php if (auth_check()): ?>
                <?php if (user()->id != $user->id): ?>
                    <!--form follow-->
                    <?php echo form_open('profile_controller/follow_unfollow_user', ['class' => 'form-inline']); ?>
                    <input type="hidden" name="following_id" value="<?php echo $user->id; ?>">
                    <input type="hidden" name="follower_id" value="<?php echo user()->id; ?>">
                    <?php if (is_user_follows($user->id, user()->id)): ?>
                        <p class="m-t-5">
                            <button class="btn btn-md btn-outline-gray"><i class="icon-user-minus"></i>&nbsp;<?php echo trans("unfollow"); ?></button>
                        </p>
                    <?php else: ?>
                        <p class="m-t-5">
                            <button class="btn btn-md btn-outline-gray"><i class="icon-user-plus"></i>&nbsp;<?php echo trans("follow"); ?></button>
                        </p>
                    <?php endif; ?>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            <?php else: ?>
                <p class="m-t-15">
                    <button class="btn btn-md btn-outline-gray" data-toggle="modal" data-target="#loginModal"><i class="icon-user-plus"></i>&nbsp;<?php echo trans("follow"); ?></button>
                </p>
            <?php endif; ?>
        </div>
    </div>
    
	  <?php endif; ?>
</div>
