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

                <h1 class="page-title">List transaksi promosi produk</h1>
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
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <!-- include message block -->
                        <?php $this->load->view('partials/_messages'); ?>
                        <div class="table-responsive">
                            <table class="table table-orders">
                                <thead>
                                <tr>
                                    <th scope="col"><?php echo trans("order"); ?></th>
									<th scope="col">Tipe promosi</th>
                                    <th scope="col"><?php echo trans("total"); ?></th>
                                    <th scope="col">Produk</th>
                                    <th scope="col"><?php echo trans("status"); ?></th>
                                    <th scope="col"><?php echo trans("date"); ?></th>
                                    <th scope="col"><?php echo trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($transaksi)): ?>
                                    <?php foreach ($transaksi as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order->id; ?></td>
											 <td>#<?php echo $order->purchased_plan; ?></td>
                                            <td><?php echo $order->payment_amount; ?></td>
                                            <td>
                                               <?php echo $order->product_title; ?>
                                            </td>
                                            <td>
                                                <strong>
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
  CURLOPT_POSTFIELDS => array('key' => '57231044-80B0-4739-8FF8-FFC3C71403AB','id' => $order->payment_id,'format' => 'json'),
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

                                                </strong>
                                            </td>
                                            <td><?php echo date("Y-m-d / h:i", strtotime($order->created_at)); ?></td>
                                            <td>
                                                <a href="<?php echo lang_base_url(); ?>transaksi/<?php echo $order->id; ?>" class="btn btn-sm btn-custom"><?php echo trans("details"); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>


                        <?php if (empty($order)): ?>
                            <p class="text-center">
                                <?php echo trans("no_records_found"); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row-custom m-t-15">
                    <div class="float-right">
                        <?php echo $this->pagination->create_links(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Wrapper End-->

