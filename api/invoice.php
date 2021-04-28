<?php

header("Access-Control-Allow-Origin: *");
/*$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if (!isset($_REQUEST["secret"])) {
  if ((strpos($_SERVER["HTTP_REFERER"],"ACinvoices.php") === FALSE && $_SERVER["HTTP_REFERER"] != "https://costerbuilding.com" && $_SERVER["HTTP_REFERER"] != "https://costercatalog.com/admin/" && $_SERVER['HTTP_REFERER'] != "http://costerbuilding.com/" && $_SERVER['HTTP_REFERER'] != "http://localhost:3000/") && stripos($ua,'android') === false){
    header('HTTP/1.0 403 Forbidden');
    die; //just for good measure
  }
}*/

$filename = __DIR__ . "/invoices/" . $_GET["invoice"];

echo base64_encode(file_get_contents($filename));

/*$filename = __DIR__ . "/invoices/" . $_GET["invoice"];
header("Content-type: application/pdf");
header("Content-Length: " . filesize($filename));
echo (file_get_contents($filename));*/
?>
