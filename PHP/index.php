<?php

// --- For Sandbox Testing ---
$authToken = 'INSERT SANDBOX AUTH TOKEN HERE';
$base_url = 'https://app-sandbox.viaeurope.com/api/v1';

// --- For Production System ---
// $authToken = 'INSERT PRODUCTION AUTH TOKEN HERE';
// $base_url = 'http://app.viaeurope.com/api/v1/orders';

// Your reference for the order
$client_reference = 'QA0200602190019';

//
// THIS IS THE CODE FOR STEP 1: Create order and receive label
//

$step_1_url = $base_url . "/orders";

// The order data to send to the API
$postData = array(
  'order' => array(
    'client_reference' => $client_reference,
    'disposition' => 'sale',
  'delivery' => array(
    'courier_service' => 'DDP31_MULTI',
    'courier' => 'UPS', // You can use DPD, UPS or DHL
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
    ),
    array(
      'description' => 'Car Airbed',
      'taric_code' => '9949909043',
      'price' => '2000', // In cents, so 2000 cents = 20.00 Euros
      'qty' => '1',
      'weight' => '200', // In grams, so 200 = 0.2 kilos
      'parcel_number' => 2
    )
  )
));

// Setup cURL
$request = curl_init($step_1_url);

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
foreach($responseData['labels'] as $label) {
  $label_url = $label['pdf_url'];
  $parcel_number = $label['parcel_number'];

  // Print raw PDF data for browser
  echo "Label received for parcel number ".$parcel_number.": ".$label_url."\n";
}

//
// END OF CODE FOR STEP 1
//

//
// THIS IS THE CODE FOR STEP 2: Set HAWB and MAWB
//

echo "\n\nSTEP 2\n\n";

$step_2_url = $step_1_url . "/" . $client_reference;

$putData = array(
  'order' => array(
    'hawb' => 'TEST_12345_UPS',
    'mawb' => '160-12345675'
  )
);

// Setup cURL
$request = curl_init($step_2_url);

curl_setopt_array($request, array(
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Token token="'.$authToken.'"',
        'Content-Type: application/json',
        'Accept: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($putData)
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
  echo "\nSTEP 2 FAILED\n";
  echo "Response HTTP Code ".$response_status."<br/>";
  echo "Response: <pre>".$response."</pre>";
}

echo "STEP 2 COMPLETED";

// Close the connection
curl_close($request);

//
// END OF CODE FOR STEP 2
//

//
// THIS IS THE CODE FOR STEP 3: Set information on HAWB
//

echo "\n\nStarting STEP 3\n\n";

$step_3_url = $base_url."/hawbs/TEST_12345_UPS";

$putData = array(
  'hawb' => array(
    'number_of_units' => 29,
    'volumetric_weight' => 1435
  )
);

// Setup cURL
$request = curl_init($step_3_url);

curl_setopt_array($request, array(
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Token token="'.$authToken.'"',
        'Content-Type: application/json',
        'Accept: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($putData)
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
  echo "\nSTEP 3 FAILED\n";
  echo "Response HTTP Code ".$response_status."<br/>";
  echo "Response: <pre>".$response."</pre>";
}

echo "STEP 3 COMPLETED";

// Close the connection
curl_close($request);


//
// END OF CODE FOR STEP 3
//

?>
