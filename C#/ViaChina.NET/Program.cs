using System;
using System.Text;
using System.Dynamic;
using System.Collections.Generic;
using System.Threading.Tasks;
using System.Net.Http;
using System.IO;

using Newtonsoft.Json;

namespace ViaChina.NET
{
	class MainClass
	{
		public static void Main (string[] args)
		{
			var request = new OrderRequest ();
			Task<string> requestTask = request.createOrder ();
			requestTask.Wait ();		
			Console.WriteLine ("Returned: " + requestTask.Result);
		}
	}

	class OrderRequest
	{
		private string endpoint = "http://api.staging.viachina.com"; // For test system
		private string authToken = "INSERT AUTH TOKEN HERE";

		public async Task<string> createOrder() {
			// Address attributes
			dynamic address = new ExpandoObject();
			address.name = "John Doe";
			address.street1 = "Sample Street 2423";
			address.zip_code = "12345";
			address.town = "Exampleton";
			address.country_code = "DE";

			// Line item attributes
			dynamic lineItem = new ExpandoObject();
			lineItem.original_description = "Car Airbed";
			lineItem.kind = "gift";
			lineItem.hs_code = "9949909043";
			lineItem.price_in_eur = "2000"; // Eurocents, so 2000 Eurocents = 20.00 Euro
			lineItem.qty = 1;
			lineItem.weight = 0.2;

			// Order attributes
			dynamic order = new ExpandoObject();
			order.service  = "DDP31";
			order.bag = 0;
			order.pallet = 0;
			order.client_reference = "QA0200602190010";
			order.domestic_carrier = "DPD";
			order.address_attributes = address;
			order.line_items_attributes = new List<Object> {
				lineItem
			};

			string json = Newtonsoft.Json.JsonConvert.SerializeObject(new {
				pdf_label_paper_size = "A4", // Use A4 for regular sheet paper and A6 for thermal print labels
				order = order
			});

			var client = new HttpClient ();
			client.DefaultRequestHeaders.Add("Authorization", "Bearer " + authToken);
			var orderURL = this.endpoint + "/v1/orders";
			var requestContent = new StringContent (json, Encoding.UTF8, "application/json");
			var response = await client.PostAsync(orderURL, requestContent);
			var responseString = await response.Content.ReadAsStringAsync();

			dynamic responseJson = Newtonsoft.Json.JsonConvert.DeserializeObject(responseString);
			string labelDataString = responseJson["pdf_label_data"];

			// This variable now contains the PDF data for the label
			var labelDataAsPdf = Convert.FromBase64String(labelDataString);

			// Now write it to a file "label.pdf"
			var filePath = @"label.pdf";

			using (var imageFile = new FileStream(filePath, FileMode.Create))
			{
				imageFile.Write(labelDataAsPdf ,0, labelDataAsPdf.Length);
				imageFile.Flush();
			}

			// And return the response for further inspection
			return responseString;
		}
	}
}
