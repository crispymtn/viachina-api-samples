using System;
using System.Text;
using System.Dynamic;
using System.Collections.Generic;
using System.Threading.Tasks;
using System.Net.Http;
using System.IO;

using Newtonsoft.Json;

namespace ViaEurope.NET
{
	public class Label
	{
		public int parcel_number { get; set; }
		public string pdf_url { get; set; }
		public string tracking_code { get; set; }
	}

	public class OrderResponse
	{
		public string client_reference { get; set; }
		public object hawb { get; set; }
		public List<Label> labels { get; set; }
		public object mawb { get; set; }
		public string reference { get; set; }
	}

	class MainClass
	{
		public static void Main (string[] args)
		{
			var request = new OrderRequest ();
			Task<string> requestTask = request.createOrder ();
			requestTask.Wait ();		
		}
	}

	class OrderRequest
	{
		private string endpoint = "https://app-sandbox.viaeurope.com/api/v1/orders"; // For test system

		// !!! INSERT YOUR AUTH TOKEN HERE !!!
		private string authToken = "INSERT AUTH TOKEN HERE";

		public async Task<string> createOrder() {
			// Address attributes
			dynamic delivery = new ExpandoObject();
			delivery.name = "John Doe";
			delivery.street = "Sample Street 2423";
			delivery.zip_code = "12345";
			delivery.city = "Exampleton";
			delivery.country_code = "DE";
			delivery.courier = "DPD";
			delivery.courier_service = "DDP31";
			delivery.registered = true;

			// Line item attributes
			dynamic lineItem = new ExpandoObject();
			lineItem.description = "Car Airbed";
			lineItem.taric_code = "9949909044";
			lineItem.price = 2000; // Eurocents, so 2000 Eurocents = 20.00 Euro
			lineItem.weight = 200;
			lineItem.qty = 1;
			lineItem.parcel_number = 1;

			// Order attributes
			dynamic order = new ExpandoObject();
			order.client_reference = "QA" + DateTime.Now.ToString("yyyyMMddHHmmssffff");
			order.delivery = delivery;
			order.line_items = new List<Object> {
				lineItem
			};

			string json = Newtonsoft.Json.JsonConvert.SerializeObject(new {
				order = order
			});

			var client = new HttpClient ();
			client.DefaultRequestHeaders.Add("Authorization", "Bearer " + authToken);
			client.DefaultRequestHeaders.Add("Accept", "application/json");
			var requestContent = new StringContent (json, Encoding.UTF8, "application/json");
			var response = await client.PostAsync(this.endpoint, requestContent);
			var responseBody = await response.Content.ReadAsStringAsync();
			var responseStatus = (int)response.StatusCode;

			if (responseStatus != 201) {
				Console.WriteLine("Error!");
				Console.WriteLine("Got response code: " + responseStatus.ToString());
				Console.WriteLine("Response body: " + responseBody);
			}
			else 
			{
				// Parse JSON into "OrderResponse" class
				OrderResponse orderResponse = Newtonsoft.Json.JsonConvert.DeserializeObject<OrderResponse>(responseBody);

				// Get all the labels
				List<Label> labels = orderResponse.labels;

				foreach (var label in labels)
				{
					// For every label, write parcel number and download URL to console
					Console.WriteLine("Parcel Number: " + label.parcel_number + " - Label URL " + label.pdf_url);
				}
			}

			// And return the response for further inspection
			return responseBody;
		}
	}
}
