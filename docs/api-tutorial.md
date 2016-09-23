This document will tell you how to use the ViaEurope API, in order to receive labels, create orders and send your shipments.

There are 3 step:
1. Create Order and receive Label
2. Set MAWB and HAWB
3. Update Shipment as 'Ready to Ship"

You can skip step 2 if you already have the MAWB and HABW data available when creating the order.

# Setup 

**You will need an API key from ViaEurope to get started.**

### Endpoints 

Your requests for testing our API must go to
`http://app-sandbox.viaeurope.com/api/v1/`

When you're ready to proceed with using the API, you can direct your
requests to `http://app.viaeurope.com/api/v1/`

Note that these two endpoints will require _different API keys_.

### Authorization & Headers

Your requests must include the following headers:
```
Authorization: Token token="INSERT YOUR API KEY HERE"
Content-Type: application/json
Accept: application/json
```

If you send no Authorization header or the wrong token, you will receive a `401
Unauthorized` response.


# 1. Creating an order and receive Label

Send this example JSON via POST request to 
`http://app-sandbox.viaeurope.com/api/v1/orders`

```
{
  "order": {
    "bag_number": "Bag 1",
    "client_reference": "74644043909723",
    "disposition": "sale",
    "hawb": "12345-EXAMPLE",
    "mawb": "123-12345675",
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

If your request was successful, you will receive a `201 Created` response. 
Otherwise there will be a `422 Unprocessable Entity` response with an `errors`
JSON explaining what went wrong. You will also receive the created order as
JSON:

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
    "hawb": "12345-EXAMPLE",
    "mawb": "123-12345675"
}
```

You can inspect the `pdf` field of every object under `labels` to get a download
link to your order's labels. 

# 2. Assigning HAWB and MAWB to an order

If you don't know HAWB and MAWB at the time you create the order, it is possible
to assign these two at a later date by doing an update.

If you don't have an HAWB, use the MAWB for both the HAWB and MAWB field.

Send the following example JSON via PUT request to 
`http://app-sandbox.viaeurope.com/api/v1/orders/ORDER_CLIENT_REFERENCE`. Be sure
to replace `ORDER_CLIENT_REFERENCE` in the above URL with your reference for 
this order. In the example above, it would be `74644043909723`, so the request 
would go to `http://app-sandbox.viaeurope.com/api/v1/orders/74644043909723`.

Example JSON update to order, assigning HAWB and MAWB:

```
{
  "order": {
    "hawb": "12345-EXAMPLE",
    "mawb": "123-12345675"
  }
}
```

If your request was successful, you will receive a `204 No Content` response. 
Otherwise there will be a `422 Unprocessable Entity` response with an `errors`
JSON explaining what went wrong.


# 3. Set Shipment as "ready to ship"

When you've created all orders belonging to a HAWB, and when you're ready to 
ship that HAWB, you can mark it as "ready" by updating its volumetric weight
and its number of Carton boxes to their correct values. That way, we will know that
you're now ready to ship an HAWB.

Send the following example JSON via PUT request to 
`http://app-sandbox.viaeurope.com/api/v1/hawbs/HAWB_NUMBER`. Be sure
to replace `HAWB_NUMBER` in the above URL with the HAWB number you want to
update. In the example above, it would be `12345-EXAMPLE`, so the request 
would go to `http://app-sandbox.viaeurope.com/api/v1/orders/12345-EXAMPLE`.

Example JSON update to HAWB: 

```
{
  "hawb": {
    "volumetric_weight": "1224",
    "number_of_units": "17"
  }
}
```

This would update the HAWB information to reflect a volumetric weight of 1224 
kg and 17 Carton Boxes / Units.

If your request was successful, you will receive a `204 No Content` response. 
Otherwise there will be a `422 Unprocessable Entity` response with an `errors`
JSON explaining what went wrong.

Careful: after issuing this request, we will assume you are finished with
assigning orders to this HAWB and will proceed with announcing it to our
partners. You cannot add any additional orders to it after this step.

