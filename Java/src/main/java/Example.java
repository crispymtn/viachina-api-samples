import javax.json.Json;
import javax.json.JsonObject;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

class Example {
    public static void main(String[] args) {
        String authorizationToken = "INSERT AUTH TOKEN HERE";
        String systemURL = "http://api.staging.viachina.com/v1/orders";

        try {
            URL targetURL = new URL(systemURL);

            HttpURLConnection connection = (HttpURLConnection) targetURL.openConnection();
            connection.setDoOutput(true);
            connection.setDoInput(true);
            connection.setRequestProperty("Content-Type", "application/json");
            connection.setRequestProperty("Accept", "application/json");
            connection.setRequestMethod("POST");
            connection.setRequestProperty ("Authorization", "Bearer " + authorizationToken);
            connection.connect();

            JsonObject order = Json.createObjectBuilder()
                .add("order",  Json.createObjectBuilder()
                    .add("service", "DDP31")
                    .add("bag", 0)
                    .add("pallet", 0)
                    .add("client_reference", "QA0200602190010")
                    .add("domestic_carrier", "DPD")

                    .add("address_attributes", Json.createObjectBuilder()
                            .add("name", "John Doe")
                            .add("street1", "Example Street 59")
                            .add("zip_code", "12345")
                            .add("town", "Exampleton")
                            .add("country_code", "DE"))

                    .add("line_items_attributes", Json.createArrayBuilder()
                            .add(Json.createObjectBuilder()
                                    .add("original_description", "Car Airbed")
                                    .add("kind", 0)
                                    .add("hs_code", "6109909043")
                                    .add("price_in_eur", 2000) // This price is in CENTS!
                                    .add("qty", 1)
                                    .add("weight", 0.20)
                            )
                            .add(Json.createObjectBuilder()
                                    .add("original_description", "Normal Airbed")
                                    .add("kind", 0)
                                    .add("hs_code", "6109909043")
                                    .add("price_in_eur", 4000) // This price is in CENTS!
                                    .add("qty", 4)
                                    .add("weight", 0.80)
                            )
                    )
                ).build();

            OutputStream toServerStream = connection.getOutputStream();
            OutputStreamWriter writer = new OutputStreamWriter(toServerStream, "UTF-8");

            writer.write(order.toString());
            writer.flush();
            writer.close();

            InputStream fromServerStream;

            if (connection.getResponseCode() != HttpURLConnection.HTTP_CREATED) {
                System.out.println("Failed! HTTP error code: "  + connection.getResponseCode());
                fromServerStream = connection.getErrorStream();
            } else {
                System.out.println("Success! HTTP code: "  + connection.getResponseCode());
                fromServerStream = connection.getInputStream();
            }

            BufferedReader br = new BufferedReader(new InputStreamReader(fromServerStream));

            String output;

            System.out.println("Output from Server: \n");

            while ((output = br.readLine()) != null) {
                System.out.println(output);
            }

            connection.disconnect();

        } catch (MalformedURLException e) {

            e.printStackTrace();

        } catch (IOException e) {

            e.printStackTrace();

        }

    }

}
