### Order attributes

Attribute | Required? | Type | Description | Example
------------- | ------------- | ------------- | -------------
`client_reference` | required | String | Your unique reference for the order | `"ABCDEFG123456"`, `"HK_DPD_2342"`
`hawb` | optional | String | HAWB number for this order. Can be added later. | `"HK_DPD_12345"`, `"12345-EXAMPLE"`
`mawb` | optional | String | MAWB number for this order. Can be added later. | `"123-12345675"`, `"160-63698552"`
`bag_number` | optional | String | The bag identifier for this order | `"1"`, `"15-A"`
`disposition` | required | String | Can either be `stock` or `sale` | `"stock"`, `"sale"`
`line_items` | required | An array of line items for this order | 
`delivery` | required | The delivery information for this order |

    
   

### Line item attributes

Attribute | Required? | Type | Description | Example
------------- | ------------- | ------------- | -------------
`qty` | required | Integer | Quantity of items | `1`, `2`, `426`
`price` | required | Integer | The price of ONE item in cents | `12` for 12 cents, `123` for 1.23€
`weight` | required | Integer | The weight of ONE item in grams | `12` for 12 grams, `1200` for 1.2 kg
`parcel_number` | required | Integer | The parcel, which the item is in | `1`, `2`, must be consecutive numbers starting at 1
`taric_code` | required | String |  The TARIC code (10 digits) for this item | `"8487905910"`
`ecommerce_url` | optional | String | An URL where this item can be found on the internet | `"http://example.org/shop/products/1234"`
`ecommerce_reference` | optional | String | A product reference from your ecommerce system | `"1234"`

    
   

### Delivery attributes

Attribute | Required? | Type | Description | Example
------------- | ------------- | ------------- | -------------
`courier_name` | required | String | Courier you want to ship the order with | `"DPD"`, `"DHL"`, `"UPS"`, `"PostNL"`
`courier_service` | required | String | Service to use | `"DDP31"`, `"DDP2"`, `"DDU"`
`registered` | required | Boolean | Track order? | `true` or `false`
`name` | required | String | Customer name | `"John Doe"`
`company` | optional | String | Customer's company name | `"John Doe Ltd."`
`street` | required | String | Customer's street address | `"Example Street 42"`
`zip_code` | required | String | Customer's zip code | `"12345"`
`city` | required | String | Customer's city | `"Example City"`
`country_code` | required | String | Customer's country as [ISO 3166-2](https://en.wikipedia.org/wiki/ISO_3166-2) code | `"DE"`, `"GB"`, `"NL"`
`phone` | | String | Customer's phone number. Required for UPS shipment. | `"+31477234929"`

