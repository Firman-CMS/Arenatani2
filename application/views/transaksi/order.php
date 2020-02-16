<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $title; ?></li>
                    </ol>
                </nav>

                <h1 class="page-title">Detail transaksi</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-3">
                <div class="row-custom">
                    <!-- load profile nav -->
                    <?php $this->load->view("transaksi/_order_tabs"); ?>
                </div>
            </div>

            <div class="col-sm-12 col-md-9">
                <div class="row">
                    <div class="col-12">
                        <!-- include message block -->
                        <?php $this->load->view('product/_messages'); ?>
                    </div>
                </div>

                <div class="order-details-container">
                    <div class="order-head">
                        <h2 class="title"><?php echo trans("order"); ?>:&nbsp;#<?php echo $transaksi->id; ?></h2>
                    </div>
                    <div class="order-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <?php echo trans("status"); ?>
                                    </div>
                                    <div class="col-9">
                                      <?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://my.ipaymu.com/api/transaksi",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array('key' => '57231044-80B0-4739-8FF8-FFC3C71403AB','id' => $transaksi->payment_id,'format' => 'json'),
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  
}
$response = json_decode($response); 
  
echo $response->Keterangan; 
echo "\n"; 
  
?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        Nominal
                                    </div>
                                    <div class="col-9">
                                       <?php echo $transaksi->payment_amount; ?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <?php echo trans("payment_status"); ?>
                                    </div>
                                    <div class="col-9">
                                        <?php echo trans($transaksi->payment_status); ?>

                                        <?php if ($transaksi->payment_method == "Bank Transfer" && $transaksi->payment_status == "awaiting_payment"):

                                            if (isset($last_bank_transfer)):?>
                                                <?php if ($last_bank_transfer->status == "pending"): ?>
                                                    <span class="text-info">(<?php echo trans("pending"); ?>)</span>
                                                <?php elseif ($last_bank_transfer->status == "declined"): ?>
                                                    <span class="text-danger">(<?php echo trans("bank_transfer_declined"); ?>)</span>
                                                    <button type="button" class="btn btn-sm btn-secondary color-white m-l-15" data-toggle="modal" data-target="#reportPaymentModal"><?php echo trans("report_bank_transfer"); ?></button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary color-white m-l-15" data-toggle="modal" data-target="#reportPaymentModal"><?php echo trans("report_bank_transfer"); ?></button>
                                            <?php endif; ?>


                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <?php echo trans("payment_method"); ?>
                                    </div>
                                    <div class="col-9">
                                        <?php
                                        if ($transaksi->payment_method == "Bank Transfer") {
                                            echo trans("bank_transfer");
                                        } else {
                                            echo $transaksi->payment_method;
                                        } ?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        <?php echo trans("date"); ?>
                                    </div>
                                    <div class="col-9">
                                        <?php echo date("Y-m-d / h:i", strtotime($transaksi->created_at)); ?>
                                    </div>
                                </div>
                                <div class="row order-row-item">
                                    <div class="col-3">
                                        Catatan penting:
                                    </div>
                                    <div class="col-9">
                                        Silahakan melakukan pembayaran sesuai dengan petunjuk yang telah kami berikan melalui email dan sma.Ketika anda sudah melakukan pembayaran dan sukses maka status pembayaran akan secara otomatis
                                        berubah menjadi sukses dan produk anda otomotis akan di promosikan.Terima kasih
                                    </div>
                                </div>
                            </div>
                        </div>
                 
                    </div>
                </div>
                <?php if (!empty($shipping)): ?>
                    <p class="text-confirm-order">*<?php echo trans("warning_buyer_approve_order"); ?></p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<!-- Wrapper End-->

<!-- Modal -->
<div class="modal fade" id="reportPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-custom">
            <!-- form start -->
            <?php echo form_open_multipart('order_controller/bank_transfer_payment_report_post'); ?>
            <div class="modal-header">
                <h5 class="modal-title"><?php echo trans("report_bank_transfer"); ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"><i class="icon-close"></i> </span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="order_number" class="form-control form-input" value="<?php echo $transaksi->order_number; ?>">
                <div class="form-group">
                    <label><?php echo trans("payment_note"); ?></label>
                    <textarea name="payment_note" class="form-control form-textarea" maxlength="499"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo trans("receipt"); ?>
                        <small>(.png, .jpg, .jpeg)</small>
                    </label>
                    <p>
                        <a class='btn btn-md btn-secondary btn-file-upload'>
                            <?php echo trans('select_image'); ?>
                            <input type="file" name="file" size="40" accept=".png, .jpg, .jpeg" onchange="$('#upload-file-info').html($(this).val());">
                        </a><br>
                        <span class='badge badge-info' id="upload-file-info"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-red" data-dismiss="modal"><?php echo trans("close"); ?></button>
                <button type="submit" class="btn btn-md btn-custom"><?php echo trans("submit"); ?></button>
            </div>
            <?php echo form_close(); ?><!-- form end -->
        </div>
    </div>
</div>

<?php if (!empty($this->session->userdata('mds_send_email_order_summary'))): ?>
    <script>
        $(document).ready(function () {
            var data = {
                "order_id": '<?php echo $transaksi->id; ?>',
                'lang_folder': lang_folder,
                'form_lang_base_url': '<?php echo lang_base_url(); ?>'
            };
            data[csfr_token_name] = $.cookie(csfr_cookie_name);
            $.ajax({
                type: "POST",
                url: base_url + "ajax_controller/send_email_order_summary",
                data: data,
                success: function (response) {
                }
            });
        });
    </script>
    <?php
    $this->session->unset_userdata('mds_send_email_order_summary');
endif; ?>
