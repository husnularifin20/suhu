#include <EEPROM.h>
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <Wire.h>
#include <stdlib.h>
#include <iostream> 

WiFiClient espClient;
PubSubClient client(espClient);

// Connect to the WiFi
const char* ssid = "WIFI ERROR";
const char* password = "1slkmzwa8awa";
const char* mqtt_server = "riset.revolusi-it.com";

const char* topik = "iot/G_231_19_0223";
 
String messages_terima;
String messages_kirim;

int led1=D1;
int led2=D2;
int led3=D3;
int led4=D4;

int sensor=A0;
float nilai_analog, nilai_volt, nilai_suhu;


 
void reconnect() {
 // Loop until we're reconnected
 while (!client.connected()) {
 Serial.print("Menghubungkan diri ke MQTT Server : "+(String)mqtt_server);
 // Attempt to connect
 if (client.connect("G.231.19.0223-2341")) {
  Serial.println("connected");
  // ... and subscribe to topic
  client.subscribe(topik);
 } else {
  Serial.print("failed, rc=");
  Serial.print(client.state());
  Serial.println(" coba lagi dalam 5 detik...");
  // Wait 5 seconds before retrying
  delay(5000);
  }
 }
}


void konek_wifi()
{
WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected"); 
}

float baca_suhu()
{
  nilai_analog=analogRead(sensor);
  nilai_volt = (nilai_analog/1024)*3;
  nilai_suhu = nilai_volt*100;

  Serial.println("Analog = "+(String)nilai_analog);
  Serial.println("Voltase = "+(String)nilai_volt);
  Serial.println("Suhu = "+(String)nilai_suhu+" celcius");

  return nilai_suhu;
}

void callback(char* topic, byte* payload, unsigned int length) {

Serial.print("Pesan dari MQTT [");
Serial.print(topic);
Serial.print("] ");
messages_terima="";
for (int i=0;i<length;i++) { // susun jadi string saja...
char receivedChar = (char)payload[i];
messages_terima=messages_terima+(char)payload[i]; // ambil pesannya masukkan dalam string
}
  Serial.println(messages_terima);

float suhu=baca_suhu();

messages_kirim=(String)suhu;
  char attributes[100];
messages_kirim.toCharArray(attributes, 100);
client.publish(topik,attributes,true);
Serial.println("messages : "+messages_kirim+" terkirim.....");
delay(1000);

int suhu_led;
suhu_led=messages_kirim.toInt();
 if (suhu_led <= 29){
   digitalWrite(led1,0);
   digitalWrite(led2,0);
   digitalWrite(led3,0);
   digitalWrite(led4,0);
 }if (suhu_led >= 29){
   digitalWrite(led1,1);
   digitalWrite(led2,0);
   digitalWrite(led3,0);
   digitalWrite(led4,0);
   }if (suhu_led >= 30){
   digitalWrite(led1,0);
   digitalWrite(led2,1);
   digitalWrite(led3,0);
   digitalWrite(led4,0);
 }if (suhu_led >= 31){
   digitalWrite(led1,0);
   digitalWrite(led2,0);
   digitalWrite(led3,1);
   digitalWrite(led4,0);
 }



}
 // ------------------------------- begin --------------------------------------- //
void setup()
{
 Serial.begin(9600);
 
 client.setServer(mqtt_server, 1883);
 client.setCallback(callback);
 pinMode(sensor, INPUT);
 pinMode(led1, OUTPUT);
 pinMode(led2, OUTPUT);
 pinMode(led3, OUTPUT);
 pinMode(led4, OUTPUT);
 digitalWrite(led1,0);
 digitalWrite(led2,0);
 digitalWrite(led3,0);
 digitalWrite(led4,0);

}
 
void loop()
{

 if(WiFi.status() != WL_CONNECTED) { konek_wifi(); } // jika tidak konek wifi maka di reconnect
 if (!client.connected()) { reconnect(); } // reconnect apabila belum konek 
 
 client.loop();
 
}
