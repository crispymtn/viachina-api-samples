This document will tell you how to use the ViaEurope API, in order to create 
orders and receive labels for your orders.

**You will need an API key from ViaEurope to get started.**

Your requests must go to:
`http://app-sandbox.viaeurope.com/api/v1/orders`

Your requests must include the following headers:
```
Authorization: Token token="INSERT YOUR API KEY HERE"
Content-Type: application/json
Accept: application/json
```

If you send no Authorization header or the wrong token, you will receive a `401
Unauthorized` response.

# Creating your first order

Send this example JSON via POST request to 
`http://app-sandbox.viaeurope.com/api/v1/orders`

```
{
  "order": {
    "bag_number": "Bag 1",
    "client_reference": "74644043909723",
    "disposition": "sale",
    "line_items": [
      {
        "description": "UV Lamp",
        "qty": 2,
        "weight": 400,
        "price": 1999,
        "parcel_number": 1,
        "taric_code": "8487905910",
        "ecommerce_url": "http://example.org/shop/product_12345ABC",
        "ecommerce_reference": "12345ABC"
      },
      {
        "description": "Water Glass",
        "qty": 1,
        "weight": 300,
        "price": 250,
        "parcel_number": 2,
        "taric_code": "8487905910",
        "ecommerce_url": "http://example.org/shop/product_12346BCD",
        "ecommerce_reference": "12346BCD"
      }
    ],
    "delivery": {
      "courier_name": "DPD",
      "courier_service": "DDP31",
      "registered": true,
      "name": "John Doe",
      "company": "John Doe Company",
      "street": "Example Street 42",
      "zip_code": "12345",
      "city": "Example Town",
      "country_code": "DE",
      "phone": "+4916012345678"
    }
  }
}
```

You will receive a response like this:

```
{
    "client_reference": "74644043909723",
    "reference": "VEOR3E70760A"
    "labels": [
        {
            "parcel_number": "1",
            "pdf": "http://...",
            "tracking_code": "09988540036769"
        }
    ],
    "hawb": null,
    "mawb": null,
}
```

You can inspect the `pdf` field of every object under `labels` to get a download
link to your order's labels. 
