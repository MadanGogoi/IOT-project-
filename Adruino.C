# this file contains the code to get the data from the server and send this data to the server using NOdeMCU1.0

#include <ESP8266WiFi.h>
#include <WiFiClient.h> 
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>


// Set the desired credentials.
const char *ssid = "madan"; // wifi name 
const char *pass= " "; // wifi password
const char *server = "http:// "; // server address where u want to send the data


// initializes or defines the output pin of the LM35 temperature sensor
int outputpin= A0;
//this sets the ground pin to LOW and the input voltage pin to high
void setup() {
 delay(1000);
  Serial.begin(115200);
  WiFi.mode(WIFI_OFF);        //Prevents reconnection issue (taking too long to connect)
  delay(1000);
  WiFi.mode(WIFI_STA);        //This line hides the viewing of ESP as wifi hotspot
  
  WiFi.begin(ssid, pass);     //Connect to your WiFi router
  Serial.println("");

  Serial.print("Connecting");
  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  //If connection successful show IP address in serial monitor
  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());  //IP address assigned to your ESP
}

void loop()       //main loop

{

  HTTPClient http;
  String postData,value;
int analogValue = analogRead(outputpin);
float millivolts = (analogValue/1024.0) * 3300; //3300 is the voltage provided by NodeMCU
float celsius = millivolts/10;
//now we change into fahrenheit
float fahrenheit = ((celsius *9)/5 + 32);
Serial.println(" In Fahrenheit = ");
Serial.println(fahrenheit);
value = String(fahrenheit);
postData = "value="+value;
  http.begin(server);              //Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header

  int httpCode = http.POST(postData);   //Send the request
   if (httpCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpCode);
    }
  String payload = http.getString();    //Get the response payload

  Serial.println(httpCode);   //Print HTTP return code
  Serial.println(payload);    //Print request response payload
  

  http.end();  //Close connection
  
  delay(30000);  //Post Data at every 5 seconds



}
