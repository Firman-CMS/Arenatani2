<?php

// URL Payment IPAYMU
$url = 'https://my.ipaymu.com/payment.htm';

// Prepare Parameters
$params = array(
            'key'      => '57231044-80B0-4739-8FF8-FFC3C71403AB', // API Key Merchant / Penjual
            'action'   => 'payment',
            'product'  => $promoted_plan->purchased_plan,
            'price'    => price_format_decimal($promoted_plan->total_amount), // Total Harga
            'quantity' => 1,
            'comments' => 'Pembelian paket promosi produk', // Optional           
            'ureturn'  => 'https://arenatani.com/promote-product/payment?',
            'unotify'  => 'https://arenatani.com',
            'ucancel'  => 'https://arenatani.com',

            /* Parameter untuk pembayaran lain menggunakan PayPal 
             * ----------------------------------------------- */
                'buyer_name'  => $this->auth_user->slug, // Nama customer/pembeli(opsional) 
                'buyer_phone' => $this->auth_user->phone_number, // No HP customer/pembeli (opsional)
                'buyer_email' => $this->auth_user->email, // Email customer/pembeli (opsional)
          
            /* ----------------------------------------------- */
            
            'format'   => 'json' // Format: xml / json. Default: xml 
        );

$params_string = http_build_query($params);

//open connection
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($params));
curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

//execute post
$request = curl_exec($ch);

if ( $request === false ) {
    echo 'Curl Error: ' . curl_error($ch);
} else {
    
    $result = json_decode($request, true);

    if( isset($result['url']) )
        header('location: '. $result['url']);
    else {
        echo "Request Error ". $result['Status'] .": ". $result['Keterangan'];
    }
}

//close connection
curl_close($ch);

?>