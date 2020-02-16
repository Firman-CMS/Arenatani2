<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script>
    var total_amount = '<?php echo price_format_decimal($promoted_plan->total_amount); ?>';
    var currency = '<?php echo $payment_settings->promoted_products_payment_currency; ?>';
    var paypal_mode = '<?php echo $payment_settings->paypal_mode; ?>';
    var paypal_client_id = '<?php echo $payment_settings->paypal_client_id; ?>';
    var stripe_key = '<?php echo $payment_settings->stripe_publishable_key; ?>';
    $(window).bind("load", function () {
        $("#payment-button-container").css("visibility", "visible");
    });
</script>

<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shopping-cart shopping-cart-shipping">
                    <div class="row">
                        <div class="col-sm-12 col-lg-7">
                            <div class="left">
                                <h1 class="cart-section-title">Promosi produk</h1>
                                <?php if (!auth_check()): ?>
                                    <div class="row m-b-15">
                                        <div class="col-12 col-md-6">
                                            <p><?php echo trans("checking_out_as_guest"); ?></p>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <p class="text-right"><?php echo trans("have_account"); ?>&nbsp;<a href="javascript:void(0)" class="link-underlined" data-toggle="modal" data-target="#loginModal"><?php echo trans("login"); ?></a></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                               
                                <div class="tab-checkout tab-checkout-open">
                                    <h2 class="title">&nbsp;&nbsp;<?php echo trans("payment"); ?></h2>

                                    <div class="row">
                                        <div class="col-12">
                                          
    <?php echo form_open('promote_controller/bank_transfer_payment_post'); ?>
    <input type="hidden" name="payment_id" value="<?php
$this->load->helper('url');
$currentURL = current_url();
$params   = $_SERVER['QUERY_STRING'];
$fullURL = $currentURL . '?' . $params;
$parts = parse_url($fullURL);
$output = [];
parse_str($parts['query'], $output);
echo $output['trx_id']; //

?>">
    <div id="payment-button-container" class=paypal-button-cnt">
        <div class="bank-account-container">
            <?php echo $payment_settings->bank_transfer_accounts; ?>
        </div>

        <p class="p-transaction-number"><span><?php echo trans("transaction_number"); ?>: <?php
$this->load->helper('url');
$currentURL = current_url();
$params   = $_SERVER['QUERY_STRING'];
$fullURL = $currentURL . '?' . $params;
$parts = parse_url($fullURL);
$output = [];
parse_str($parts['query'], $output);
echo $output['trx_id']; //

?></span></p>

        <p>Setelah Anda melakukan pemesanan, Anda dapat melakukan pembayaran ke rekening bank dibawah ini,atau bisa juga mengikuti petunjuk pembayaran yang telah dikirim melalui email dan sms.</p>
        <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom float-right">Selesai</button>
    </div>

    <?php echo form_close(); ?>

<b>Rincian transaksi anda:</b>
<br>
status:<?php
$this->load->helper('url');
$currentURL = current_url();
$params   = $_SERVER['QUERY_STRING'];
$fullURL = $currentURL . '?' . $params;
$parts = parse_url($fullURL);
$output = [];
parse_str($parts['query'], $output);
echo $output['status']; //

?>
<br>
Bank Tujuan:<?php
$this->load->helper('url');
$currentURL = current_url();
$params   = $_SERVER['QUERY_STRING'];
$fullURL = $currentURL . '?' . $params;
$parts = parse_url($fullURL);
$output = [];
parse_str($parts['query'], $output);
echo $output['channel']; //

?>
<br>
Nomor rekening:<?php
$this->load->helper('url');
$currentURL = current_url();
$params   = $_SERVER['QUERY_STRING'];
$fullURL = $currentURL . '?' . $params;
$parts = parse_url($fullURL);
$output = [];
parse_str($parts['query'], $output);
echo $output['va']; //

?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-5">
                            <h2 class="cart-section-title"><?php echo trans("order_summary"); ?> (1)</h2>
                            <div class="right">
                                <?php $this->load->view("promote/_order_summary"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Wrapper End-->


