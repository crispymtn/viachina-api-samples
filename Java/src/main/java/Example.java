import javax.json.*;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

class Example {
    public static void main(String[] args) {
        // --- For Sandbox Testing ---
        String authorizationToken = "INSERT SANDBOX AUTH TOKEN HERE";
        String systemURL = "https://app-sandbox.viaeurope.com/api/v1/orders";

        // --- For Production System ---
        //String authorizationToken = "INSERT PRODUCTION AUTH TOKEN HERE";
        //String systemURL = "https://app.viaeurope.com/api/v1/orders";

        try {
            URL targetURL = new URL(systemURL);

            HttpURLConnection connection = (HttpURLConnection) targetURL.openConnection();
            connection.setDoOutput(true);
            connection.setDoInput(true);
            connection.setRequestProperty("Content-Type", "application/json");
            connection.setRequestProperty("Accept", "application/json");
            connection.setRequestMethod("POST");
            connection.setRequestProperty ("Authorization", "Token token=\"" + authorizationToken + "\"");
            connection.connect();

            JsonObject order = Json.createObjectBuilder()
                .add("order",  Json.createObjectBuilder()
                    .add("client_reference", "QA0200602190010")
                    .add("disposition", "sale")

                    .add("delivery", Json.createObjectBuilder()
                            .add("courier", "DPD")
                            .add("courier_service", "DDP31")
                            .add("registered", true)
                            .add("name", "John Doe")
                            .add("street", "Example Street 59")
                            .add("zip_code", "12345")
                            .add("city", "Exampleton")
                            .add("country_code", "DE")
                            .add("phone", "+00000000000"))

                    .add("line_items", Json.createArrayBuilder()
                            .add(Json.createObjectBuilder()
                                    .add("description", "Car Airbed")
                                    .add("taric_code", "6109909043")
                                    .add("price", 2000) // Price in cents; 2000 = 20.00 Euro
                                    .add("qty", 1)
                                    .add("weight", 200) // Weight in grams; 200 = 0.2 kg
                                    .add("parcel_number", 1)
                            )
                            .add(Json.createObjectBuilder()
                                    .add("description", "Car Airbed")
                                    .add("taric_code", "6109909043")
                                    .add("price", 50) // Price in cents; 50 = 0.50 Euro
                                    .add("qty", 7)
                                    .add("weight", 1000) // Weight in grams; 1000 = 1.0 kg
                                    .add("parcel_number", 1)
                            )
                      )
                ).build();

            OutputStream toServerStream = connection.getOutputStream();
            OutputStreamWriter writer = new OutputStreamWriter(toServerStream, "UTF8");

            writer.write(order.toString());
            writer.flush();
            writer.close();

            InputStream fromServerStream;
            Boolean success;

            if (connection.getResponseCode() != HttpURLConnection.HTTP_CREATED) {
                System.out.println("Failed! HTTP error code: "  + connection.getResponseCode());
                fromServerStream = connection.getErrorStream();
                success = false;
            } else {
                System.out.println("Success! HTTP code: "  + connection.getResponseCode());
                fromServerStream = connection.getInputStream();
                success = true;
            }

            if(success == true) {
              JsonReader reader = Json.createReader(fromServerStream);
              JsonObject responseJSON = reader.readObject();
              JsonArray labels = responseJSON.getJsonArray("labels");

              for(JsonValue labelValue: labels) {
                JsonObject label = (JsonObject)labelValue;
                String labelPdfURL = label.getString("pdf_url");
                String parcelNumber = label.getString("parcel_number");
                System.out.println("Parcel Number " + parcelNumber + ", PDF URL: " + labelPdfURL);
              }
            } else {
              BufferedReader br = new BufferedReader(new InputStreamReader(fromServerStream, "UTF8"));
              StringBuilder responseStrBuilder = new StringBuilder();

              String output;

              while ((output = br.readLine()) != null) {
                responseStrBuilder.append(output);
              }

              System.out.println("Output from Server: \n" + responseStrBuilder.toString());
            }

            connection.disconnect();

        } catch (MalformedURLException e) {

            e.printStackTrace();

        } catch (IOException e) {

            e.printStackTrace();

        }

    }

}
