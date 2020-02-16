<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if ($cart_payment_method->payment_option == "bank_transfer"): ?>
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

        <p class="p-complete-payment">Setelah Anda melakukan pemesanan, Anda dapat melakukan pembayaran ke rekening bank dibawah ini.</p>
        <button type="submit" name="submit" value="update" class="btn btn-lg btn-custom float-right">Selesai</button>
    </div>
    <?php echo form_close(); ?>
<?php endif; ?>
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