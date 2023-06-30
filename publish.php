<?php
$host   = "riset.revolusi-it.com";
$port     = 1883;
$username = "WIFI ERROR";
$password = "1slkmzwa8awa";

require("phpMQTT.php");
$message = "D1=1";
$message1 = "D2=1";
$message2 = "D3=1";
$mqtt = new bluerhinos\phpMQTT($host, $port, "G.231.19.0226-2431".rand());

if ($mqtt->connect(true,NULL,$username,$password)) {
  $mqtt->publish("iot/kendali",$message, 0);
  $mqtt->publish("iot/kendali",$message1, 0);
  $mqtt->publish("iot/kendali",$message2, 0);
  $mqtt->close();
}else{
  echo "Fail or time out
";
}
