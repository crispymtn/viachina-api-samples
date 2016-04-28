<?php


// Your API token
$authToken = 'INSERT AUTH TOKEN HERE'; // For test system

// The order data to send to the API
$postData = array( 'order' => array(
    'service' => 'DDP31',
    'bag' => 0,
    'pallet' => 0,
    'client_reference' => 'QA0200602190010',
    'domestic_carrier' => 'DPD',
    'address_attributes' => array(
      'name' => 'John Doe',
      'street1' => "Example Street 59",
      'zip_code' => '12345',
      'town' => 'Exampleton',
      'country_code' => 'DE'
    ),
    'line_items_attributes' => array(
      array(
        'original_description' => 'Car Airbed',
        'kind' => 0,
        'hs_code' => '9949909043',
        'price_in_eur' => '2000', // Eurocents, so 2000 Eurocents = 20.00 Euro
        'qty' => '1',
        'weight' => '0.20'
      )
    )
));

// Setup cURL
$request = curl_init('http://api.staging.viachina.com/v1/orders'); // For test system
// $request = curl_init('http://api.viachina.com/v1/orders'); // For production system


curl_setopt_array($request, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$authToken,
        'Content-Type: application/json'
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
$encoded_pdf_label_data = $responseData['pdf_label_data'];

// Decode the PDF label data
$pdf_label_data = base64_decode($encoded_pdf_label_data);

// Send out headers to show PDF in browser
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename=label.pdf');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . mb_strlen($pdf_label_data));
header('Accept-Ranges: bytes');

// Print raw PDF data for browser
echo $pdf_label_data

?>
