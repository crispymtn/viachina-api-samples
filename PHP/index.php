<?php

// --- For Sandbox Testing ---
$authToken = 'INSERT SANDBOX AUTH TOKEN HERE';
$url = 'https://app-sandbox.viaeurope.com/api/v1/orders';

// --- For Production System ---
// $authToken = 'INSERT PRODUCTION AUTH TOKEN HERE';
// $url = 'http://app.viaeurope.com/api/v1/orders';

// The order data to send to the API
$postData = array(
  'order' => array(
    'client_reference' => 'QA0200602190010',
    'disposition' => 'sale',
  'delivery' => array(
    'courier_service' => 'DDP31',
    'courier' => 'DPD', // You can use DPD, UPS or DHL
    'registered' => true,
    'name' => 'John Doe',
    'street' => 'Example Street 59',
    'zip_code' => '12345',
    'city' => 'Exampleton',
    'country_code' => 'DE',
    'phone' => '+000000000000' // UPS requires a phone number
  ),
  'line_items' => array(
    array(
      'description' => 'Car Airbed',
      'taric_code' => '9949909043',
      'price' => '2000', // In cents, so 2000 cents = 20.00 Euros
      'qty' => '1',
      'weight' => '200', // In grams, so 200 = 0.2 kilos
      'parcel_number' => 1
    )
  )
));

// Setup cURL
$request = curl_init($url);

curl_setopt_array($request, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Token token="'.$authToken.'"',
        'Content-Type: application/json',
        'Accept: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($postData)
));

// Send the request
$response = curl_exec($request);

// Check for errors
if ($response === FALSE) {
    die(curl_error($request));
}

// Check response status
$response_status = curl_getinfo($request, CURLINFO_HTTP_CODE);

if ($response_status < 200 || $response_status >= 300) {
  echo "Response HTTP Code ".$response_status."<br/>";
  echo "Response: <pre>".$response."</pre>";
  die();
}

// Decode the response JSON
$responseData = json_decode($response, TRUE);

// Close the connection
curl_close($request);

// Get the PDF label data from the response (it is base64 encoded)
$label_url = $responseData['labels'][0]['pdf_url'];

// Print raw PDF data for browser
echo "Label received: ".$label_url

?>
