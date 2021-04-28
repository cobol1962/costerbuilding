<?php
  session_start();

/*  if (!isset($_SERVER["HTTP_REFERER"]) && !isset($_REQUEST["secret"])) {

    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }
  if (!strpos($_SERVER["HTTP_REFERER"], $_SERVER['HTTP_HOST']) && !isset($_REQUEST["secret"])) {
    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }

  */
  //error_reporting(E_ALL);
  //ini_set('display_errors', 1);
  unset($_POST["secret"]);
unset($_POST["secret"]);

 include "config/database.php";

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
 //require_once("highriseapi.class.php");
//require __DIR__ . '/googlecalendar/vendor/autoload.php';

  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");
  $actions = explode("/", $_GET["request"]);
  $action = str_replace("-", "_" ,$actions[0]);
  $data = [];
  $updated = 0;
  $itemsupdated = 0;
  $encrypt = 0;
if (isset($_POST["encrypted"])) {
  $encrypt = 1;
  unset($_POST["encrypted"]);
  $ts = array(
      "data" => array(
        "post" => $_POST,
        "action" => "decrypt"
     )
   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp = (array) json_decode($server_output);
  //  var_dump($rsp);
  } else {
    $rsp = $_POST;
  }

  $r = $action($rsp, [], $mysqli);
  if (isset($_GET["callback"])) {
    echo $_GET['callback']. "(".json_encode($r).")";
  } else {
    if ($encrypt == 0) {
      echo json_encode($r);
    } else {
      $ts = array(
          "data" => array(
            "post" => $r,
            "action" => "encrypt"
         )
       );

        $pload = json_encode($ts);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 1000000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $rsp = json_decode($server_output);

        if (isset($rsp->error)) {
            echo json_encode($r);
        } else {
          echo json_encode($rsp);
        }
    }
  }

  exit;
  function getShowRooms($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "select WarehouseID as showroomid,Warehouse as name from warehouses where isshowroom=1";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function insertRoom($data, $params, $mysqli) {

    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "insert into showrooms (name) Values ('" . $data["name"] . "')";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = $sql;
    }
    return $res;
  }
  function updateShowRoom($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "update  showrooms set name='" . $data["name"] . "' where showroomid='$showroomid'" ;

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = $sql;
    }
    return $res;
  }
  function deleteShowroom($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "delete from  showrooms  where showroomid='$showroomid'" ;

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = $sql;
    }
    return $res;
  }
  function getSalespersons($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "select * from salespersons " . (!isset($salespersonid) ? "" : " where salespersonid='$salespersonid'") ;

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
        $res["status"] = "ok";
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function getTours($data, $params, $mysqli) {
    $res = [];
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "select `ProjId` as DT_RowId, `PrivateID`, `ProjId`, `ProjName`, `Email`, `AVisitDateTime`, `TouroperatorID`,
    `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`,
    `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `country`, `language`  from tours order by AVisitDateTime desc";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {

        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function getToursLimited($data, $params, $mysqli) {
    $res = [];
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "select `ProjId` as DT_RowId, `PrivateID`, `ProjId`, `ProjName`, `Email`, `AVisitDateTime`, `TouroperatorID`,
    `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`,
    `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `country`, `language`  from tours order by AVisitDateTime desc LIMIT 10";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $row["AVisitDateTime"] = substr(explode(" ", $row["AVisitDateTime"])[0],5) . " " . substr(explode(" ", $row["AVisitDateTime"])[1],0,5);
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function getPrivateTours($data, $params, $mysqli) {
    $res = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "select * from privateTours order by tourid";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res[] = $row;

      }
    }
    return $res;
  }
  function insertSalesPerson($data, $params, $mysqli) {

    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "insert into salespersons (name) Values ('" . $data["name"] . "')";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = $sql;
    }
    return $res;
  }
  function updateSalesperson($data, $params, $mysqli) {

    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "update  salespersons set Employee='$Employee',email='$email' where EmplID='$EmplID'" ;

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $sql = "select * from salespersons where EmplID='$EmplID'" ;
    //  $res = $sql;
        $res = mysqli_fetch_assoc($mysqli->query($sql));
    }
    return $res;
  }
  function deleteSalesperson($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "delete from  salespersons  where salepersonid='$salepersonid'" ;

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = $sql;
    }
    return $res;
  }
  function getProduct($data, $params, $mysqli) {

    $res = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
      $sql = "select `ITEM ID`, CONCAT(`Mounting`, ' ', `Category`, ' ', `Total weight crt`, ' ',`ColourTxt`, ' ',`ClarityTxt`,' ',`CutTxt`) as name, `Sales Price` from products  where `ITEM ID`='$serial'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res[] = $row;

      }
    }
    return $res;
  }
  function sendExport($data, $params, $mysqli) {
    $res = [];
    file_put_contents("exports/" . $data["name"], base64_decode($data["pdf"]));
    $res[] = " OK";
    return $res;
  }
  function sendMail($data, $params, $mysqli) {
    $res = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $user = $_POST["user"];
    if ($data["mode"] == "1" || $data["mode"] == "3") {
      $mailto =  (($data["customer"] != "") ? "," . $data["customer"] : "");
    } else {
  //    $mailto = "Costerdiamonds@gmail.com";
        $mailto = "costerdiamonds@gmail.com";
    }
  //  $mailto =  (($data["customer"] != "") ? "," . $data["customer"] : "");
  //  $mailto = 'cobol1962@gmail.com';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com/api/invoice.php?invoice=" . $data["name"] . "&secret=12dddgfgffgfgfggfgfgfg");
  $result=curl_exec($ch);
//  var_dump("https://costercatalog.com/api/invoice.php?invoice=" . $data["name"] . "&secret=12dddgfgffgfgfggfgfgfg");
  curl_close($ch);
  //  $subject = "Invoice from Royal Coster Diamonds";
  //  $filename = __DIR__ . "/invoices/" . "SalesInvoice_20210120_902318_gb.pdf";
    $attachement = $result;
    //$attachement = chunk_split(base64_encode(file_get_contents("https://costercatalog.com/api/invoices/" . $data["name"])));
  //  $content = chunk_split($data["pdf"]);
    // a random hash will be necessary to send mixed content
    $separator = md5(time());
    $rabdom_hash = $separator;
    // carriage return type (RFC)
    $eol = "\r\n";
  //  $from = "info@royalcoster.com";
    // main header (multipart mandatory)
    $headers = "";
  //  $items = json_decode($data["items"]);



    if ($data["mode"] != "2") {
      $headers .= "Bcc: Costerdiamonds@gmail.com\r\n";
    }


    $headers .= "Reply-To: <invoice@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
    $headers .= "Return-Path: <invoice@costercatalog.com>\r\n";
    $headers .= 'From: invoice@costercatalog.com' . "\r\n";
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;


    // message
    $htmlContent = '
      <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
       <div style="padding:15px;width:600px;text-align:center;"><img style="width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logo.png" /></div>
      <span style="font-size:30px;margin-top:20px;font-weight:bold;">Thank you for your order, ' . $customerName . '.</span>
      <br /> <span style="font-size:20px;">Please find your invoice attached.</span>

      ';

  $htmlContent .= '<div style="padding:15px;width:600px;text-align:center;"><img style="width:200px;" src="https://costercatalog.com/costerdemo/coster/www/images/bag.png" /></div>';


    $htmlContent .= "<p style='color:green !important;text-align:center;font-size:17px;color:#646464;'>You already paid for this order on " . $date . " invoice number " . $invoiceNumber . "</p>";
    $htmlContent .= '<p style="text-align:center;font-size:17px;color:#646464;">This email contains your invoice for your recent purchase at Royal Coster Diamonds.
     Please add this email address to your contact list to avoid delivery in your spam folder in the future.</p>';

      $htmlContent .= "<p style='text-align:center;font-size:17px;color:#646464;'><b>Please contact us for any question you might have.</b></p>";
      $htmlContent .= "<br /><i><span style='text-align:center;font-size:17px;color:#646464;'>Call: +310 (20) 3055 555</span>";
      $htmlContent .= "<br /><span style='text-align:center;font-size:17px;color:#646464;'>© 2020 Royal Coster Diamonds BV. All rights reserved.</span>";
      $htmlContent .= "<br /><span style='text-align:center;font-size:17px;color:#646464;'>Paulus Potterstraat 2, 1071 CZ  Amsterdam |  The Netherlands.</span></i>";

    $htmlContent .= '</body></html>';
    $body = "";
  	$body .= "--".$separator.$eol;
  	$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
  	$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;//optional defaults to 7bit
  	$body .= $htmlContent.$eol;

  	// attachment
  	$body .= "--".$separator.$eol;
  	$body .= "Content-Type: application/octet-stream; name=\"".$data["name"]."\"".$eol;
  	$body .= "Content-Transfer-Encoding: base64".$eol;
  	$body .= "Content-Disposition: attachment".$eol.$eol;
  	$body .= $attachement.$eol;
  	$body .= "--".$separator."--";
    //SEND Mail
    if (mail($mailto, $subject, $body, $headers)) {
        $res[] = "invoices/" . $data["name"] + ".pdf"; // or use booleans here
    } else {
        $res[] =  function_exists( 'mail' ) . " mail send ... ERROR! " . json_encode(error_get_last()) . "\n" . json_encode($data);

    }
    $res["base64"] = $attachement;
    return $res;
  }
  function insertWebCustomer($data,$params, $mysqli) {
      $adr = $data["country"] . ", ". $data["zip"] . ' ' . $data["city"] . ", " . $data["address1"];
      $res = [];
      $fields = [];
      $values = [];
      foreach ($data as $k => $v) {
        $fields[] = "`" . $k . "`";
        $values[] = "'" . $v . "'";
      }
      foreach ($data as $k => $v) {
        $$k = $v;
      }
      $f = implode(",", $fields);
      $d = implode(",", $values);
      $sql = "select * from web_customers where email='$email'";
      $r = $mysqli->query($sql);

      if ($r->num_rows == 0) {
          $sql = "insert into `web_customers` ($f) values ($d)";
      } else {
          while ($row = mysqli_fetch_assoc($r)) {
            $res["customerid"] = $row["customerid"];
          }
          $sql = "update web_customers set `afilliateid`='$afilliateid', `name`='$name',`email`='$email',`country`='$country',`countryCode`='$countryCode',`address1`='$address1',`address2`='$address2',`city`='$city',`zip`='$zip',`ringsize`='$ringsize',`telephone`='$telephone' WHERE email='$email'";
      }
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
      } else {
        $res["status"] = "ok";
        if ($r->num_rows == 0) {
            $res["customerid"] = $mysqli->insert_id;
        }
      }
      return $res;
  }
  function insertWebShipping($data,$params, $mysqli) {
      $adr = $data["country"] . ", ". $data["zip"] . ' ' . $data["city"] . ", " . $data["address1"];
      $res = [];
      $fields = [];
      $values = [];
      foreach ($data as $k => $v) {
        $fields[] = "`" . $k . "`";
        $values[] = "'" . $v . "'";
      }
      foreach ($data as $k => $v) {
        $$k = $v;
      }
      $f = implode(",", $fields);
      $d = implode(",", $values);
      $sql = "insert into `web_shipping` ($f) values ($d)";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
      } else {
        $res["status"] = "ok";

      }
      return $res;
  }
  function insertCustomer($data,$params, $mysqli) {
    $adr = $mysqli->real_escape_string($data["country"]) . ", ". $data["zip"] . ' ' . $data["city"] . ", " . $mysqli->real_escape_string($data["address1"]);

    $res = [];
    $fields = [];
    $values = [];
    foreach ($data as $k => $v) {
      $fields[] = "`" . $k . "`";
      $values[] = "'" . $mysqli->real_escape_string($v) . "'";
    }
    $f = implode(",", $fields);
    $d = implode(",", $values);
    $sql = "insert into `customers` ($f) values ($d)";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
      $res["customerid"] = $mysqli->insert_id;

/*    $url = 'http://costerdiamonds.api-us1.com';
    $params = array(
      'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
      'api_action'   => 'contact_add',
      'api_output'   => 'json',
    );
    $nm = explode(" ", $data["name"]);
    $fn = $nm[0];
    if (isset($nm[1])) {
      $l = [];
      for ($i=1;$i<count($nm);$i++) {
        $l[] = $nm[$i];
      }
      $ln = implode(" ", $l);
    } else {
      $ln = "";
    }
    $tags = [];
    if ($data["email"] == "") {
      $tags[] = "mist email";
    }
    if ($data["telephone"] == "") {
      $tags[] = "no phone";
    }
    $tg = implode(",", $tags);
    $post = array(
      'email'                    => ($data["email"] == "") ? str_pad((string) $mysqli->insert_id, 6, "0", STR_PAD_LEFT) ."@nomail.com" : $data["email"],
      'first_name'               => $fn,
      'last_name'                => $ln,
      'phone'                    => $data["telephone"],
      'tags' => $tg,
      'status[123]'              => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 1)
    );
    $query = "";
    foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
    $query = rtrim($query, '& ');
    $data = "";
    foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
    $data = rtrim($data, '& ');

    // clean up the url
    $url = rtrim($url, '/ ');

    // This sample code uses the CURL library for php to establish a connection,
    // submit your request, and show (print out) the response.
    if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

    // If JSON is used, check if json_decode is present (PHP 5.2.0+)
    if ( $params['api_output'] == 'json' && !function_exists('json_decode') ) {
        die('JSON not supported. (introduced in PHP 5.2.0)');
    }

    // define a final API request - GET
    $api = $url . '/admin/api.php?' . $query;

    $request = curl_init($api); // initiate curl object
    curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
    curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
    //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
    curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

    $response = (string)curl_exec($request); // execute curl post and store results in $response

    // additional options may be required depending upon your server configuration
    // you can find documentation on curl options at http://www.php.net/curl_setopt
    curl_close($request); // close curl objec*/
  }
  return $res;

}
  function updateCustomer($data, $params, $mysqli) {
    $u = [];
    $res = [];
    $adr = $mysqli->real_escape_string($data["country"]) . ", ". $data["zip"] . ' ' . $mysqli->real_escape_string($data["city"]) . ", " . $mysqli->real_escape_string($data["address1"]);

    foreach ($data as $k => $v) {
      if ($k != "customerid") {
        $u[] = "`" . $k . "`='" . $mysqli->real_escape_string($v) . "'";
        $kk = $mysqli->real_escape_string($v);
      }
    }
    $e = "";

    if (true) {

      $e = $data["email"];
      $up = implode(",", $u);
      $sql = "update customers set " . $up . " where customerid='" . $data["customerid"] . "'";
      if (!($re = mysqli_query($mysqli,$sql))) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;

      } else {
        $res["status"] = "ok";
        $res["sql"] = $sql;
        $res["action"] = "update";
      }
    /*  $listId = "4592419c64";

      $data = array(
        "email_address" => $data["email"],
        "email_type" => "html",
        "status" => "subscribed",
        "merge_fields" => array(
            'FNAME' => $data["firstName"],
            "LNAME" => $data["lastName"],
            "ADDRESS" => $adr,
            "PHONE" => $data["telephone"],
            "SALESPERSO" => "",
            "MMERGE7" => "",
            "MMERGE9" => "",
            "MMERGE10" => ""
        ),
        //"ip_signup" => $_SERVER{"HTTP_REFERER"],
        "timestamp_signup" => date('Y-m-d H:i:s'),
        "tags" => array("Customers"),
        "location" => array("country_code" => $data["country"])
      );
      $data_string = json_encode($data);

      $ch = curl_init("https://us6.api.mailchimp.com/3.0/lists/" . $listId . "/members/" . md5(strtolower($e)));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Authorization: apikey '. "5a58cf78c2595f820e5a3e5723717dac-us6",
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string))
      );

      $result = curl_exec($ch);
      $res["mc"] = $result;
      $res["adr"] =  $adr;
      return $res;
    } else {
      $sql = "delete from customers where customerid='" . $data["customerid"] . "'";
      if (!($re = mysqli_query($mysqli,$sql))) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;

      } else {
        $res["status"] = "ok";
        $res["sql"] = $sql;
        $res["action"] = "delete";
      }*/
      return $res;
    }
  }
  function updateInvoiceFinance($data,$params, $mysqli) {
    $res = [];
    foreach (array_keys($data) as $w) {
      $$w = $mysqli->real_escape_string($data[$w]);
    }
    $sel = "select * from invoices where invoiceid='$invoiceid'";
    $ssel = $mysqli->query($sel);
    $row = mysqli_fetch_assoc($ssel);

    foreach (array_keys($row) as $w) {
      $$w = $mysqli->real_escape_string($row[$w]);
    }

    $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$invoiceid."' and version=''";
    $rsum = $mysqli->query($ssum);
    $firstpaid = floatval(mysqli_fetch_assoc($rsum)["paid"]);

    $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$invoiceid."'";
    $rsum = $mysqli->query($ssum);
    $paid = floatval(mysqli_fetch_assoc($rsum)["paid"]);

    $initialdue = floatval($dueAmount) - floatval($firstpaid);

    $currentdue = floatval($dueAmount) - floatval($paid);
    $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
    $rsum = $mysqli->query($ssum);
    $rs = mysqli_fetch_assoc($rsum);
    $startingTotal = floatval($rs["startingTotal"]);
    $totaldiscount = floatval($startingTotal) - floatval($dueAmount);
    $isDue = ((floatval($currentdue) > 0) ? 1 : 0);
    $isDiscount = ((floatval($totaldiscount) > 0) ? 1 : 0);
    $usql = "update invoices set initialdue='$initialdue',paid='$paid',totaldiscount='$totaldiscount',currentdue='$currentdue',
    isDiscounted='$isDiscount', isDue='$isDue', total=$startingTotal
    where invoiceid='$invoiceid'";
    $mysqli->query($usql);
    $res["sql"] = $usql;
    return $res;
  }
  function updateInvoiceFinance_all($data,$params, $mysqli) {
    $res = [];
    foreach (array_keys($data) as $w) {
      $$w = $mysqli->real_escape_string($data[$w]);
    }
    $sql = "select * from invoices";
    $rez = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($rez)) {
          foreach (array_keys($row) as $w) {
            $$w = $mysqli->real_escape_string($row[$w]);
          }

          $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$invoiceid."' and version=''";
          $rsum = $mysqli->query($ssum);
          $firstpaid = floatval(mysqli_fetch_assoc($rsum)["paid"]);

          $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$invoiceid."'";
          $rsum = $mysqli->query($ssum);
          $paid = floatval(mysqli_fetch_assoc($rsum)["paid"]);

          $initialdue = floatval($dueAmount) - floatval($firstpaid);

          $currentdue = floatval($dueAmount) - floatval($paid);
          $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
          $rsum = $mysqli->query($ssum);
          $rs = mysqli_fetch_assoc($rsum);
          $startingTotal = floatval($rs["startingTotal"]);
          $totaldiscount = floatval($startingTotal) - floatval($dueAmount);
          $isDue = ((floatval($currentdue) > 0) ? 1 : 0);
          $isDiscount = ((floatval($totaldiscount) > 0) ? 1 : 0);
          $usql = "update invoices set initialdue='$initialdue',paid='$paid',totaldiscount='$totaldiscount',currentdue='$currentdue',
          isDiscounted='$isDiscount', isDue='$isDue',total=$startingTotal
          where invoiceid='$invoiceid'";
          $mysqli->query($usql);
          var_dump($usql);
      }
    return ["ok"];
  }
  function insertInvoice($data,$params, $mysqli) {
    $res = [];
    $fields = [];
    $values = [];
    foreach ($data as $k => $v) {
      $fields[] = "`" . $k . "`";
      $values[] = "'" . addslashes($v) . "'";
    }
    $f = implode(",", $fields);
    $d = implode(",", $values);
    $sql = "insert into `invoices` ($f) values ($d)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
      $res["invoiceid"] = $mysqli->insert_id;

    }
    return $res;
  }
  function insertWebInvoice($data,$params, $mysqli) {
    $res = [];
    $fields = [];
    $values = [];
    foreach ($data as $k => $v) {
      $fields[] = "`" . $k . "`";
      $values[] = "'" . $v . "'";
    }
    $f = implode(",", $fields);
    $d = implode(",", $values);
    $sql = "insert into `web_invoices` ($f) values ($d)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
      $res["invoiceid"] = $mysqli->insert_id;
    }
    return $res;
  }
  function updateWebInvoice($data,$params, $mysqli) {
    $res = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "update web_invoices set `pdf`='$pdf' where invoiceid='$invoiceid'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";

    }
    return $res;
  }
  function getCustomers($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
    $sql = "select customers.*, tours.touroperater from customers
      inner join tours on customers.TourNo=tours.ProjId
      inner join invoices on customers.customerid=invoices.customerid
       where invoices.salePersonId='" . $data["salePersonId"] . "'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function allCustomers($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
    $sql = "select customers.*, tours.touroperater from customers
      inner join tours on customers.TourNo=tours.ProjId
      inner join invoices on customers.customerid=invoices.customerid";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function getlogs($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
    $sql = "select * from log order by datetime DESC LIMIT 500";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function getsystemlogs($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
    $sql = "select * from systemlog order by datetime DESC LIMIT 500";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;

      }
    }
    return $res;
  }
  function checkSalesPerson($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "SELECT * FROM `salespersons` where EmplID='$EmplID' and pin='$pin'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
      return $res;
    }
    $r = $mysqli->query($sql);
    if ($r->num_rows == 0) {
      $res["status"] = "fail";
      $res["type"] = "2";
      $res["title"] = "Invalid PIN code";
      $res["sql"] = $sql;
      return $res;
    } else {
      $res["sp"] = mysqli_fetch_assoc($r);
      $res["status"] = "ok";
      return $res;
    }

  }
  function registerSalePerson($data, $params, $mysqli) {
    $res = [];
    $fields = [];
    $values = [];
    foreach ($data as $k => $v) {
      $fields[] = "`" . $k . "`";
      $values[] = "'" . $v . "'";
    }
    $f = implode(",", $fields);
    $d = implode(",", $values);
    $sql = "insert into `salespersons` ($f) values ($d)";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
    return $res;
  }
  function getSPData($data, $params, $mysqli) {
    $res = [];
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $sql = "SELECT * FROM `salespersons`";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
      return $res;
    }
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[$row["EmplID"]] = $row;
    }
    return $res;
  }
  function updateSPData($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      if ($k != "salepersonid") {
        $u[] = "`" . $k . "`='" . $v . "'";
        $kk = $v;
      }
    }
    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $up = implode(",", $u);
    $sql = "update salespersons set " . $up . " where EmplID='" . $data["salepersonid"] . "'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["error"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
      return $res;
    } else {
      $res["status"] = "ok";
      return $res;
    }

  }

  function resetPassword($data, $params, $mysqli) {
    $res["data"] = [];
    foreach ($data as $k => $v) {
      if ($k != "salepersonid") {
        $u[] = "`" . $k . "`='" . $v . "'";
        $kk = $v;
      }
    }

    foreach ($data as $k => $v) {
      $$k = $v;
    }
    $up = implode(",", $u);
    $np = generateRandomString();
    $sql = "update salespersons set `password`='$np' where salepersonid='" . $data["salepersonid"] . "'";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["sql"] = $sql;
      return $res;
    } else {
      $res["status"] = "ok";
      mail($email, 'New password for Sales App', "Your new automatic generated password is " . $np . ". Use this password to login in Sales app. And after login you can change passwprd.");
      return $res;
    }

  }
  function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function updateInvoiceDocuments($data, $params = [], $mysqli) {
  $res = [];
/*  $sql = "select documentLanguages from invoices where invoiceid='" . $data["invoiceid"] . "'";
  $result = mysqli_query($sql);
  $r = $mysqli->query($sql);
  $row = mysqli_fetch_assoc($r);
  $dl = explode(",", $row["documentLanguages"]);
  if (!in_array ( $data["language"], $dl )) {
    $dl[] = $data["language"];
  }*/
  foreach ($data as $k => $v) {
    $$k = addslashes($v);
  }

  $sql = "update invoices set showroom='$showroom',
                            customerid='$customerid',
                            showroom='$showroom',
                            salePersonId='$salePersonId',
                            salesPerson='$salesPerson',
                            showroomid='$showroomid',
                            tourNo='$tourNo',
                            total='$total',
                            discount='$discount',
                            discountAmount='$discountAmount',
                            dueAmount='$dueAmount',
                            vatexcluded='$vatExcluded',
                            vat='$vat',
                            vatrefund='$vatRefund',
                            directrefund='$directRefund',
                            version='$version',
                            saledate='$saledate',
                            reference='$reference',
                            isproform='$isproform',
                            remark='$remark',
                            admincharge='$adminCharge' where invoiceid='$invoiceid'";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["sql"] = $sql;
    return $res;
  } else {
    $res["status"] = "ok";
    $res["invoiceid"] = $data["invoiceid"];
    return $res;
  }
}
function updateInvoicepdf($data, $params = [], $mysqli) {
  $res = [];
/*  $sql = "select documentLanguages from invoices where invoiceid='" . $data["invoiceid"] . "'";
  $result = mysqli_query($sql);
  $r = $mysqli->query($sql);
  $row = mysqli_fetch_assoc($r);
  $dl = explode(",", $row["documentLanguages"]);
  if (!in_array ( $data["language"], $dl )) {
    $dl[] = $data["language"];
  }*/
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "update invoices set  pdf='$pdf',documentname='$documentName' where invoiceid='$invoiceid'";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["sql"] = $sql;
    return $res;
  } else {
    $res["status"] = "ok";
    $res["invoiceid"] = $data["invoiceid"];
    return $res;
  }
}
function deleteInvoiceBody($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "delete from `invoice_body`  where invoiceid='$invoiceid'";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["sql"] = $sql;
    return $res;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function insertInvoiceBody($data, $params, $mysqli) {

  $res = [];
  $fields = [];
  $values = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sql = "INSERT INTO `invoice_body`(`invoiceid`, `imageURL`, `SerialNo`,`productName`,`CompName`, `SalesPrice`, `Discount`, `startRealPrice`, `realPrice`, `MainGroup`,`quantity`)
   VALUES ('$invoiceid','$imageURL','$SerialNo', '$name','$CompName', '$SalesPrice', '$Discount', '$startRealPrice', '$realPrice', '$MainGroup','$quantity')";
  $res["sql"] = $sql;

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function insertWebInvoiceBody($data, $params, $mysqli) {

  $res = [];
  $fields = [];
  $values = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sql = "INSERT INTO `web_invoice_body`(`invoiceid`, `imageURL`, `SerialNo`,`productName`,`CompName`, `quantity`,`total`,`SalesPrice`, `Discount`, `startRealPrice`, `realPrice`, `MainGroup`)
   VALUES ('$invoiceid','$imageURL','$SerialNo', '$name','$CompName', '$quantity', '$total', '$SalesPrice', '$Discount', '$startRealPrice', '$realPrice', '$MainGroup')";
  $res["sql"] = $sql;

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function deleteInvoicePayments($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "delete from `invoice_payments`  where invoiceid='$invoiceid'";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["sql"] = $sql;
    return $res;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function insertInvoicePayments($data, $params, $mysqli) {
  $res = [];
  $fields = [];
  $values = [];
  foreach ($data as $k => $v) {
    $fields[] = "`" . $k . "`";
    $values[] = "'" . $v . "'";
  }
  $f = implode(",", $fields);
  $d = implode(",", $values);
  $sql = "insert into `invoice_payments` ($f) values ($d)";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function getExcangeRates($data, $params, $mysqli) {
  $res = [];
  $sql = "SELECT * from exchangeRates";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }

  return $res;
}
function excangeRates($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?excangerates");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  curl_close($ch);
  $response = json_decode($result, true);

  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Exchangerates response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $sql = "TRUNCATE exchangeRates";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/exchangeRates.csv'
    INTO TABLE exchangeRates FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (CurrencyCode,Currency,ExchangeRate,ExRateDate)";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com/shopify/api.php?request=updateCurrencyRates");
      $result=curl_exec($ch);
      curl_close($ch);
    } else {
      $res["status"] = "ok";
    }
  }
  return $res;
}
function inventSerial($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?inventserial");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Invent Serials response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
     $sql = "TRUNCATE inventSerialNew";
     $mysqli->query($sql);

      $sql1 = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inventSerial.csv'
      INTO TABLE `inventSerialNew` FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
      (SerialNo,ItemID,ProdDate,SerialTxt,RingSize,SubLocation,SerialName)";
      if (!mysqli_query($mysqli,$sql1)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql1;
      } else {
        $res["status"] = "ok";
      }
  }
    $sql = "select * from inventSerial where SerialNo NOT IN (select SerialNo from inventSerialNew)";
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      foreach (array_keys($row) as $w) {
        $$w = $mysqli->real_escape_string($row[$w]);
      }
      $ss = "insert into inventSerialNew (`SerialNo`, `ItemID`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `SerialName`)
      VALUES ('$SerialNo', '$ItemID', '$ProdDate', '$SerialTxt', '$RingSize', '$SubLocation', '$SerialName')";
      $mysqli->query($ss);

  }
    $sql = "TRUNCATE costertemp.inventSerial";
    $mysqli->query($sql);
    $sql = "insert into costertemp.inventSerial select * from costertemp.inventSerialNew";
    $mysqli->query($sql);

  $sql = "TRUNCATE costerdiamonds.inventSerial";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.inventSerial select * from costertemp.inventSerial";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  return($res);

}
function jewelCompositions($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?jewelcompositions");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  //$response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Jewel compositions response ' . $result .  "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
//if (true) {
    $updated = 1;
     $sql = "TRUNCATE jewelCompositions";
     $mysqli->query($sql);
      $sql1 = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/jewelCompositions.csv'
      INTO TABLE `jewelCompositions` FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
      (Qnt,TotalWeight,ClarityID,ColourID,CutID,TypeID,ProductID,CompName)";
      if (!mysqli_query($mysqli,$sql1)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql1;
      } else {
        $res["status"] = "ok";
      }
      $sql = "TRUNCATE jewelCompositionsShort";
      $mysqli->query($sql);
       $sql1 = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/jewelCompositions.csv'
       INTO TABLE `jewelCompositionsShort` FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
       (Qnt,TotalWeight,ClarityID,ColourID,CutID,TypeID,ProductID,CompName)";
       if (!mysqli_query($mysqli,$sql1)) {
         $res["status"] = "fail";
         $res["type"] = "Mysql error";
         $res["title"] = mysqli_error($mysqli);
         $res["sql"] = $sql1;
       } else {
         $res["status"] = "ok";
       }

      $sql = "select * from attributes";
      $rez = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($rez)) {
        if ($row["MainAttribute"] == "") {
          $sqlu = "update attributes set MainAttributeValue='" . $row["Atribute"] . "' where AtributeID='" . $row["AtributeID"] . "'";
        } else {
          $ss = "select * from mainAttributes where MainAttributeID='" . $row["MainAttribute"] . "'";
          $rw = mysqli_fetch_assoc($mysqli->query($ss));
          $sqlu = "update attributes set MainAttributeValue='" . $rw["MainAttributeDesc"]. "'  where AtributeID='" . $row["AtributeID"] . "'";
        }

        $mysqli->query($sqlu);
      }
      $sql = "TRUNCATE costerdiamonds.attributes";
      $mysqli->query($sql);
      $sql = "insert into  costerdiamonds.attributes select * from costertemp.attributes";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
      }



      $sql = "Update  jewelCompositions a
              set
              a.ClarityID = IFNULL((select Atribute from attributes b
              where a.ClarityID =b.AtributeID),'')";
      $mysqli->query($sql);
      $sql = "Update  jewelCompositions a
              set
              a.ColourID = IFNULL((select Atribute from attributes b
              where a.ColourID =b.AtributeID),'')";
      $mysqli->query($sql);
      $sql = "Update  jewelCompositions a
          set
          a.CutID = IFNULL((select Atribute from attributes b
          where a.CutID =b.AtributeID),'')";
      $mysqli->query($sql);

      $sql = "Update  jewelCompositions a
              set
              a.TypeID = IFNULL((select Atribute from attributes b
              where a.TypeID =b.AtributeID),'')";
      $mysqli->query($sql);
      $sql = "Update  jewelCompositions a
              set
              a.ColorDesc = IFNULL((select ColorDesc from itemsColors b
              where a.ColorID =b.ColorID),'')";
      $mysqli->query($sql);


      $sql = "Update  jewelCompositionsShort a
              set
              a.ClarityID = IFNULL((select MainAttributeValue from attributes b
              where a.ClarityID =b.AtributeID),'')";
      $mysqli->query($sql);
      $sql = "Update  jewelCompositionsShort a
              set
              a.ColourID = IFNULL((select MainAttributeValue from attributes b
              where a.ColourID =b.AtributeID),'')";
      $mysqli->query($sql);
      $sql = "Update  jewelCompositionsShort a
          set
          a.CutID = IFNULL((select MainAttributeValue from attributes b
          where a.CutID =b.AtributeID),'')";
      $mysqli->query($sql);

      $sql = "Update  jewelCompositionsShort a
              set
              a.TypeID = IFNULL((select MainAttributeValue from attributes b
              where a.TypeID =b.AtributeID),'')";
      $mysqli->query($sql);

    }
    $sql = "TRUNCATE costerdiamonds.jewelCompositions";
    $mysqli->query($sql);
    $sql = "insert into  costerdiamonds.jewelCompositions select * from costertemp.jewelCompositions";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
    }
    $sql = "TRUNCATE costerdiamonds.jewelCompositionsShort";
    $mysqli->query($sql);
    $sql = "insert into  costerdiamonds.jewelCompositionsShort select * from costertemp.jewelCompositionsShort";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
    }
    return($res);

}

function attributes($data, $params, $mysqli) {
  $res = [];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?attributes");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


  $result=curl_exec($ch);
  var_dump($result);
  $response = json_decode($result, true);

  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Attributes response ' . $result .  "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE attributes";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inv_Attributes.csv'
    INTO TABLE attributes FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (AtributeID,AtributeType,AtributeValue,AtributeSeq,Atribute,AtributeShort,MainAttribute)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    }
  }
  $sql = "update `attributes` set Atribute=REPLACE(Atribute,'ï¿½','é')";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["sql"] = $sql;
    $res["status"] = "ok";
  }
  return($res);

}

function warehouses($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?warehouses");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Warehouses response ' . $result . "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE warehouses";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/warehouses.csv'
    INTO TABLE warehouses FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (WarehouseID,Warehouse,WarehouseSeq,IsShowroom)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}

function colors($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?colors");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Colors response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE itemsColors";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inventColors.csv'
    INTO TABLE itemsColors FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (	ColorID,ColorDesc)";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }

  }
  return($res);

}

function brands($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?brands");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Brands response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE brands";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/brands.csv'
    INTO TABLE brands FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (	BrandID, Brand)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}

function groups($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?groups");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Groups response ' . $result .  "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE groups";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/groups.csv'
    INTO TABLE groups FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (	ItemGroupID, ItemGroup, SubGroupRecID, MainGroupRecID, ItemTypeID)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function showrooms($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?showroomssave");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Showrooms response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE showrooms";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/showrooms.csv'
    INTO TABLE showrooms FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`showroomid`, `name`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function mainGroups($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?maingroups");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Main groups response ' . $result .  "\r\n");
  echo "main";
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE mainGroups";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/mainGroups.csv'
    INTO TABLE mainGroups FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (mainGroupID, MainGroup,MainGroupShort, MainGroupRecID)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}

function subGroups($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?subgroups");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Subgroups response ' . $result .  "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE subGroups";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/subGroups.csv'
    INTO TABLE subGroups FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (SubGroupID, SubGroup, SubGroupRecID)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}

function lockedInvoices($data, $params, $mysqli) {
  $fp = fopen('synctours.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Locked invoices started' . "\n");
  fclose($fp);
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?lockedinvoices");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  var_dump($response);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Locked invoices response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE lockedInvoices";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/lockedInvoices.csv'
    INTO TABLE lockedinvoices FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (invoiceid)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $sql = "TRUNCATE costerdiamonds.lockedinvoices";
      $mysqli->query($sql);
      $sql = "insert into  costerdiamonds.lockedinvoices select * from costertemp.lockedinvoices";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
      }
      $sql = "update  costerdiamonds.invoices set locked='1' where invoiceid in (select CONVERT(SUBSTRING(invoiceid, 2), SIGNED) as invoiceid from costerdiamonds.lockedinvoices where invoiceid<>'')";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
      } else {
        var_dump("invoices locked");
      }
    }
    $fp = fopen('synctours.log', 'a');
    fwrite($fp, date("F j, Y, g:i a") . " " . 'Sales/AC started' . "\r\n");
    fclose($fp);
    sales($data, $params, $mysqli);
    insertActualSalesInAC($data, $params, $mysqli);
  //  createDeals($data, $params, $mysqli);
    $fp = fopen('synctours.log', 'a');
    fwrite($fp, date("F j, Y, g:i a") . " " . 'Sales/AC Deals done' . "\r\n");
    fclose($fp);
  }


  $res["status"] = "ok";


    var_dump("invoices locked done");
  return($res);

}
function privateTours($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?privatetours");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Private tours response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE privateTours";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/privateTours.csv'
    INTO TABLE privateTours FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`ProjID`, `PrivateID`, `Visitdate`, `CosterGuideID`,`LastName`, `FirstName`, `EMail`, `CountryID`, `PAX`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function projects($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?projects");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Projects response ' . $result .  "\r\n");
  curl_close($ch);
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE projects";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/projects.csv'
    INTO TABLE projects FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (ProjId,ProjName,AVisitDateTime,TouroperatorID,TouroperatorRefNo,WholesalerID,WholesalerRefNo,TourleaderID,GuideID,HotelID,PAX,CountryID)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function vendors($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?vendors");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Vendors response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE vendors";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/vendors.csv'
    INTO TABLE vendors FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (VendorID, VendorName)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function country($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?countries");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Countries response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE countries";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/countries.csv'
    INTO TABLE countries FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`CountryID`, `Country`, `EUMember`, `Nationality`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function getpaymentmethods($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?paymentmethods");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Payment methods response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE paymentMethods";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/paymentMethods.csv'
    INTO TABLE paymentMethods FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`PaymentID`, `Payment`, `Rounding`, `IsVatRefund`, `IsAdminCharge`, `IsWWFTCheck`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function getAreas($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?areas");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Areas response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE areas";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/areas.csv'
    INTO TABLE areas FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`AreaID`, `Area`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}

function getActcSales($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?soldserials");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Sold serials response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?actcsales");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result=curl_exec($ch);
    $response = json_decode($result, true);
    $fp = fopen('synclog.log', 'a');
    fwrite($fp, date("F j, Y, g:i a") . " " . 'actcSales response ' . $result .  "\r\n");
    $response = json_decode($result, true);
    $sql = "TRUNCATE soldSerials";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/soldSerials.csv'
    INTO TABLE soldSerials FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`invoiceNumber`,`date`, `serialNo`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
    $sql = "TRUNCATE costerdiamonds.soldSerials";
    $mysqli->query($sql);
    $sql = "insert into costerdiamonds.soldSerials (invoiceNumber,serialNo) select invoiceNumber,serialno from costertemp.soldSerials";
    $mysqli->query($sql);


    $sql = "TRUNCATE actcSales";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/actcSales.csv'
    INTO TABLE actcSales FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`TransDate`, `ProjID`, `ExtInvoiceNo`, `PrivateRegNo`, `SalesCountryName`, `MainGroup`, `SalesPerson`, `Brand`, `Showroom`, `Turnover`, `Discount`,`invoiceNumber`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
    sales($data, $params, $mysqli);

    insertActualSalesInAC($data, $params, $mysqli);
  //  createDeals($data, $params, $mysqli);
    $fp = fopen('synctours.log', 'a');
    fwrite($fp, date("F j, Y, g:i a") . " " . 'Sales/Deals AC done' . "\n");
    fclose($fp);

  }
  return($res);

}

function getActcPrivates($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?actcprivates");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'actcPrivates response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE actcPrivates";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/actcPrivates.csv'
    INTO TABLE actcPrivates FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`PrivateID`, `FirstName`, `LastName`, `CountryID`, `Nationality`, `Visitors`, `VisitDate`, `IntGuideID`, `IntGuideName`, `HotelID`, `HotelName`, `Agent`, `InquiryID`, `Inquiry`, `EMail`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}
function getMainAttributes($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?mainattributes");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'mainAttributes response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE mainAttributes";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inv_MainAttributes.csv'
    INTO TABLE mainAttributes FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`MainAttributeID`, `MainAttributeDesc`, `MainAttributeType`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $sql = "TRUNCATE costerdiamonds.mainAttributes";
      $mysqli->query($sql);
      $sql = "insert into  costerdiamonds.mainAttributes select * from costertemp.mainAttributes";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
      }
      $res["status"] = "ok";
    }
  }
  return($res);

}
function getSchedule($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?schedule");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Schedule response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
  //if (true) {
    $updated = 1;
    $sql = "TRUNCATE schedule_new";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/schedule.csv'
    INTO TABLE schedule_new FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`empid`, `empname`, `department`, `date`, `type`, `description`, `abscence`, `holiday`, `desciptionEN`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";

      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $sql = "select *,group_concat(type) as type,group_concat(desciptionEN) as desciption from schedule_new " . $q . " group by date,empid";
      $rez = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($rez)) {
        foreach (array_keys($row) as $w) {
          $$w = $mysqli->real_escape_string($row[$w]);
        }
        $si = "insert into schedule ( `empid`, `empname`, `department`, `date`, `type`, `description`, `abscence`, `holiday`, `desciptionEN`) values
        ('$empid', '$empname', '$department', '$date', '$type', '$description', '$abscence', '$holiday', '$desciptionEN')
        ON DUPLICATE KEY UPDATE  `department`='$department',`type`='$type', `description`='$description,', `abscence`='$abscence',
         `holiday`='$holiday', `desciptionEN`='$desciptionEN'";
         var_dump($si);
         $mysqli->query($si);
      }
    //  insertEvents($data, $params, $mysqli);
      $res["status"] = "ok";
    }
  }
  $sql = "TRUNCATE costerdiamonds.schedule_new";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.schedule_new (select * from costertemp.schedule_new)";
  $mysqli->query($sql);
  $sql = "TRUNCATE costerdiamonds.schedule";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.schedule (select * from costertemp.schedule)";
  $mysqli->query($sql);
  return $res;

}
function insertEvents($data, $params, $mysqli) {
  $res = ["ok"];
  $client = getcalendarClient();
  $service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
  $calendarId = 'iqgpcbnkkqpte6kh35j0ah5p68@group.calendar.google.com';
  //$service->calendars->clear($calendarId);
//  return $res;
  $sql = "select *,group_concat(id) as id,group_concat(type) as t,group_concat(desciptionEN) as d from schedule where  calid='' and abscence='False' group by date,empid";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {

    $event = new Google_Service_Calendar_Event(array(
      'summary' => $row["empname"] . " - " . $row["department"] . " (" . $row["t"] . ")",
      'location' => $row["department"],
      'description' => $row["d"],
      'start' => array(
        'date' => $row["date"],
      ),
      'end' => array(
        'date' => $row["date"],
      )
    ));
      $event = $service->events->insert($calendarId, $event);
      $uu = "update schedule set calid='" . $event->id . "' where id IN (" . $row["id"] . ")";
      $mysqli->query($uu);
    var_dump($uu);
     sleep(1);
      var_dump($event->htmlLink);
  }
  $sql = "TRUNCATE costerdiamonds.schedule_new";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.schedule_new (select * from costertemp.schedule_new)";
  $mysqli->query($sql);
  $sql = "TRUNCATE costerdiamonds.schedule";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.schedule (select * from costertemp.schedule)";
  $mysqli->query($sql);
  return $res;
}
function getActcGroups($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?actcgroups");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'actcGroups response ' . $result .  "\r\n");
  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE actcGroups";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE 'csv/actcGroups.csv'
    INTO TABLE actcGroups FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`ProjId`, `AVisitDateTime`, `ProjName`, `TouroperatorID`, `TOName`, `TouroperatorRefNo`, `WholesalerID`, `WSName`, `WholesalerRefNo`, `TourleaderID`, `TLName`, `GuideID`, `GDName`, `PAX`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }
  return($res);

}


function getEmployees($data, $params, $mysqli) {
  $res = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?employees");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Employees response ' . $result .  "\r\n");
  if (true) {
    $updated = 1;
    $sql = "TRUNCATE costerdiamonds.salespersons_new";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/employees.csv'
    INTO TABLE costerdiamonds.salespersons_new FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`EmplID`, `Employee`, `AreaID`, `Email`, `SalesApp`, `Admin`, `Dashboard`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
    $sql = "select * from costerdiamonds.salespersons_new";
    $rez = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($rez)) {
      $ss = "select * from costerdiamonds.salespersons where EmplID='" . $row["EmplID"] . "'";
      $rz = $mysqli->query($ss);

      if (mysqli_num_rows($rz) == 0) {
        $isq = "insert into costerdiamonds.salespersons
        (`EmplID`, `Employee`, `AreaID`, `Email`, `SalesApp`, `Admin`, `Dashboard`, `firebaseid`, `status`, `pin`)
        select `EmplID`, `Employee`, `AreaID`, `Email`, `SalesApp`, `Admin`, `Dashboard`, `firebaseid`, `status`,'1840' from salespersons_new where (EmplID='" . $row["EmplID"] . "')";
        $mysqli->query($isq);
      }
    }
    $sql = "select * from costerdiamonds.salespersons_new";
    $rez = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($rez)) {
      if ($row["Email"] != "") {
        $sup = "update costerdiamonds.salespersons set Email='" .$row["Email"] . "' where EmplID='" . $row["EmplID"] . "'";
        $mysqli->query($sup);
      }
    }


    $sql = "select * from costerdiamonds.salespersons";
    $rez = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($rez)) {
      if ($row["Email"] == "") {
        $em = str_replace(" ",'', $row["Employee"]) . "@costerdiamonds.com";
        $sup = "update costerdiamonds.salespersons set Email='$em' where id='" . $row["id"] . "'";
        $mysqli->query($sup);
      }
    }
  }
  return $res;

}
function sendPassword($data, $params, $mysqli) {

  $to = $_REQUEST["email"];
  $headers = "";

  $subject = "Login informations";
  $headers .= "Reply-To: <accounts@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
  $headers .= "Return-Path: <accounts@costercatalog.com>\r\n";
  $headers .= 'From: accounts@costercatalog.com' . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  // message
  $htmlContent = '
    <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
     <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
    <span style="font-size:30px;margin-top:24px;font-weight:bold;">Dear ' . $_REQUEST["displayName"] . ',</span>
    <br /> <span style="font-size:20px;">In order to finish registration, please follow <a href="' . base64_decode($_REQUEST["link"]) . '">this link and set password</span>".
   ';


      $htmlContent .= '</body></html>';
      mail($to, $subject, $htmlContent, $headers);

      $sql = "update salespersons set status='2' where `email`='" . $_REQUEST["email"] . "'";
      $mysqli->query($sql);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?useractivated");
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $result=curl_exec($ch);
      return $_REQUEST;
}
function updateTours($data, $params, $mysqli) {
  $fp = fopen('synctours.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Update tours started' . "\n");
  fclose($fp);
  createsystemlog(["name" => "Tours", "activity" => 'Update tours started'], $params, $mysqli);
  $res = [];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?privatetours");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $fp = fopen('synctours.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Private tours ' . $response["status"] .  "\r\n");
  fclose($fp);
  createsystemlog(["name" => "Tours", "activity" => 'Private tours ' . $response["status"]], $params, $mysqli);
  if ($response["status"] == "Has updates") {

    $updated = 1;
    $sql = "TRUNCATE privateTours";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/privateTours.csv'
    INTO TABLE privateTours FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (`ProjID`, `PrivateID`, `Visitdate`, `CosterGuideID`,`LastName`, `FirstName`, `EMail`, `CountryID`, `PAX`)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?projects");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $response = json_decode($result, true);
  curl_close($ch);
  $fp = fopen('synctours.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Projects ' . $response["status"] .  "\r\n");
  fclose($fp);
  createsystemlog(["name" => "Tours", "activity" => 'Projects ' . $response["status"]], $params, $mysqli);

  if ($response["status"] == "Has updates") {
    $updated = 1;
    $sql = "TRUNCATE projects";
    $mysqli->query($sql);
    $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/projects.csv'
    INTO TABLE projects FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
    (ProjId,ProjName,AVisitDateTime,TouroperatorID,TouroperatorRefNo,WholesalerID,WholesalerRefNo,TourleaderID,GuideID,HotelID,PAX,CountryID)";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
  }

  $sql = "TRUNCATE tours";
  $mysqli->query($sql);

  $sql = "select *, concat(`FirstName`, ' ', `LastName`) as name from privateTours";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $dd = explode(" ",$row["Visitdate"]);
    $tt = $dd[1] . ":00";
    $ddd = explode("-", $dd[0]);
    $tt = $ddd[2] . "-" . $ddd[1] . "-" . $ddd[0] . " " . $tt;
    $ss = "insert into tours (`PrivateID`,`ProjId`, `ProjName`, `AVisitDateTime`,
     `GuideID`, `email`, `PAX`, `CountryID`)
     VALUES ('$PrivateID', '$ProjID', '$name','$tt', '$CosterGuideID', '$EMail', '$PAX', '$CountryID')";
     $mysqli->query($ss);
  }
  $sql = "select * from projects";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $ss = "insert into tours (`ProjId`, `ProjName`, `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`,
    `HotelID`, `PAX`, `CountryID`, `email`, `touroperater`, `wholesaler`, `country`, `language`)
     VALUES ('$ProjId', '$ProjName', '$AVisitDateTime', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID', '$WholesalerRefNo', '$TourleaderID', '$GuideID',
     '$HotelID', '$PAX', '$CountryID', '$email', '$touroperater', '$wholesaler', '$country', '$language')";
     $mysqli->query($ss);
  }
  $sql = "Update  tours a
          set
          a.country = IFNULL((select Country from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  tours a
          set
          a.country = IFNULL((select Country from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  tours a
          set
          a.language = IFNULL((select Nationality from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  tours a
          set
          a.EUMember = IFNULL((select EUMember from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  tours a
          set
          a.wholesaler = IFNULL((select VendorName from vendors b
          where a.WholesalerID =b.VendorID),'')";
  $mysqli->query($sql);
  $sql = "Update  tours a
          set
          a.touroperater = IFNULL((select VendorName from vendors b
          where a.TouroperatorID =b.VendorID),'')";
  $mysqli->query($sql);
  $sql = "TRUNCATE costerdiamonds.privateTours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.privateTours select * from costertemp.privateTours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "TRUNCATE costerdiamonds.tours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.tours select * from costertemp.tours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $fp = fopen('synctours.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Update tours ended ' . json_encode($res) . "\n");
  fclose($fp);
  createsystemlog(["name" => "Tours", "activity" => 'Update tours ended ' . json_encode($res)], $params, $mysqli);
//updatedatabasic($data, $params, $mysqli);
if ($updated == 1) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?tours");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result=curl_exec($ch);
  }
  return $res;
}
function updateSearchTable($data, $params, $mysqli) {
  $res = [];
/*  $sql = "TRUNCATE costerdiamonds.products_search";
  $mysqli->query($sql);*/
/*  $sql = "insert into  costerdiamonds.products_search select * from costerdiamonds_2020.products_search
  where costerdiamonds_2020.products_search.itemid in (select distinct(itemid) from costerdiamonds.products)
   ON DUPLICATE KEY
  UPDATE costerdiamonds.products_search.ItemID=costerdiamonds_2020.products_search.ItemID";*/
  return $res;
}
function getItems($data, $params, $mysqli) {
  var_dump("Get items started");
  $itemsupdated = 0;
  $res = [];
  $updated = 0;
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Tours Sync started' . "\n");
  fclose($fp);
  createsystemlog(["name" => "Products", "activity" => '5 mins products started '], $params, $mysqli);
  //updateTours($data, $params, $mysqli);
  lockedInvoices($data, $params, $mysqli);
  getMainAttributes($data, $params, $mysqli);

    return ["sync ok"];

}
function backup_db($data,$patams,$mysqli) {
  $filename='database_backup_'.date("Y-m-d_H:i:s") .'.sql';

  exec('mysqldump costerdiamonds --password=C9kczWoqo8GCosP4 --user=root --single-transaction >/var/www/dbbackup/'.$filename);
  $res = ["ok"];
  return $res;
}
function getItemsComplete($data, $params, $mysqli) {
  echo "Get items started \r\n";

  $itemsupdated = 0;
  $res = [];
  $updated = 0;
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Main Sync started' . "\n");
  fclose($fp);
//  createsystemlog(["name" => "Products", "activity" => 'Sync products started '], $params, $mysqli);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  attributes($data, $params, $mysqli);
  warehouses($data, $params, $mysqli);
  colors($data, $params, $mysqli);
  brands($data, $params, $mysqli);
  groups($data, $params, $mysqli);
  mainGroups($data, $params, $mysqli);
  subGroups($data, $params, $mysqli);
//  lockedInvoices($data, $params, $mysqli);
  getActcSales($data, $params, $mysqli);
  getActcPrivates($data, $params, $mysqli);
  getActcGroups($data, $params, $mysqli);
  showrooms($data, $params, $mysqli);
  vendors($data, $params, $mysqli);
  country($data, $params, $mysqli);
  getpaymentmethods($data, $params, $mysqli);
  getAreas($data, $params, $mysqli);
  getEmployees($data, $params, $mysqli);
  excangeRates($data, $params, $mysqli);
  //  getSchedule($data, $params, $mysqli);
  var_dump("here 1111");

  updatedatabasic($data, $params, $mysqli);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync basic done' . "\n");
  fclose($fp);
   createsystemlog(["name" => "Products", "activity" => 'Sync basic done '], $params, $mysqli);

  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?onhand");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  curl_close($ch);
  $response = json_decode($result, true);
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Onhand response ' . $result . "\r\n");
  fclose($fp);
  echo "responseeeeeeeeeeee " . $response["status"];
  $fp = fopen('synclog.log', 'a');
  fwrite($fp, date("F j, Y, g:i a") . " " . 'Main tables ' . $response["status"] . "\r\n");
  fclose($fp);
//   createsystemlog(["name" => "Products", "activity" => 'Sync basic done '], $params, $mysqli);
   inventSerial($data, $params, $mysqli);
   jewelCompositions($data, $params, $mysqli);
   convertRedInvoices($data, $params, $mysqli);
    //  $result11=exec('mysqldump costerdiamonds --password=Rm#150620071010 --user=root --single-transaction >/var/www/database_backup/'.$filename,$output);
      $updated = 1;
      $sql = "TRUNCATE onHandSerialNew";
      $mysqli->query($sql);
      $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inv_OnhandSerial.csv'
      INTO TABLE onHandSerialNew FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
      (ItemID,OnhandQnt,WareHouseID,SerialNo,ColorId)";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
      } else {
        $res["status"] = "ok";
      }

      $sql = "select * from onHandSerial where serialNo NOT IN (select serialNo from onHandSerialNew)";

      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        foreach (array_keys($row) as $w) {
          $$w = $mysqli->real_escape_string($row[$w]);
        }
        $ss = "insert into costertemp.onHandSerialNew (`ItemID`, `OnhandQnt`, `WareHouseID`, `SerialNo`, `ColorId`, `ColorDesc`)
        VALUES ('$ItemID', '0', '$WareHouseID', '$SerialNo', '$ColorId', '$ColorDesc')";

        $mysqli->query($ss);

      }



      $sql = "TRUNCATE onHandSerial";
      $mysqli->query($sql);
      $sql = "INSERT INTO `onHandSerial`(`ItemID`, `OnhandQnt`, `WareHouseID`, `SerialNo`, `ColorId`, `ColorDesc`)
      select `ItemID`, sum(`OnhandQnt`) as OnhandQnt, `WareHouseID`, `SerialNo`, `ColorId`, `ColorDesc` from onHandSerialNew group by serialno";
    //  $sql = "insert into onHandSerial select * from onHandSerialNew";
      $mysqli->query($sql);


      $sql = "TRUNCATE costerdiamonds.onHandSerial";
      $mysqli->query($sql);
      $sql = "insert into costerdiamonds.onHandSerial select * from costertemp.onHandSerial";
      $mysqli->query($sql);


      $sql = "TRUNCATE costerdiamonds.solditems";
      $mysqli->query($sql);
      $sql = "insert into costerdiamonds.solditems select * from costertemp.solditems";
      $mysqli->query($sql);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?items");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result=curl_exec($ch);
    curl_close($ch);
    $response = json_decode($result, true);
    $response = json_decode($result, true);
    $fp = fopen('synclog.log', 'a');
    fwrite($fp, date("F j, Y, g:i a") . " " . 'Items response ' . $result .  "\r\n");

      echo 'Items update start ';
      $fp = fopen('synclog.log', 'a');
      fwrite($fp, date("F j, Y, g:i a") . " " . 'Items update start ' . $response["status"] . "\n");
      fclose($fp);
      $updated = 1;
      $itemsupdated = 1;
      $sql = "TRUNCATE itemsNew";
      $mysqli->query($sql);
      $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/itemTable.csv'
      INTO TABLE itemsNew FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
      (ItemID,BrandID,ProductID,ItemGroupID,SalesPrice,Discount,ImageName,ItemName)";

      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
      } else {
        $res["status"] = "ok";
      }

      $sql = "select * from items where ItemID NOT IN (select ItemID from itemsNew)";
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        foreach (array_keys($row) as $w) {
          $$w = $mysqli->real_escape_string($row[$w]);
        }

        $ss = "insert into itemsNew (`ItemID`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `ItemName`)
        VALUES ('$ItemID', '$BrandID', '$ProductID', '$ItemGroupID', '$SalesPrice', '$Discount', '$ImageName', '$ItemName')";
        $mysqli->query($ss);
      }

      $sql = "TRUNCATE items";
      $mysqli->query($sql);
      $sql = "insert into items select * from itemsNew";
      $mysqli->query($sql);
      $sql = "Update  items a
              set
              a.BrandID = IFNULL((select Brand from brands b
              where a.BrandID =b.BrandID),'')";
      $mysqli->query($sql);

      $sql = "TRUNCATE items";
      $mysqli->query($sql);
      $sql = "insert into items select * from itemsNew";
      $mysqli->query($sql);


      $sql = "TRUNCATE costerdiamonds.items";
      $mysqli->query($sql);
      $sql = "insert into costerdiamonds.items select * from costertemp.items";
      $mysqli->query($sql);


    $sql = "TRUNCATE costerdiamonds.items";
    $mysqli->query($sql);
    $sql = "insert into  costerdiamonds.items select * from costertemp.items";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail items";
    }

    $sql = "Update  salespersons a
            set
            a.AreaName = IFNULL((select Area from areas b
            where a.AreaID =b.AreaID),'')";
    $mysqli->query($sql);
    //fixed pin
    $sql = "Update  salespersons a
            set
            pin = 1840";
    $mysqli->query($sql);


    updatedatabasic($data, $params, $mysqli);
    var_dump("Updated ? " . $itemsupdated);
    if ($itemsupdated == 1) {
  //if (true) {


      $sql = "TRUNCATE costertemp.itemsTableView";
      $mysqli->query($sql);
      $sql = "insert into costertemp.itemsTableView (`SerialNo`, `ItemID`,  `ProdDate`, `SerialTxt`, `RingSize`,
       `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`,
       `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`,
        `MainGroup`, `imageURL`)
        select `SerialNo`, `ItemID`,  `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`,
        `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, `ProductID`,
        `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`,
         `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
          `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`
          from costerdiamonds.items_table
           ON DUPLICATE KEY UPDATE itemsTableView.itemid=itemsTableView.itemid";
           var_dump($sql);
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
      } else {
        var_dump("Itemstable view ok");
        $res["status"] = "ok";
      }


      $sql = "Update  itemsTableView a
              set
              a.ItemName = IFNULL((select ItemName from items b
              where a.ItemID =b.ItemID),'')";
              if (!mysqli_query($mysqli,$sql)) {
                $fp = fopen('synclog.log', 'a');
                fwrite($fp, date("F j, Y, g:i a") . " " . 'Fail Items names ' . "\n");
                fclose($fp);
                $res["status"] = "fail item name";
                $res["type"] = "Mysql error";
                $res["title"] = mysqli_error($mysqli);
                $res["sql"] = $sql1;
              } else {
                $fp = fopen('synclog.log', 'a');
                fwrite($fp, date("F j, Y, g:i a") . " " . 'Item Names OK ' . "\n");
                fclose($fp);
                $res["status"] = "ok";
              }
      $sql = "Update  itemsTableView a
              set
              a.SerialName = IFNULL((select SerialName from inventSerial  b
              where a.SerialNo =b.SerialNo LIMIT 1),'')";
              if (!mysqli_query($mysqli,$sql)) {
                $res["status"] = "fail update serial name";
                $fp = fopen('synclog.log', 'a');
                fwrite($fp, date("F j, Y, g:i a") . " " . 'Fail Serial Name ' . "\n");
                fclose($fp);
                $res["type"] = "Mysql error";
                $res["title"] = mysqli_error($mysqli);
                $res["sql"] = $sql1;
              } else {
                $fp = fopen('synclog.log', 'a');
                fwrite($fp, date("F j, Y, g:i a") . " " . 'Serial Names OK ' . "\n");
                fclose($fp);
                $res["status"] = "ok";
              }

      $sql = "update itemsTableView set realPrice= IFNULL(SalesPrice-((SalesPrice / 100) * Discount), 0)";
      $mysqli->query($sql);
      $sql = "TRUNCATE costerdiamonds.itemsTableView";
      $mysqli->query($sql);
      $sql = "insert into  costerdiamonds.itemsTableView select * from costertemp.itemsTableView";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
      }


      $sql = "TRUNCATE products_all";
      $mysqli->query($sql);
      $sql = "insert into products_all (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
      `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
      `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
       `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
        `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
         `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
         select `SerialNo`, `ItemID`,
         `ItemName`, `SerialName`, jewelCompositions.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
         `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositions.`ProductID`,
          `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositions.`TotalWeight`,
          jewelCompositions.`ClarityID`, jewelCompositions.`ColourID`, jewelCompositions.`CutID`,
           jewelCompositions.`TypeID`, jewelCompositions.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
            `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
             `imageURL`, `realPrice`,jewelCompositions.`CompID` from itemsTableView
             left join jewelCompositions on itemsTableView.ProductID=jewelCompositions.ProductID
              ON DUPLICATE KEY UPDATE products_all.itemid=products_all.itemid";
             if (!mysqli_query($mysqli,$sql)) {
               $res["status"] = "fail products temp";
               $res["type"] = "Mysql error";
               $res["title"] = mysqli_error($mysqli);
               $res["sql"] = $sql;
             } else {
               $res["status"] = "ok";
             }
             $sql = "update products_all  set ImageName=CONCAT(`CutID`,'.jpeg') where MainGroup='Diamonds'";
             if (!mysqli_query($mysqli,$sql)) {
               $res["status"] = "fail1";
             }
             //****
             $sql = "TRUNCATE costerdiamonds.products_all";
             $mysqli->query($sql);
             $sql = "insert into  costerdiamonds.products_all select * from costertemp.products_all";
             if (!mysqli_query($mysqli,$sql)) {
               $res["status"] = "fail1";
             }


             $sql = "TRUNCATE products_all_short";
             $mysqli->query($sql);
             $sql = "insert into products_all_short (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
             `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
             `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
              `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
               `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
                `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
                select `SerialNo`, `ItemID`,
                `ItemName`, `SerialName`, jewelCompositionsShort.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
                `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositionsShort.`ProductID`,
                 `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositionsShort.`TotalWeight`,
                 jewelCompositionsShort.`ClarityID`, jewelCompositionsShort.`ColourID`, jewelCompositionsShort.`CutID`,
                  jewelCompositionsShort.`TypeID`, jewelCompositionsShort.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
                   `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
                    `imageURL`, `realPrice`,jewelCompositionsShort.`CompID` from itemsTableView
                    left join jewelCompositionsShort on itemsTableView.ProductID=jewelCompositionsShort.ProductID
                     ON DUPLICATE KEY UPDATE products_all_short.itemid=products_all_short.itemid";
                    if (!mysqli_query($mysqli,$sql)) {
                      $res["status"] = "fail products temp";
                      $res["type"] = "Mysql error";
                      $res["title"] = mysqli_error($mysqli);
                      $res["sql"] = $sql;
                    } else {
                      $res["status"] = "ok";
                    }

                    $sql = "update products_all_short  set ImageName=CONCAT(`CutID`,'.jpeg') where MainGroup='Diamonds'";
                    if (!mysqli_query($mysqli,$sql)) {
                      $res["status"] = "fail123";
                    }
                    //****

                    $sql = "TRUNCATE costerdiamonds.products_all_short";
                    $mysqli->query($sql);
                    $sql = "insert into  costerdiamonds.products_all_short select * from costertemp.products_all_short";
                    if (!mysqli_query($mysqli,$sql)) {
                      $res["status"] = "fail1";
                    }

        $sql = "TRUNCATE products_temp";
        $mysqli->query($sql);
        $sql = "insert into products_temp (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
        `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
        `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
         `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
          `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
           `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
           select `SerialNo`, `ItemID`,
           `ItemName`, `SerialName`, jewelCompositions.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
           `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositions.`ProductID`,
            `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositions.`TotalWeight`,
            jewelCompositions.`ClarityID`, jewelCompositions.`ColourID`, jewelCompositions.`CutID`,
             jewelCompositions.`TypeID`, jewelCompositions.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
              `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
               `imageURL`, `realPrice`,jewelCompositions.`CompID` from
                itemsTableView
               left join jewelCompositions on itemsTableView.ProductID=jewelCompositions.ProductID
               where MainGroup<>'Diamonds'
               ON DUPLICATE KEY UPDATE products_temp.itemid=products_temp.itemid";
               if (!mysqli_query($mysqli,$sql)) {
                 $res["status"] = "fail products temp";
                 $res["type"] = "Mysql error";
                 $res["title"] = mysqli_error($mysqli);
                 $res["sql"] = $sql;
               } else {
                 $res["status"] = "ok";
               }
               echo "products_temp";

               $sql = "TRUNCATE products_temp_short";
               $mysqli->query($sql);
               $sql = "insert into products_temp_short (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
               `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
               `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
                `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
                 `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
                  `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
                  select `SerialNo`, `ItemID`,
                  `ItemName`, `SerialName`, jewelCompositionsShort.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
                  `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositionsShort.`ProductID`,
                   `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositionsShort.`TotalWeight`,
                   jewelCompositionsShort.`ClarityID`, jewelCompositionsShort.`ColourID`, jewelCompositionsShort.`CutID`,
                    jewelCompositionsShort.`TypeID`, jewelCompositionsShort.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
                     `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
                      `imageURL`, `realPrice`,jewelCompositionsShort.`CompID` from
                       itemsTableView
                      left join jewelCompositionsShort on itemsTableView.ProductID=jewelCompositionsShort.ProductID
                      where MainGroup<>'Diamonds'
                      ON DUPLICATE KEY UPDATE products_temp_short.itemid=products_temp_short.itemid";
                      if (!mysqli_query($mysqli,$sql)) {
                        $res["status"] = "fail products temp_short";
                        $res["type"] = "Mysql error";
                        $res["title"] = mysqli_error($mysqli);
                        $res["sql"] = $sql;
                      } else {
                        $res["status"] = "ok";
                      }
                      echo "products_temp_short";


               $sql = "TRUNCATE products_serial";
               $mysqli->query($sql);
               $sql = "insert into products_serial (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
               `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
               `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
                `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
                 `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
                  `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
                  select `SerialNo`, `ItemID`,
                  `ItemName`, `SerialName`, jewelCompositions.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
                  `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositions.`ProductID`,
                   `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositions.`TotalWeight`,
                   jewelCompositions.`ClarityID`, jewelCompositions.`ColourID`, jewelCompositions.`CutID`,
                    jewelCompositions.`TypeID`, jewelCompositions.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
                     `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
                      `imageURL`, `realPrice`,jewelCompositions.`CompID` from itemsTableView
                      left join jewelCompositions on itemsTableView.ProductID=jewelCompositions.ProductID

                      ON DUPLICATE KEY UPDATE products_serial.itemid=products_serial.itemid";
                      if (!mysqli_query($mysqli,$sql)) {
                        $res["status"] = "fail";
                        $res["type"] = "Mysql error";
                        $res["title"] = mysqli_error($mysqli);
                        $res["sql"] = $sql;
                      } else {
                        $res["status"] = "ok";
                      }

        $sql = "delete from products_serial where MainGroup='Diamonds'";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail222";
          $res["type"] = "Mysql error";
          $res["title"] = mysqli_error($mysqli);
          $res["sql"] = $sql;
        } else {
          $res["status"] = "ok";
        }
                      var_dump("here 222");
        $sql = "TRUNCATE products_a";
        $mysqli->query($sql);


        $sql = "insert into products_a SELECT `id`, `SerialNo`, `ItemID`, `ItemName`,
         `SerialName`, group_concat(Distinct(`CompName`)), `ProdDate`,
          `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`,
           `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`,
            `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
            GROUP_CONCAT(distinct(`ColorDesc`)), `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
            `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`, `CompID` FROM `products_temp`
            group By itemid";
        $mysqli->query($sql);
        $sql = "TRUNCATE products_temp";
        $mysqli->query($sql);
        $sql = "insert into products_temp select * from products_a";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail";
          $res["type"] = "Mysql error";
          $res["title"] = mysqli_error($mysqli);
          $res["sql"] = $sql;
        } else {
          $res["status"] = "ok";
        }

        $sql = "insert into products_a_short SELECT `id`, `SerialNo`, `ItemID`, `ItemName`,
         `SerialName`, group_concat(Distinct(`CompName`)), `ProdDate`,
          `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`,
           `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`,
            `ClarityID`, `ColourID`, `CutID`, `TypeID`, `ClarityID_1`, `ColourID_1`, `CutID_1`, `TypeID_1`,
            `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
            GROUP_CONCAT(distinct(`ColorDesc`)), `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
            `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`, `CompID` FROM `products_temp_short`
            group By itemid";
        $mysqli->query($sql);
        $sql = "TRUNCATE products_temp_short";
        $mysqli->query($sql);
        $sql = "insert into products_temp_short select * from products_a_short";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail";
          $res["type"] = "Mysql error";
          $res["title"] = mysqli_error($mysqli);
          $res["sql"] = $sql;
        } else {
          $res["status"] = "ok";
        }

        $sql = "TRUNCATE diamonds";
        $mysqli->query($sql);
        $sql = "insert into diamonds (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
        `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
        `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
         `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
          `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
           `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`) select `SerialNo`, `ItemID`,
           `ItemName`, `SerialName`, jewelCompositions.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
           `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositions.`ProductID`,
            `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositions.`TotalWeight`,
            jewelCompositions.`ClarityID`, jewelCompositions.`ColourID`, jewelCompositions.`CutID`,
             jewelCompositions.`TypeID`, jewelCompositions.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
              `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
               `imageURL`, `realPrice` from itemsTableView
               left join jewelCompositions on itemsTableView.ProductID=jewelCompositions.ProductID
               where MainGroup='Diamonds'
               ON DUPLICATE KEY UPDATE diamonds.SerialNo=diamonds.SerialNo";
        $r =  $mysqli->query($sql);
// DZ items




        $sql = "select id, itemName from products";
        $r = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($r)) {
          $ss = "update products_temp set itemName='" . str_replace("?","é", utf8_decode($row["itemName"])) . "' where id='" . $row["id"] . "'";
          $mysqli->query($ss);
        }
        $sql = "Update  products_temp a
              set
              a.imageURL = IFNULL((select imageURL from images b
              where a.ImageName =b.imageName),'')";
        $mysqli->query($sql);

        $sql = "select id, itemName from products";
        $r = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($r)) {
          $ss = "update products_temp_short set itemName='" . str_replace("?","é", utf8_decode($row["itemName"])) . "' where id='" . $row["id"] . "'";
          $mysqli->query($ss);
        }
        $sql = "Update  products_temp_short a
              set
              a.imageURL = IFNULL((select imageURL from images b
              where a.ImageName =b.imageName),'')";
        $mysqli->query($sql);
        $sql = "Update  products_serial a
              set
              a.imageURL = IFNULL((select imageURL from images b
              where a.ImageName =b.imageName),'')";



        $sql = "update products_temp set  discount=REPLACE((discount, '%', '')";
        $mysqli->query($sql);
        $sql = "update products_temp set realPrice= IFNULL(SalesPrice-((SalesPrice / 100) * Discount), 0))";
        $mysqli->query($sql);
        $s =  "<img style='width:50px;' src='https://costercatalog.com/coster/www/images/logo.png' />";
        $sql = "update products_temp set imageURL=\"" . $s . "\" where ImageName=''";
        $mysqli->query($sql);
        $sql = "update products_temp set Discount=CONCAT(REPLACE(Discount, '%', ''),'%')";
        $mysqli->query($sql);

        $sql = "update products_temp_short set  discount=REPLACE((discount, '%', '')";
        $mysqli->query($sql);
        $sql = "update products_temp_short set realPrice= IFNULL(SalesPrice-((SalesPrice / 100) * Discount), 0))";
        $mysqli->query($sql);
        $s =  "<img style='width:50px;' src='https://costercatalog.com/coster/www/images/logo.png' />";
        $sql = "update products_temp_short set imageURL=\"" . $s . "\" where ImageName=''";
        $mysqli->query($sql);
        $sql = "update products_temp_short set Discount=CONCAT(REPLACE(Discount, '%', ''),'%')";
        $mysqli->query($sql);

        $sql = "select id, itemName from diamonds";
        $r = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($r)) {
          $ss = "update diamonds set itemName='" . str_replace("?","é", utf8_decode($row["itemName"])) . "' where id='" . $row["id"] . "'";
          $mysqli->query($ss);
        }

        $sql = "update diamonds set realPrice= IFNULL(SalesPrice-((SalesPrice / 100) * Discount), 0)";


        $sql = "update diamonds set Discount=CONCAT(Discount,'%')";
        $mysqli->query($sql);
        $s =  "<img style='width:50px;max-width:50px;height:auto;max-height:50px;' src='https://costercatalog.com/coster/www/images/logo.png' />";
        $sql = "update diamonds set imageURL=\"" . $s . "\" where ImageName=''";
        $mysqli->query($sql);
        $sql = "update products_temp set imageName='crown.png' where imageName=''";
        $mysqli->query($sql);

      /*  $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:5000/?getImageUrl");
        $result=curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        foreach ($response as $k => $v) {
          $s =  "<img style='max-width:50px;height:auto;max-height:50px;' src='https://costercatalog.com:5000/?displayImage=" . $v["id"] . "&export=view' />";
          $sql = "update products_temp set imageURL=\"" . $s . "\" where ImageName='" . $v["name"] . "'";
          $mysqli->query($sql);
          $sql = "update products_serial set imageURL=\"" . $s . "\" where ImageName='" . $v["name"] . "'";
          $mysqli->query($sql);
        }*/
        $sql = "select id, CompName, Qnt from products_temp";
        $r = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($r)) {
          $q = 0;
          $cd = explode(",", $row["CompName"]);
          foreach ($cd as $k) {
            $cdd = explode("=", $k);
            $q = $q + intval($cdd[0]);
          }
          $sql = "update products_temp set qnt='" . $q . "' where id='" . $row["id"] . "'";
          $r1 = $mysqli->query($sql);
        }
        $sql = "select id, CompName, Qnt from products_temp_short";
        $r = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($r)) {
          $q = 0;
          $cd = explode(",", $row["CompName"]);
          foreach ($cd as $k) {
            $cdd = explode("=", $k);
            $q = $q + intval($cdd[0]);
          }
          $sql = "update products_temp_short set qnt='" . $q . "' where id='" . $row["id"] . "'";
          $r1 = $mysqli->query($sql);
        }
        $fp = fopen('synclog.log', 'a');
        fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync products done ' . "\n");
        fclose($fp);
        createsystemlog(["name" => "Products", "activity" => 'Sync products done '], $params, $mysqli);

        $sql = "TRUNCATE products";
        $mysqli->query($sql);
        $sql = "insert into products select * from products_temp";
        $mysqli->query($sql);
        $sql = "TRUNCATE products_short";
        $mysqli->query($sql);
        $sql = "insert into products_short select * from products_temp_short";
        $mysqli->query($sql);

        $sql = "select * from jewelCompositions";
        $rez = $mysqli->query($sql);
        while ($row = mysqli_fetch_assoc($rez)) {
          foreach (array_keys($row) as $ww) {
            $$ww = $row[$ww];
          }
          $sqlu = "update products_short set ClarityID_1='$ClarityID',ColourID_1='$ColourID',CutID_1='$CutID',TypeID_1='$TypeID'
          where products_short.ProductID='$ProductID'";
          $mysqli->query($sqlu);
        }



        $sql = "Update  products_serial a
                set
                a.OnhandQnt = IFNULL((select OnHandQnt from onHandSerial b
                where a.serialno =b.serialno),'0')";
        $mysqli->query($sql);
        var_dump("step 111");

        $sql = "delete from products_serial where itemid<>'Certificate' and onHandQnt=0 and serialNo not in
         (select serialNo from protectedItems where DATEDIFF(DATE(NOW()), date) <= 2)";
        $mysqli->query($sql);
        $sql = "TRUNCATE search_table";
        $mysqli->query($sql);
        $sql = "insert into search_table select * from (SELECT * FROM products_serial
            UNION
            SELECT * FROM diamonds) a ";
        $mysqli->query($sql);
var_dump("step 112");
        $sql = "update  search_table set imageName='' where isnull(imageName)";
        $mysqli->query($sql);

        $sql = "update  search_table set imageURL='' where isnull(imageURL)";
        $mysqli->query($sql);

        $sql = "Update  search_table a
                set
                a.CompName = IFNULL((select CompName from products b
                where a.ItemID =b.ItemID),'')";
        $mysqli->query($sql);
        $sql = "Update  products a
              set
              a.itemName = (select itemName from products_a b
              where a.itemID =b.itemID)";
        $mysqli->query($sql);
        var_dump("step 113");
        $sql = "UPDATE  products_serial SET totalweight=ifnull(totalweight,0), qnt=ifnull(Qnt,0)";
        $mysqli->query($sql);
        $sql = "UPDATE  products SET totalweight=ifnull(TotalWeight,0), Qnt=ifnull(Qnt,0)";
        $mysqli->query($sql);
        $sql = "TRUNCATE images";
        $mysqli->query($sql);
        $fp = fopen('synclog.log', 'a');
        fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync databases ' . "\n");
        fclose($fp);
        createsystemlog(["name" => "Products", "activity" => 'Sync databases '], $params, $mysqli);
var_dump("Sync databases ");


        $sql = "update diamonds  set ImageName=CONCAT(`CutID`,'.jpeg')";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail1";
        }

        $sql = "TRUNCATE costerdiamonds.diamonds";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.diamonds select * from costertemp.diamonds";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail1";
        }
      /*  $sql = "TRUNCATE costerdiamonds.products_search";
        echo "\r\n";

        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products_search select * from costertemp.products_search
        where costertemp.products_search.itemid in (select distinct(itemid) from costerdiamonds.products)
         ON DUPLICATE KEY
        UPDATE costerdiamonds.products_search.ItemID=costerdiamonds.products_search.ItemID";*/

      /*  if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail2";
        }*/
        echo "products_search done......";

        $sql = "TRUNCATE costerdiamonds.products";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products select * from costertemp.products";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail3";
        }
        $sql = "TRUNCATE costerdiamonds.products_short";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products_short select * from costertemp.products_short";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail3";
        }
        $sql = "TRUNCATE costerdiamonds.products_serial";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products_serial select * from costertemp.products_serial";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail4";
        }

        $sql = "TRUNCATE costerdiamonds.search_table";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.search_table select * from costertemp.search_table";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail5";
        }
        $fp = fopen('synclog.log', 'a');
        fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync databases done part one' . "\n");
        fclose($fp);
       createsystemlog(["name" => "Products", "activity" => 'Sync databases done '], $params, $mysqli);
         $fp = fopen('synclog.log', 'a');
        fwrite($fp, date("F j, Y, g:i a") . " " . 'Before QNT ' . "\n");
        fclose($fp);
        updateqnt($data, $params, $mysqli);
        createsystemlog(["name" => "Products", "activity" => 'QNT update done '], $params, $mysqli);
        $fp = fopen('synclog.log', 'a');
       fwrite($fp, date("F j, Y, g:i a") . " " . 'Update QNT done' . "\n");
       fclose($fp);

    /*    $sql = "TRUNCATE costerdiamonds.products_search";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products_search select * from costertemp.products_search";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail6";
          var_dump($res);
          die;
        }*/

        $sql = "TRUNCATE costerdiamonds.products";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products select * from costertemp.products";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail7";
        }

        $sql = "TRUNCATE costerdiamonds.products_serial";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.products_serial select * from costertemp.products_serial";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail8";
        }

        $sql = "TRUNCATE costerdiamonds.search_table";
        $mysqli->query($sql);
        $sql = "insert into  costerdiamonds.search_table select * from costertemp.search_table";
        if (!mysqli_query($mysqli,$sql)) {
          $res["status"] = "fail9";
        }


        $fp = fopen('synclog.log', 'a');
        fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync products done ' . json_encode($res) . "\n");
        fclose($fp);
        createsystemlog(["name" => "Products", "activity" => 'Sync products done '], $params, $mysqli);
        $sql = "delete from products where ifnull(OnhandQnt,0)=0";
        $mysqli->query($sql);
        $sql = "delete from products_search where itemid not in (select itemid from products)";
        $mysqli->query($sql);
        insertActualSalesInAC($data, $params, $mysqli);
        //  createDeals($data, $params, $mysqli);
        $fp = fopen('synclog.log', 'a');
       fwrite($fp, date("F j, Y, g:i a") . " " . 'AC/AC Deals done' . "\n");
        return ["ok"];
       // sync shopify
    /*   $ch = curl_init();
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_URL, "http://costercatalog.com:81/shopify");
       $result=curl_exec($ch);
       curl_close($ch);
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_URL, "http://costercatalog.com:81/shopify/api.php?request=deleteSold");
       $result=curl_exec($ch);
       curl_close($ch);*/


    } else {
      $fp = fopen('synclog.log', 'a');
      fwrite($fp, date("F j, Y, g:i a") . " " . 'Sync products done ' . json_encode($res) . "\n");
      fclose($fp);
      createsystemlog(["name" => "Products", "activity" => 'Sync products done '], $params, $mysqli);
      updateSearchTable($data, $params, $mysqli);
      $sql = "delete from products where ifnull(OnhandQnt,0)=0";
      $mysqli->query($sql);
      $sql = "delete from products_search where itemid not in (select itemid from products)";
      $mysqli->query($sql);
      insertActualSalesInAC($data, $params, $mysqli);
  //      createDeals($data, $params, $mysqli);
      $fp = fopen('synclog.log', 'a');
     fwrite($fp, date("F j, Y, g:i a") . " " . 'AC/Deals done' . "\n");
      return ["ok"];
    }

}
function updatedatabasic($data, $params, $mysqli) {
  $res = [];

  /*  $sql = "TRUNCATE costerdiamonds.salespersons";
    $mysqli->query($sql);
    $sql = "insert into  costerdiamonds.salespersons select * from costertemp.salespersons";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
    }*/

  $sql = "TRUNCATE costerdiamonds.areas";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.areas select * from costertemp.areas";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "TRUNCATE costerdiamonds.schedule";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.schedule select * from costertemp.schedule";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }


  $sql = "TRUNCATE costerdiamonds.actcSales";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.actcSales select * from costertemp.actcSales";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.actcGroups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.actcGroups select * from costertemp.actcGroups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.actcPrivates";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.actcPrivates select * from costertemp.actcPrivates";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  //sales();
  $sql = "TRUNCATE costerdiamonds.lockedinvoices";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.lockedinvoices select * from costertemp.lockedinvoices";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "update  costerdiamonds.invoices set locked='1' where invoiceid in (select CONVERT(SUBSTRING(invoiceid, 2), SIGNED) as invoiceid from costerdiamonds.lockedinvoices where invoiceid<>'')";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.brands";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.brands select * from costertemp.brands";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.attributes";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.attributes select * from costertemp.attributes";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }


  $sql = "TRUNCATE costerdiamonds.exchangeRates";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.exchangeRates select * from costertemp.exchangeRates";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.groups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.groups select * from costertemp.groups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.mainGroups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.mainGroups select * from costertemp.mainGroups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.paymentMethods";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.paymentMethods select * from costertemp.paymentMethods";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }



  $sql = "TRUNCATE costerdiamonds.showrooms";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.showrooms select * from costertemp.showrooms";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.subGroups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.subGroups select * from costertemp.subGroups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.tours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.tours select * from costertemp.tours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.vendors";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.vendors select * from costertemp.vendors";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.warehouses";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.warehouses select * from costertemp.warehouses";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  return $res;

}
function updatedata($data, $params, $mysqli) {
  $res = [];
  $res["status"] = "OK";
  $sql = "TRUNCATE costerdiamonds.areas";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.areas select * from costertemp.areas";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "TRUNCATE costerdiamonds.brands";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.brands select * from costertemp.brands";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.attributes";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.attributes select * from costertemp.attributes";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.diamonds";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.diamonds select * from costertemp.diamonds";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.exchangeRates";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.exchangeRates select * from costertemp.exchangeRates";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.groups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.groups select * from costertemp.groups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.mainGroups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.mainGroups select * from costertemp.mainGroups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.paymentMethods";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.paymentMethods select * from costertemp.paymentMethods";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.privateTours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.privateTours select * from costertemp.privateTours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "TRUNCATE costerdiamonds.products";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.products select * from costertemp.products";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.products_serial";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.products_serial select * from costertemp.products_serial";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.search_table";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.search_table select * from costertemp.search_table";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.showrooms";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.showrooms select * from costertemp.showrooms";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.subGroups";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.subGroups select * from costertemp.subGroups";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.tours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.tours select * from costertemp.tours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.vendors";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.vendors select * from costertemp.vendors";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }

  $sql = "TRUNCATE costerdiamonds.warehouses";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.warehouses select * from costertemp.warehouses";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  return $res;
}
function getScannedProduct($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (isset($_GET["serial"])) {
    $SerialNo = $_GET["serial"];
  }
//  $SerialNo = $_GET["serial"];
  $sql = "select itemsTableView.*,products.imageName as imageURL from itemsTableView left Join products on
  itemsTableView.ItemID=products.ItemID where itemsTableView.SerialNo='$SerialNo' limit 1";
  $r =  mysqli_fetch_assoc($mysqli->query($sql));
  if ($r["MainGroup"] != "Diamonds") {
    $sql = "select itemsTableView.*,itemsTableView.imageName as image, itemsTableView.CompName as CompName1 from itemsTableView left Join products_serial on
    itemsTableView.ItemID=products_serial.ItemID where itemsTableView.SerialNo='$SerialNo' limit 1";
      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res[] = $row;
      }
  } else {
    $sql = "select diamonds.imageName as image,diamonds.imageURL as imageURL,itemsTableView.* from itemsTableView left Join diamonds on
    itemsTableView.ItemID=diamonds.ItemID where itemsTableView.SerialNo='$SerialNo' limit 1";
    $r =  ($mysqli->query($sql));
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function getProductByItemID($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sq1 = "select * from itemsTableView where ItemID='$ItemID'";
  $r1 = $mysqli->query($sq1);
  while ($row1 = mysqli_fetch_assoc($r1)) {
    $res[] = $row1;

  }

  return $res;
}
function getMainGroups($data, $params, $mysqli) {
  $res = [];
  if (!isset($_GET["tn"])) {
    $table = "itemsTableView";
  } else {
    $table = $_GET["tn"];
  }

  $sql = "select distinct(SubGroup) as MainGroup from `" . $table . "`  order by MainGroup";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }

  }
  return $res;
}
function getMainGroups1($data, $params, $mysqli) {
  $res = [];
  if (!isset($_GET["tn"])) {
    $table = "itemsTableView";
  } else {
    $table = $_GET["tn"];
  }

  $sql = "select ifnull(GROUP_CONCAT(distinct(`MainGroup`)),'') as MainGroup from `" . $table . "`";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
    $res[] = ["MainGroup" => "Diamonds"];
  }
  return $res;
}

function getCollections($data, $params, $mysqli) {
  $res = [];
  $sql = "select name,items from collections  order by name";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function getWarehouses($data, $params, $mysqli) {
  $res = [];
  if (!isset($_GET["tn"])) {
    $table = "itemsTableView";
  } else {
    $table = $_GET["tn"];
  }
  $sql = "select distinct(Warehouse) from `$table`  where IsShowroom='1' order by Warehouse";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}

function getBrands($data, $params, $mysqli) {
  $res = [];
  if (!isset($_GET["tn"])) {
    $table = "products_search";
  } else {
    $table = $_GET["tn"];
  }
  $sql = "select distinct(BrandID) from `$table`  where BrandID<>'' order by BrandID";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}


function getCuts($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(CutID) from " . ((isset($data["table"])) ? $data["table"] : "products ") . "   where CutID<>'' order by CutID";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }

  return $res;
}
function getColorDesc($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(ColorDesc) from " . ((isset($data["table"])) ? $data["table"] : "products_serial ") . "   where ColorDesc<>'' order by ColorDesc";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function getDiamondsLocations($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(WarehouseID),Warehouse from " . ((isset($data["table"])) ? $data["table"] : "diamonds ") . "   where Warehouse<>'' order by Warehouse";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}

function getTypes($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(TypeID) from " . ((isset($data["table"])) ? $data["table"] : "products ") . "  where TypeID<>'' order by TypeID";

//  $sql = "select distinct(TypeID) from products_search  where TypeID<>'' order by TypeID";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function getColorsGrouped($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(ColourID) from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "  where ColourID<>'' order by ColourID";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[$row["ColourID"]] = [];
      $ss = "select distinct(ColourID_1) as CID from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "
        where ColourID='" . $row["ColourID"] . "' and ColourID_1<>'' order by ColourID_1";

      $rr = $mysqli->query($ss);
      while ($rrr = mysqli_fetch_assoc($rr)) {
        $res[$row["ColourID"]][] = $rrr["CID"];
      }

    }
  }
  return $res;
}
function getColors($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(ColourID) from " . ((isset($data["table"])) ? $data["table"] : "products ") . "  where ColourID<>'' order by ColourID";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function geterrors($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select * from errors order by date desc limit 500";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res["data"][] = $row;
    }
  }
  return $res;
}
function getClarity($data, $params, $mysqli) {
  $res = [];
  if (isset($_GET["tn"])) {
    $data["table"] = $_GET["tn"];
  }
  $sql = "select distinct(ClarityID) from " . ((isset($data["table"])) ? $data["table"] : "products ") . "  where ClarityID<>'' order by ClarityID";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $row["ClarityID"] = db_utf8_convert($row["ClarityID"]);
      $res[] = $row;
    }
  }
  return $res;
}
function db_utf8_convert($str) {
   $convmap = array(0x80, 0x10ffff, 0, 0xffffff);
   return preg_replace('/\x{EF}\x{BF}\x{BD}/u', '', mb_encode_numericentity($str, $convmap, "UTF-8"));
}
function makeProducts($data, $params, $mysqli) {

  $sql = "TRUNCATE products";
  $mysqli->query($sql);
  $sql = "select * from itemsTableView";
  $r =  $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $s1 = "select productName,ItemID from products where itemID='" . $row["ItemID"] . "'";
    $prd = $mysqli->query($s1);
    if (mysqli_num_rows($prd) == 0) {
      $isq = "insert into products select * from itemsTableView where (id='" . $row["id"] . "')";
      $mysqli->query($isq);
    } else {
       $rr = mysqli_fetch_assoc($prd);

       if (strpos($rr["productName"], $row["productName"]) === FALSE) {
        $usq = "update products set productName=CONCAT(productName,'<br />','" . $row["productName"] . "') where itemid='" . $row["ItemID"] . "'";
        $mysqli->query($usq);
      }
    }
  }
}
function setCollections($data, $params, $mysqli) {
  $sql = "TRUNCATE collections";
  $mysqli->query($sql);
  $sql = "SELECT Warehouse,group_concat(ItemID) as items FROM itemsTableView where isShowroom='0' group by warehouseid ,warehouse";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $ss = "insert into collections (name,items) values ('" . $row["Warehouse"] . "','" . $row["items"] . "')";
    var_dump($ss);
    $mysqli->query($ss);
  }

}
function createTours($data, $params, $mysqli) {
  $sql = "select * from projects";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $ss = "insert into tours (`ProjId`, `ProjName`, `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`,
    `HotelID`, `PAX`, `CountryID`, `email`, `touroperater`, `wholesaler`, `country`, `language`)
     VALUES ('$ProjId', '$ProjName', '$AVisitDateTime', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID', '$WholesalerRefNo', '$TourleaderID', '$GuideID',
     '$HotelID', '$PAX', '$CountryID', '$email', '$touroperater', '$wholesaler', '$country', '$language')";
     $mysqli->query($ss);
  }
}
function getSPTours($data, $params, $mysqli) {
  $res = [];
//  $sql = "select IFNULL(`PrivateID`,'') as PrivateID, `ProjId`, `ProjName`, `Email`, `AVisitDateTime`, `TouroperatorID`,
//   `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `country`, `language`
//   from tours where DATE_FORMAT(`AVisitDateTime`, '%Y-%m-%d')=CURDATE() order by AVisitDateTime DESC";

   $sql = "select IFNULL(`PrivateID`,'') as PrivateID, `ProjId`, `ProjName`, `Email`, `AVisitDateTime`, `TouroperatorID`,
    `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `country`, `language`
    from tours order by AVisitDateTime DESC limit 50";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function getPMethods($data, $params, $mysqli) {
  $res = [];
  $sql = "select * from paymentMethods  order by Payment";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }
  }
  return $res;
}
function gp($data, $params, $mysqli) {
  $sql = "TRUNCATE products";
  $mysqli->query($sql);
  $sql = "select * from itemsTableView where isshowroom='1' or MainGroupRecID='5637144578'";
  $r =  $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $s1 = "select SerialNo, Warehouse, productName,ItemID,ColorDesc from products where itemID='" . $row["ItemID"] . "' and SerialNo='" . $row["SerialNo"] . "'";
    $prd = $mysqli->query($s1);
    if (mysqli_num_rows($prd) == 0) {
      $isq = "insert into products select * from itemsTableView where (id='" . $row["id"] . "') LIMIT 1";
      var_dump($isq);
      $mysqli->query($isq);
    } else {
       $rr = mysqli_fetch_assoc($prd);
       $usq = "update products set productName=CONCAT(productName,'<br />','" . $row["productName"] . "') where (itemid='" . $row["ItemID"] . "' and SerialNo='" . $row["SerialNo"] . "')";
       $mysqli->query($usq);
       if (strpos($rr["Warehouse"], $row["Warehouse"]) === FALSE) {
         $usq = "update products set Warehouse=CONCAT(Warehouse,',','" . $row["Warehouse"] . "') where (itemid='" . $row["ItemID"] . "')";
         $mysqli->query($usq);
       }
       if (strpos($rr["SerialNo"], $row["SerialNo"]) === FALSE) {
         $usq = "update products set SerialNo=CONCAT(SerialNo,',','" . $row["SerialNo"] . "') where (itemid='" . $row["ItemID"] . "')";
         $mysqli->query($usq);
       }
    }
  }
}
function getDiamonds($data, $params, $mysqli) {
  $res["data"] = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

    $sql = "select SerialNo, ItemID, WarehouseID, productName, TotalWeight, ColourID, ClarityID, CutID, SalesPrice, OnhandQnt as Qty from diamonds";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res["data"][] = $row;

    }
  }
  return $res;
}
function setProducts($data, $params, $mysqli) {
  $sql = "TRUNCATE itemsTableView";
  $mysqli->query($sql);
  $sql = "insert into itemsTableView (`SerialNo`, `ItemID`,  `ProdDate`, `SerialTxt`, `RingSize`,
   `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`,
   `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`,
    `MainGroup`, `imageURL`)
    select `SerialNo`, `ItemID`,  `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`,
    `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, `ProductID`,
    `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`,
     `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
      `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL` from items_table
       ON DUPLICATE KEY UPDATE itemsTableView.itemid=itemsTableView.itemid";
       var_dump($sql);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  $sql = "Update  itemsTableView a
          set
          a.ItemName = IFNULL((select ItemName from items b
          where a.ItemID =b.ItemID),'')";
  $mysqli->query($sql);
  $sql = "Update  itemsTableView a
          set
          a.SerialName = IFNULL((select SerialName from inventSerial b
          where a.SerialNo =b.SerialNo),'')";
  $mysqli->query($sql);



    $sql = "TRUNCATE products";
    $mysqli->query($sql);
    $sql = "insert into products (`SerialNo`, `ItemID`, `ItemName`, `SerialName`,
    `CompName`, `ProdDate`, `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`,
    `WareHouseID`, `ColorId`, `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`,
     `ImageName`, `TotalWeight`, `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`,
      `WarehouseSeq`, `IsShowroom`, `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
       `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`,`CompID`)
       select `SerialNo`, `ItemID`,
       `ItemName`, `SerialName`, jewelCompositions.`CompName`, `ProdDate`, `SerialTxt`, `RingSize`,
       `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`, `BrandID`, jewelCompositions.`ProductID`,
        `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, jewelCompositions.`TotalWeight`,
        jewelCompositions.`ClarityID`, jewelCompositions.`ColourID`, jewelCompositions.`CutID`,
         jewelCompositions.`TypeID`, jewelCompositions.`Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
          `ColorDesc`, `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`, `ItemTypeID`, `SubGroup`, `MainGroup`,
           `imageURL`, `realPrice`,jewelCompositions.`CompID` from itemsTableView
           left join jewelCompositions on itemsTableView.ProductID=jewelCompositions.ProductID
           where MainGroup<>'Diamonds'
           ON DUPLICATE KEY UPDATE products.itemid=products.itemid";
           if (!mysqli_query($mysqli,$sql)) {
             $res["status"] = "fail";
             $res["type"] = "Mysql error";
             $res["title"] = mysqli_error($mysqli);
             $res["sql"] = $sql;
           } else {
             $res["status"] = "ok";
           }

    $sql = "TRUNCATE products_a";
    $mysqli->query($sql);
    $sql = "insert into products_a SELECT `id`, `SerialNo`, `ItemID`, `ItemName`,
     `SerialName`, group_concat(`CompName`,'<br />'), `ProdDate`,
      `SerialTxt`, `RingSize`, `SubLocation`, `OnhandQnt`, `WareHouseID`, `ColorId`,
       `BrandID`, `ProductID`, `ItemGroupID`, `SalesPrice`, `Discount`, `ImageName`, `TotalWeight`,
        `ClarityID`, `ColourID`, `CutID`, `TypeID`, `Qnt`, `Warehouse`, `WarehouseSeq`, `IsShowroom`,
        GROUP_CONCAT(`ColorDesc`), `ItemGroup`, `SubGroupRecID`, `MainGroupRecID`,
        `ItemTypeID`, `SubGroup`, `MainGroup`, `imageURL`, `realPrice`, `CompID` FROM `products`
        group By itemid";
    $mysqli->query($sql);
    $sql = "TRUNCATE products";
    $mysqli->query($sql);
    $sql = "insert into products select * from products_a";
    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {
      $res["status"] = "ok";
    }
    return $res;

}

function getSerials($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select SerialNo,SerialName,ifnull(Warehouse,'') as 'Warehouse',CompName,OnhandQnt,itemName, MainGroup from products_serial where ItemID='$itemid' order by SerialNo";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;

    }
  }
  return $res;
}
function getSerialsSearch($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select SerialNo,SerialName,Warehouse,CompName,OnhandQnt,itemName, MainGroup from search_table where ItemID='$itemid' order by SerialNo";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;

    }
  }
  return $res;
}
function getCountries($data, $params, $mysqli) {
  $res = [];
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select * from countries order by country";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;

    }
  }
  return $res;
}
function getMaterials($data, $params, $mysqli) {
  $res = [];
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_serial";
  } else {
    $table = $_GET["tn"];
  }

  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select distinct colorDesc from `$table` order by colorDesc";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;
    }

  }
  return $res;
}
function getMaterialsGrouped($data, $params, $mysqli) {
  $res = [];

  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_short";
  } else {
    $table = $_GET["tn"];
  }

  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select distinct(ifnull(`colorDesc`,'')) as colorDesc from  `$table`";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
      $ttl = [];
    while ($row = mysqli_fetch_assoc($r)) {
      $cdesc = "";
      $d = $row;

    if ($d["colorDesc"] != "") {
      $cd = explode(",", $d["colorDesc"]);

      foreach ($cd as $cdd) {

        if ($cdd == "925 silver" || $cdd == "950 platinum" || $cdd == "Stainless Steel" || $cdd == "stainless Steel") {
            $cdesc =  $cdd;
            if (!in_array(trim($cdesc), $ttl)) {
                $ttl[] = trim($cdesc);
            }
          } else {
            $cdesc = "";
            $cdsc = explode(" ", $cdd);
            $cdesc = explode(" ", $cdd)[0];
            if (!in_array(trim($cdesc), $ttl)) {
              $ttl[] = trim($cdesc);
            }
            foreach($cdsc as $c) {
             $stra = [];
              for ($s=1;$s<count($cdesc);$s++) {
                $stra[] = ucwords($cdsc[$s]);
              }
              if (strpos($cdd, "gold")) {
                $cdesc =  implode(" ", $stra);
              } else {
                $cdesc =  implode(" ", $stra);
              }
              if (!in_array(trim($cdesc), $ttl)) {
                  $ttl[] = trim($cdesc);
              }
            }

        }
      }
     }
    }
  }

  $row["MainGroup"] = "All Products";
  $row["colorDesc"] = "18k,14k,Yellow Gold,Rose Gold,White Gold,Orange Gold,Black Gold,925 silver,950 platinum";
  $res[] = $row;
  return $res;
}
function getBrandsGrouped($data, $params, $mysqli) {
  $res = [];
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_serial";
  } else {
    $table = $_GET["tn"];
  }

  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";
  $sql = "select MainGroup,ifnull(GROUP_CONCAT(distinct(`BrandID`)),'') as BrandID from `$table` group by MainGroup";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;

    }
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[] = $row;

    }
    $sql = "select 'disount' as MainGroup,ifnull(GROUP_CONCAT(distinct(`BrandID`)),'') as BrandID from `$table` where discount<>'0%'";
    $r1 = $mysqli->query($sql);
    while ($r = mysqli_fetch_assoc($r1)) {
      $res[] = $r;
    }
    $sql = "select 'jewelry' as MainGroup,ifnull(GROUP_CONCAT(distinct(`BrandID`)),'') as BrandID from `$table`";
    $r1 = $mysqli->query($sql);
    while ($r = mysqli_fetch_assoc($r1)) {
      $res[] = $r;
    }
    $sql = "select 'All Products' as MainGroup,ifnull(GROUP_CONCAT(distinct(`BrandID`)),'') as BrandID from `$table`";
    $r1 = $mysqli->query($sql);
    while ($r = mysqli_fetch_assoc($r1)) {
      $res[] = $r;
    }
  }
  return $res;
}
function getCutsGrouped($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_serial";
  } else {
    $table = $_GET["tn"];
  }

  $sql = "select distinct(CutID) from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "  where CutID<>'' order by CutID";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[$row["CutID"]] = [];
      $ss = "select distinct(CutID_1) as CID from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "
        where CutID='" . $row["CutID"] . "' and CutID_1<>'' order by CutID_1";
      $rr = $mysqli->query($ss);
      while ($rrr = mysqli_fetch_assoc($rr)) {
        $res[$row["CutID"]][] = $rrr["CID"];
      }

    }
  }
  //return $res;
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";



  return $res;

}
function getClarityGrouped($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_serial";
  } else {
    $table = $_GET["tn"];
  }

  $sql = "select distinct(ClarityID) from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "  where ClarityID<>'' order by ClarityID";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[$row["ClarityID"]] = [];
      $ss = "select distinct(ClarityID_1) as CID from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "
        where ClarityID='" . $row["ClarityID"] . "' and ClarityID_1<>'' order by ClarityID_1";
      $rr = $mysqli->query($ss);
      while ($rrr = mysqli_fetch_assoc($rr)) {
        $res[$row["ClarityID"]][] = $rrr["CID"];
      }

    }
  }
  //return $res;
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";



  return $res;

}
function getTypesGrouped($data, $params, $mysqli) {
  $res = [];
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($_GET["tn"])) {
    $table = "products_serial";
  } else {
    $table = $_GET["tn"];
  }

  $sql = "select distinct(TypeID) from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "  where TypeID<>'' order by TypeID";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res[$row["TypeID"]] = [];
      $ss = "select distinct(TypeID_1) as CID from " . ((isset($data["table"])) ? $data["table"] : "products_short ") . "
        where TypeID='" . $row["TypeID"] . "' and TypeID_1<>'' order by TypeID_1";
      $rr = $mysqli->query($ss);
      while ($rrr = mysqli_fetch_assoc($rr)) {
        $res[$row["TypeID"]][] = $rrr["CID"];
      }

    }
  }
  //return $res;
  //$sql = "select SerialNo, CONCAT(ConfigTxt, ' ', Category) AS productName from products  where SerialNo='$serial'";



  return $res;
}
function getItemById($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (!isset($diamonds)) {
    $sql = "select * from products where ItemID='$itemid' limit 1";
  } else {
    $sql = "select * from diamonds where ItemID='$itemid' limit 1";
  }
  $res = [];
  $res = mysqli_fetch_assoc($mysqli->query($sql));
  $res["sql"] = $sql;
  return $res;
}
function getItemBySerial($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select * from search_table where SerialNO='$serialno'";
  $res = [];
  $res = mysqli_fetch_assoc($mysqli->query($sql));
  $res["sql"] = $sql;
  return $res;
}
function getItemByIdSearch($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select * from search_table where ItemID='$itemid'";
  $res = [];
  $res = mysqli_fetch_assoc($mysqli->query($sql));
  $res["sql"] = $sql;
  return $res;
}
function search($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = trim($v);
  }
  $res=[];
  $sql = "select * from search_table where ItemID='" . trim($data["search"]) . "' or  SerialNo='" . trim($data["search"]) . "'";

    if (!mysqli_query($mysqli,$sql)) {
      $res["status"] = "fail";
      $res["type"] = "Mysql error";
      $res["title"] = mysqli_error($mysqli);
      $res["sql"] = $sql;
    } else {

      $r = $mysqli->query($sql);
      while ($row = mysqli_fetch_assoc($r)) {
        $res[] = $row;
      }
    }
    return $res;
}
function generatetours($data, $params,$mysqli) {
  $sql = "TRUNCATE tours";
  $mysqli->query($sql);

  $sql = "select * from privateTours";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $dd = explode(" ",$row["Visitdate"]);
    $tt = $dd[1] . ":00";
    $ddd = explode("-", $dd[0]);
    $tt = $ddd[2] . "-" . $ddd[1] . "-" . $ddd[0] . " " . $tt;
    $ss = "insert into tours (`PrivateID`,`ProjId`, `ProjName`, `AVisitDateTime`,
     `GuideID`, `email`, `PAX`, `CountryID`)
     VALUES ('$PrivateID', '$ProjID', '$PrivateName','$tt', '$CosterGuideID', '$EMail', '$PAX', '$CountryID')";
     $mysqli->query($ss);
  }
  $sql = "select * from projects";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $ss = "insert into tours (`ProjId`, `ProjName`, `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`,
    `HotelID`, `PAX`, `CountryID`, `email`, `touroperater`, `wholesaler`, `country`, `language`)
     VALUES ('$ProjId', '$ProjName', '$AVisitDateTime', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID', '$WholesalerRefNo', '$TourleaderID', '$GuideID',
     '$HotelID', '$PAX', '$CountryID', '$email', '$touroperater', '$wholesaler', '$country', '$language')";
     $mysqli->query($ss);
  }

}
function imagesList($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(imageName) from products where imageName<>'crown.png'";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res[] = $row["imageName"];
  }
  return $res;
}
function myinvoices($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  $sql = "select *,c.customer,tours.touroperater from invoices
  left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', countryCode) as customer from customers) c
    on invoices.customerid=c.customerid
  left join tours on invoices.tourno=tours.ProjId
  where salePersonId='" . $data["salePersonId"] . "' order by invoiceid DESC";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
        $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
        $rsum = $mysqli->query($ssum);
        $rs = mysqli_fetch_assoc($rsum);
        $startingTotal = floatval($rs["startingTotal"]);
        $row["startingTotal"] = $startingTotal;

        $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$row["invoiceid"]."' and version=''";
        $rsum = $mysqli->query($ssum);
        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["initialdue"] = floatval($tp);
        $ssum = "select sum(original) as paid from invoice_payments where  invoiceid='" . $row["invoiceid"] . "'";
        $row["due"] = "";
        $rsum = $mysqli->query($ssum);

        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["currentdue"] = floatval($tp);


        if ($res["initialdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed</span>";
        }

        if ($res["initialdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed, change " . number_format($res["initialdue"] * -1, 2, ',', '.') . "<span>";
        }


        if ($res["initialdue"] > 0 && $res["currentdue"] > 0) {
          $row["due"] .= "<span style='min-width:200px;color:red;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " Current " . number_format($res["currentdue"], 2, '.', ',') . "</span>";
        }

        if ($res["initialdue"] > 0 && $res["currentdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:black;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed</span> ";
        }
        if ($res["initialdue"] > 0 && $res["currentdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed, change " .  number_format($res["currentdue"] * -1, 2, '.', ','). "</span>";
        }
        $row["due"] .= "<realvalue realvalue='" . floatval($row["dueAmount"]) . "'></realvalue>";
        $row["due"] .= "<br />€ " . number_format(floatval($row["dueAmount"]), 2, ',', '.');
      /*  if ($res["initialdue"] > 0 && ($res["initialdue"] == $res["currentdue"])) {
          $row["due"] .= "<span style='min-width:200px;color:red' onclick='showPayments(this);'>&nbsp;Due " . number_format($res["initialdue"], 2, '.', ','). "</span>";
        }*/
        if ((floatval($startingTotal) - floatval($row["dueAmount"])) > 0) {
          $row["hasDicount"] = 1;
          $raz = floatval($startingTotal) - floatval($row["dueAmount"]);
          $row["discount"] =  "(" .   number_format(floor((floatval($raz) / floatval($row["startingTotal"]) * 100)), 0, '.', ',') . "%)<br />
          <realvalue realvalue='" .(floatval($startingTotal) - floatval($row["dueAmount"])) . "'>€ " . number_format(floatval($startingTotal) - floatval($row["dueAmount"]), 2, ',', '.') . "</realvalue>";
        } else {
          $row["discount"] = "";
          $row["discount"] = "<realvalue style='display:none;' realvalue='0'></realvalue>";
          $row["hasDicount"] = 0;
        }
        $ss = "select DATE(date) as date,original as paid,payment, version from invoice_payments
                inner join paymentMethods on invoice_payments.paymentID=paymentMethods.PaymentID
                 where  invoiceid='" .$row["invoiceid"]."'
                ";

        $r1 = $mysqli->query($ss);
        $row["due"] .= "<div payments style='display:none;min-width:100%;text-align:left;'>";
        while ($rr = mysqli_fetch_assoc($r1)) {

          if ($rr["paid"] > 0) {
            if ($rr["version"] == "") {
              $rr["version"] = "&nbsp;";
            }
            $row["due"] .= "<pay>" . $rr["version"] . "|" . $rr["date"] . "|" . $rr["payment"] . "| € " . number_format($rr["paid"], 2, '.', ',') . "</pay>";
          }

        }
        $row["due"] .= "<invoice>" . $row["invoiceid"] . "</invoice>" . "<version>" . $row["version"] . "</version><pdf>" . $row["pdf"] . "</pdf>";

        $row["due"] .= "</div>";
        $row["currentdue"] = $res["currentdue"];
      $res["data"][] = $row;
    }


  return $res;
}
function oneinvoice($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  $sql = "select *,c.customer,tours.touroperater from invoices
  left  join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', countryCode) as customer from customers) c
    on invoices.customerid=c.customerid
  left join tours on invoices.tourno=tours.ProjId
  where invoiceid='" . $data["invoiceid"] . "'";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $ss = "select sum(original) as paid from invoice_payments where invoiceid='" . $row["invoiceid"] . "'";
    $r1 = $mysqli->query($ss);
    $row["due"] = $row["dueAmount"] - mysqli_fetch_assoc($r1)["paid"];
    $res[] = $row;
  }

  return $res[0];
}
function allinvoices($data, $params, $mysqli) {
  setlocale(LC_MONETARY, 'nl_NL');
  $res = [];
  $res["data"] = [];
  if (!isset($data["salePersonId"])) {
    $sql = "select *,c.customer,tours.touroperater from invoices
    left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', IFNULL(countryCode,'')) as customer from customers) c
      on invoices.customerid=c.customerid
    left join tours on invoices.tourno=tours.ProjId   order by date desc ";
  } else {
    $sql = "select *,c.customer,tours.touroperater from invoices
    left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', IFNULL(countryCode,'')) as customer from customers) c
      on invoices.customerid=c.customerid
    left join tours on invoices.tourno=tours.ProjId  where salePersonId='" . $data["salePersonId"] . "' order by date desc ";
  }
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
        $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
        $rsum = $mysqli->query($ssum);
        $rs = mysqli_fetch_assoc($rsum);
        $startingTotal = floatval($rs["startingTotal"]);
        $row["startingTotal"] = $startingTotal;

        $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$row["invoiceid"]."' and version=''";
        $rsum = $mysqli->query($ssum);
        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["initialdue"] = floatval($tp);


        $ssum = "select sum(original) as paid from invoice_payments where  invoiceid='" . $row["invoiceid"] . "'";
        $row["due"] = "";
        $rsum = $mysqli->query($ssum);

        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["currentdue"] = floatval($tp);


        if ($res["initialdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed</span>";
        }

        if ($res["initialdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed, change " . number_format($res["initialdue"] * -1, 2, ',', '.') . "<span>";
        }


        if ($res["initialdue"] > 0 && $res["currentdue"] > 0) {
          $row["due"] .= "<span style='min-width:200px;color:red;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " Current " . number_format($res["currentdue"], 2, '.', ',') . "</span>";
        }

        if ($res["initialdue"] > 0 && $res["currentdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:black;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed</span> ";
        }
        if ($res["initialdue"] > 0 && $res["currentdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed, change " .  number_format($res["currentdue"] * -1, 2, '.', ','). "</span>";
        }
        $row["due"] .= "<realvalue realvalue='" . floatval($row["dueAmount"]) . "'></realvalue>";
        $row["due"] .= "<br />€ " . number_format(floatval($row["dueAmount"]), 2, ',', '.');
      /*  if ($res["initialdue"] > 0 && ($res["initialdue"] == $res["currentdue"])) {
          $row["due"] .= "<span style='min-width:200px;color:red' onclick='showPayments(this);'>&nbsp;Due " . number_format($res["initialdue"], 2, '.', ','). "</span>";
        }*/
        if ((floatval($startingTotal) - floatval($row["dueAmount"])) > 0) {
          $row["hasDicount"] = 1;
          $raz = floatval($startingTotal) - floatval($row["dueAmount"]);
          $row["discount"] =  "(" .   number_format(floor((floatval($raz) / floatval($row["startingTotal"]) * 100)), 0, '.', ',') . "%)<br />
          <realvalue realvalue='" .(floatval($startingTotal) - floatval($row["dueAmount"])) . "'>€ " . number_format(floatval($startingTotal) - floatval($row["dueAmount"]), 2, ',', '.') . "</realvalue>";
        } else {
          $row["discount"] = "";
          $row["discount"] = "<realvalue style='display:none;' realvalue='0'></realvalue>";
          $row["hasDicount"] = 0;
        }
        $ss = "select DATE(date) as date,original as paid,payment, version from invoice_payments
                inner join paymentMethods on invoice_payments.paymentID=paymentMethods.PaymentID
                 where  invoiceid='" .$row["invoiceid"]."'
                ";

        $r1 = $mysqli->query($ss);
        $row["due"] .= "<div payments style='display:none;min-width:100%;text-align:left;'>";
        while ($rr = mysqli_fetch_assoc($r1)) {

          if ($rr["paid"] > 0) {
            if ($rr["version"] == "") {
              $rr["version"] = "&nbsp;";
            }
            $row["due"] .= "<pay>" . $rr["version"] . "|" . $rr["date"] . "|" . $rr["payment"] . "| € " . number_format($rr["paid"], 2, '.', ',') . "</pay>";
          }

        }
        $row["due"] .= "<invoice>" . $row["invoiceid"] . "</invoice>" . "<version>" . $row["version"] . "</version><pdf>" . $row["pdf"] . "</pdf>";

        $row["due"] .= "</div>";
        $row["currentdue"] = $res["currentdue"];
      $res["data"][] = $row;
    }


  return $res;
}

function oneInvoicePayments($data, $params, $mysqli) {
  setlocale(LC_MONETARY, 'nl_NL');
  $res = [];
  $res["data"] = [];
  if (!isset($data["invoiceid"])) {
    $sql = "select *,c.customer,tours.touroperater from invoices
    left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', IFNULL(countryCode,'')) as customer from customers) c
      on invoices.customerid=c.customerid
    left join tours on invoices.tourno=tours.ProjId   order by date desc ";
  } else {
    $sql = "select *,c.customer,tours.touroperater from invoices
    left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', IFNULL(countryCode,'')) as customer from customers) c
      on invoices.customerid=c.customerid
    left join tours on invoices.tourno=tours.ProjId  where invoiceid='" . $data["invoiceid"] . "' order by date desc ";
  }
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
        $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
        $rsum = $mysqli->query($ssum);
        $rs = mysqli_fetch_assoc($rsum);
        $startingTotal = floatval($rs["startingTotal"]);
        $row["startingTotal"] = $startingTotal;

        $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$row["invoiceid"]."' and version=''";
        $rsum = $mysqli->query($ssum);
        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["initialdue"] = floatval($tp);
        $ssum = "select sum(original) as paid from invoice_payments where  invoiceid='" . $row["invoiceid"] . "'";
        $row["due"] = "";
        $rsum = $mysqli->query($ssum);

        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["currentdue"] = floatval($tp);


        if ($res["initialdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed</span>";
        }

        if ($res["initialdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed, change " . number_format($res["initialdue"] * -1, 2, ',', '.') . "<span>";
        }


        if ($res["initialdue"] > 0 && $res["currentdue"] > 0) {
          $row["due"] .= "<span style='min-width:200px;color:red;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " Current " . number_format($res["currentdue"], 2, '.', ',') . "</span>";
        }

        if ($res["initialdue"] > 0 && $res["currentdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:black;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed</span> ";
        }
        if ($res["initialdue"] > 0 && $res["currentdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed, change " .  number_format($res["currentdue"] * -1, 2, '.', ','). "</span>";
        }
        $row["due"] .= "<realvalue realvalue='" . floatval($row["dueAmount"]) . "'></realvalue>";
        $row["due"] .= "<br />€ " . number_format(floatval($row["dueAmount"]), 2, ',', '.');
      /*  if ($res["initialdue"] > 0 && ($res["initialdue"] == $res["currentdue"])) {
          $row["due"] .= "<span style='min-width:200px;color:red' onclick='showPayments(this);'>&nbsp;Due " . number_format($res["initialdue"], 2, '.', ','). "</span>";
        }*/
        if ((floatval($startingTotal) - floatval($row["dueAmount"])) > 0) {
          $row["hasDicount"] = 1;
          $raz = floatval($startingTotal) - floatval($row["dueAmount"]);
          $row["discount"] =  "(" .   number_format(floor((floatval($raz) / floatval($row["startingTotal"]) * 100)), 0, '.', ',') . "%)<br />
          <realvalue realvalue='" .(floatval($startingTotal) - floatval($row["dueAmount"])) . "'>€ " . number_format(floatval($startingTotal) - floatval($row["dueAmount"]), 2, ',', '.') . "</realvalue>";
        } else {
          $row["discount"] = "";
          $row["discount"] = "<realvalue style='display:none;' realvalue='0'></realvalue>";
          $row["hasDicount"] = 0;
        }
        $ss = "select DATE(date) as date,original as paid,payment, version from invoice_payments
                inner join paymentMethods on invoice_payments.paymentID=paymentMethods.PaymentID
                 where  invoiceid='" .$row["invoiceid"]."'
                ";

        $r1 = $mysqli->query($ss);
        $row["due"] .= "<div payments style='display:none;min-width:100%;text-align:left;'>";
        while ($rr = mysqli_fetch_assoc($r1)) {

          if ($rr["paid"] > 0) {
            if ($rr["version"] == "") {
              $rr["version"] = "&nbsp;";
            }
            $row["due"] .= "<pay>" . $rr["version"] . "|" . $rr["date"] . "|" . $rr["payment"] . "| € " . number_format($rr["paid"], 2, '.', ',') . "</pay>";
          }

        }
        $row["due"] .= "<invoice>" . $row["invoiceid"] . "</invoice>" . "<version>" . $row["version"] . "</version><pdf>" . $row["pdf"] . "</pdf>";

        $row["due"] .= "</div>";
        $row["currentdue"] = $res["currentdue"];
      $res["data"][] = $row;
    }


  return $res;
}

function searchCustomers($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select customerid as id,concat(ifnull(name,''), ', ', ifnull(email,''), ', ',ifnull(telephone,''), ', ', ifnull(country,''), ', ', ifnull(countryCode,'')) as customer from customers where
   name like('%$query%') or email like('%$query%') or country like('%$query%') or countryCode like('%$query%') or telephone like('%$query%')";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    if ($row["customer"] != NULL &&  $row["id"] != NULL) {
      $res[] = ["id" => $row["id"], "name" => $row["customer"]];
    }
  }

  return $res;
}
function getCustomerById($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select * from customers where customerid='$query'";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res[] = $row;
  }

  return $res;
}
function checkCustomerEmail($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if ($email == "") {
    return $res;
  }
  $sql = "select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', countryCode) as customer from customers where email='$email'";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res[] = $row;
  }

  return $res;
}
function chackAdmin($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select * from admin where username='$username' and password='$password' and active='1'";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res[] = $row;
  }

  return $res;
}
function setInvoiceStatus($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "update invoices set status='$status' where invoiceid='$invoiceid'";
  $r = $mysqli->query($sql);
  $res[] = "ok";
  return $res;
}
function setInvoiceLocked($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "update invoices set locked='$locked' where invoiceid='$invoiceid'";
  $r = $mysqli->query($sql);
  $res[] = "ok";
  return $res;
}
function createlog($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $aa = str_replace("undefined", "", $activity);
  $sql = "INSERT INTO `log` (`emplid`, `name`, `activity`, `deviceid`, `ipaddress`) VALUES ('$emplid','$name','$aa','$deviceid','$ipaddress')";

  $r = $mysqli->query($sql);
  $res[] = "ok";
  return $res;
}
function  createsystemlog($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "INSERT INTO `systemlog` (`name`, `activity`) VALUES ('$name','$activity')";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}

function clog($data, $params, $mysqli) {
  $res = [];
  $id = $_GET["id"];
  $sql = "Select * from `log` where emplid='$id' order by datetime desc limit 1";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach($row as $j => $jj) {
      $$j = $jj;
    }
    $sql = "INSERT INTO `log` (`emplid`, `datetime`,`name`, `activity`, `deviceid`, `ipaddress`) VALUES ('$emplid',NOW(),'$name','Close websocket connection','$deviceid','$ipaddress')";
    $mysqli->query($sql);
  }
  $res[] = $sql;
  return $res;
}
function updateqnt($data, $params, $mysqli) {
  $sql = "TRUNCATE products_qnt";
  $mysqli->query($sql);
  $sql = "insert into products_qnt (Select SerialNo, sum(qnt) as qnt, (CompName) as CompName from products_search  group by SerialNo)";
  $mysqli->query($sql);
  $res = ["ok"];
  $sql = "Update  products_search a
          set
          a.Qnt = IFNULL((select Qnt from products_qnt b
          where a.SerialNo =b.SerialNo),'')";
  $mysqli->query($sql);
  $sql = "Update  products_serial a
          set
          a.Qnt = IFNULL((select Qnt from products_qnt b
          where a.SerialNo =b.SerialNo),'')";

  $mysqli->query($sql);
/*  $sql = "TRUNCATE costerdiamonds.products_search";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.products_search select * from costertemp.products_search";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }*/
  $sql = "TRUNCATE costerdiamonds.products_serial";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.products_serial select * from costertemp.products_serial";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  echo "<br />Doneeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee";
  return $res;
}
function checkIsDiamond($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $sql = "select * from diamonds where itemid='$itemid'";
  $r = $mysqli->query($sql);
  $res["n"] = mysqli_num_rows($r);
  $res["sql"] = $sql;
  return $res;
}
function getItemDescription($data, $params, $mysqli) {
  $res = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  if (isset($_GET["itemid"])) {
    $itemid = $_GET["itemid"];
  }
  $sql = "select * from itemsTableView where itemid='$itemid'";
  $r = $mysqli->query($sql);
  $qnt = 0;
  $weight = 0;
  $available = 0;
  $desc = "";
  $desc ="<div id='composition'>";
  $i = 0;
  $srn = "";
  while ($row = mysqli_fetch_assoc($r)) {
    if ($i == 0) {
      $srn = $row["SerialNo"];
      $i++;
    }
    if ($row["SerialNo"] == $srn) {
          $qnt += $row["Qnt"];
          $available += $row["OnhandQnt"];
          $weight += $row["TotalWeight"];
          if ($row["Qnt"] > 1) {
            $s = "stones";
          } else {
            $s = "stone";
          }
          if ($row["Qnt"] != "0") {
            $desc .= "<b>" . $row["Qnt"] . " " . $s . " " . round($row["TotalWeight"], 2) . " crt</b> ";
          } else {
            $desc .= round($row["TotalWeight"], 2) . " crt</b> ";
          }
          $desc .=  $row["TypeID"] . " ";
          if ($row["ColourID"] != "") {
            $desc .= "color: " . $row["ColourID"] . " ";
          }
          if ($row["ClarityID"] != "") {
            $desc .= "clarity: " . $row["ClarityID"] . " ";
          }
          if ($row["CutID"] != "") {
            $desc .= "cut: " . $row["CutID"] . " ";
          }
          $desc .=  "<br />";
      }
  }
  $desc .= "</div>";
  $res["available"] = $available;
  $res["qnt"] = $qnt;
  $res["weight"] = round($weight, 2);
  $res["description"] = $desc;
  return $res;
}
function insertWebState($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $sql = "insert into web_states (`stateid`, `state`, `text`, `sqlstring`, `view_table`) values ('$stateid','$state','$text','$sqlstring','$name')";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
    return $res;
  } else {
    $res["status"] = "ok";
  }
  $sql = "CREATE VIEW `" . $name . "` AS " .  base64_decode($sqlstring);
  $sql = str_replace("distinct(`itemid`)", " * ", $sql);
  $sql = str_replace("LIMIT 0, 12", "", $sql);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function getWebState($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $sql = "select * from web_states where `stateid`='$stateid'";

  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res["state"] = $row["state"];
    $res["sqlstring"] = $row["sqlstring"];
  }
  return $res;
}
function createTempTable($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $sql = "CREATE VIEW `" . $name . "` AS " .  $sqlstring;
  $sql = str_replace("distinct(`itemid`)", " * ", $sql);
  $sql = str_replace("LIMIT 0, 12", "", $sql);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";

  }
  return $res;

}
function checkPin($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $sql = "select * from web_states where `pin`='$pin' limit 1";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);

  if ($r->num_rows == 0) {
    $res["status"] = "fail";
  } else {
    $res["data"] = mysqli_fetch_assoc($r);
    $res["status"] = "ok";
  }
  return $res;
}
function getInvoiceItems($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $res["data"] = [];
  $sql = "select * from invoice_body where `invoiceid`='$invoiceid'";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);

  if ($r->num_rows == 0) {
    $res["status"] = "fail";
  } else {
    while ($row = mysqli_fetch_assoc($r)) {
      $res["data"][] = $row;
    }
    $res["status"] = "ok";
  }
  return $res;
}
function getItemImage($data, $params, $mysqli) {
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $res = [];
  $res["data"] = [];
  $sql = "select ImageName from itemsTableView where `SerialNo`='$SerialNo'";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    if ($r->num_rows == 0) {
      $res["status"] = "no records";
    } else {
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;
      }
      $res["status"] = "ok";
    }

  }

  return $res;
}
function getInvoicePayments($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sql = "select * from `invoice_payments` where invoiceid='$invoiceid'";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    if ($r->num_rows == 0) {
      $res["status"] = "no records";
    } else {
      while ($row = mysqli_fetch_assoc($r)) {
        $res["data"][] = $row;
      }
      $res["status"] = "ok";
    }
  }
  return $res;

}
function getActualSalesTable($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sql = "SELECT `id`, `TransDate`, `ProjID`, `ExtInvoiceNo`, `PrivateRegNo`, `SalesCountryName`,
   `MainGroup`, `SalesPerson`, `Brand`, `Showroom`, `Turnover`, `Discount`,
    IFNULL(`name`,'') as 'name',
    IFNULL(`address1`,'') as 'address1',
    IFNULL(`address2`,'') as 'address2',
    IFNULL(`city`,'') as 'city',
    IFNULL(`zip`,'') as 'zip',
    IFNULL(`telephone`,'') as 'telephone',
    IFNULL(`ProjName`,'') as 'ProjName',
    IFNULL(`Email`,'') as 'Email',
      IFNULL(`inquery`,'') as 'inquery',
        IFNULL(`Nationality`,'') as 'Nationality',
    `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`,
   `EUMember`, `touroperater`, `wholesaler`, `country`, `language`, `type` FROM `ActualSales` order by TransDate desc limit 200";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);

  if ($r->num_rows == 0) {
    $res["status"] = "fail";
  } else {
    while ($row = mysqli_fetch_assoc($r)) {
      $rr = [];
      foreach (array_keys($row) as $ww) {
        $$ww = $row[$ww];
      }
      if ($type == "0") {
        $color = "#06e411";
      }
      if ($type == "1") {
        $color = "#d2b55b";
      }
      if ($type == "3") {
        $color = "#ff0000";
      }
      $rr["TransDate"] = "<div style='color:" . $color . "'>" . $TransDate . "</div>";

      $rr["general"] = "<div style='color:" . $color . "'>No. " . $ExtInvoiceNo . " Sales Person:" . $SalesPerson . "<br />";
      $rr["general"] .= "Sale country: " . $SalesCountryName . " Showroom: " . $Showroom . "<br />";
      $rr["general"] .= "Main Group: " . $MainGroup . " Brand: " . $Brand . "</br>";
      $rr["general"] .= "Turnover: €" . number_format($Turnover, 2, '.', ',') . " Discount: €" . number_format($Discount, 2, '.', ',') . "</br>";
    //  $rr['TransDate'] = $TransDate;

      $rr["Customer"] = "<div style='color:" . $color . "'>Name. " . $name .  "<br />";
      $rr["Customer"] .= "Email: " . $Email . "<br />";
      $rr["Customer"] .= "Country " . $country . "<br />";
      $rr["Customer"] .= "Zip: " . $zip . " City: " . $city . "<br />";
      $rr["Customer"] .= "Address: " . $address1 . " Phone: " . $telephone . "</div>";

      $rr["ProjectDetails"] = "<div style='color:" . $color . "'>No. " . $ProjID . "<br />";
      $rr["ProjectDetails"] .= "Name:" . $ProjName . "<br />";
      $rr["ProjectDetails"] .= "Wholesaler " . $wholesaler . "<br />";
      $rr["ProjectDetails"] .= "Touropeartor: " . $touroperator . " PAX: " . $PAX . "</div>";

      $rr["PrivateDetails"] = "<div style='color:" . $color . "'>No. " . $PrivateID . "<br />";
      $rr["PrivateDetails"] .= "Country " . $country . "Language " . $language . "<br />";
      $rr["PrivateDetails"] .= "Hotel: " . $HotelID . "</div>";

      $res["data"][] = $rr;
    }
    $res["status"] = "ok";
  }

  return $res;

}
function getInvoiceTour($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }

  $sql = "select * from `tours` where PrivateID='$tourid' OR Projid='$tourid'";
  $res["sql"] = $sql;
  $r = $mysqli->query($sql);

  if ($r->num_rows == 0) {
    $res["status"] = "ok";
  } else {
    while ($row = mysqli_fetch_assoc($r)) {
      $res["data"][] = $row;
    }
    $res["status"] = "ok";
  }
  return $res;

}
function ttt($data, $params, $mysqli) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
  $to = "cobol1962@gmail.com";
  $separator = md5(time());
    $eol = "\r\n";
  $rabdom_hash = $separator;

  $attachement = chunk_split(base64_encode(file_get_contents("https://costercatalog.com/api/invoices/" . "SalesInvoice_20200918_901075_gb.pdf")));

  $headers = "";
  $subject = "Booked invoice";
  $headers .= "Reply-To: <invoice@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
  $headers .= "Return-Path: <invoice@costercatalog.com>\r\n";
  $headers .= 'From: invoice@costercatalog.com' . "\r\n";
  $headers .= "MIME-Version: 1.0" . $eol;
  $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
  $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
  $headers .= "This is a MIME encoded message." . $eol;


  // message
  $htmlContent = '
    <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
     <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
    <span style="font-size:30px;margin-top:20px;font-weight:bold;">Thank you for your order, ' . "JA" . '.</span>
    <br /> <span style="font-size:20px;">Please find your invoice attached.</span>
   ';

$htmlContent .= '<div style="padding:15px;width:600px;text-align:center;"><img style="width:200px;" src="https://costercatalog.com/costerdemo/coster/www/images/bagsmall.png" /></div>';


  $htmlContent .= "<p style='color:green !important;text-align:center;font-size:17px;color:#646464;'>You already paid for this order on " . " sada " . " invoice number " . " tada " . "</p>";
  $htmlContent .= '<p style="text-align:center;font-size:17px;color:#646464;">This email contains your invoice for your recent purchase at Royal Coster Diamonds.
   Please add this email address to your contact list to avoid delivery in your spam folder in the future.</p>';

    $htmlContent .= "<p style='text-align:center;font-size:17px;color:#646464;'><b>Please contact us for any question you might have.</b></p>";
    $htmlContent .= "<br /><i><span style='text-align:center;font-size:17px;color:#646464;'>Call: +310 (20) 3055 555</span>";
    $htmlContent .= "<br /><span style='text-align:center;font-size:17px;color:#646464;'>© 2020 Royal Coster Diamonds BV. All rights reserved.</span>";
    $htmlContent .= "<br /><span style='text-align:center;font-size:17px;color:#646464;'>Paulus Potterstraat 2, 1071 CZ  Amsterdam |  The Netherlands.</span></i>";

  $htmlContent .= '</body></html>';
  $body = "";


  $body .= "--".$separator.$eol;
  $body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
  $body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;//optional defaults to 7bit
  $body .= $htmlContent.$eol;

  // attachment
  $body .= "--".$separator.$eol;
  $body .= "Content-Type: application/octet-stream; name=\""."Nenad.pdf"."\"".$eol;
  $body .= "Content-Transfer-Encoding: base64".$eol;
  $body .= "Content-Disposition: attachment".$eol.$eol;
  $body .= $attachement.$eol;
  $body .= "--".$separator."--";
  mail($to, $subject, $body, $headers);
  return ["ok"];
}
function sales($data, $params, $mysqli) {

   $sql = "TRUNCATE protectedItems";
   $mysqli->query($sql);
   $sql = "insert into protectedItems (`date`, `invoiceNumber`, `serialNo`, `ExtInvoiceNo`)
   SELECT soldSerials.`date`, soldSerials.`invoiceNumber`, `soldSerials`.`serialNo`, actcSales.`ExtInvoiceNo`
 FROM soldSerials inner JOIN actcSales on soldSerials.invoiceNumber=actcSales.invoiceNumber
   where SUBSTRING(actcSales.ExtInvoiceNo, 1, 1)='6'";
  $mysqli->query($sql);
  $sql = "TRUNCATE costerdiamonds.protectedItems";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.protectedItems select * from costertemp.protectedItems";
  $mysqli->query($sql);

  $sql = "TRUNCATE actcTours";
  $mysqli->query($sql);

  $sql = "select *, concat(`FirstName`, ' ', `LastName`) as name from actcPrivates";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $dd = explode(" ",$row["VisitDate"]);
    $tt = $dd[1] . ":00";
    $ddd = explode("-", $dd[0]);
    $tt = $ddd[2] . "-" . $ddd[1] . "-" . $ddd[0] . " " . $tt;

    $ss = "insert into actcTours (`Ref`,`PrivateID`,`ProjId`, `ProjName`, `AVisitDateTime`,
     `GuideID`, `email`, `PAX`, `CountryID`,`HotelID`)
     VALUES ('$PrivateID','$PrivateID', '', '$name','$tt', '', '$EMail', '0', '$CountryID','$HotelName')";
     $mysqli->query($ss);
  }
  $sql = "select * from actcGroups";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach ($row as $k => $v) {
      $$k = $v;
    }
    $ss = "insert into actcTours (`Ref`,`ProjId`, `ProjName`, `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`, `TourleaderID`, `GuideID`,
    `HotelID`, `PAX`, `CountryID`, `email`, `touroperater`, `wholesaler`, `country`, `language`)
     VALUES ('$ProjId','$ProjId', '$ProjName', '$AVisitDateTime', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID', '$WholesalerRefNo', '$TourleaderID', '$GuideID',
     '', '$PAX', '', '', '$TOName', '$WSName', '', '')";
     $mysqli->query($ss);
  }

  $sql = "Update  actcTours a
          set
          a.country = IFNULL((select Country from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  actcTours a
          set
          a.country = IFNULL((select Country from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  actcTours a
          set
          a.language = IFNULL((select Nationality from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  actcTours a
          set
          a.EUMember = IFNULL((select EUMember from countries b
          where a.CountryID =b.CountryID),'')";
  $mysqli->query($sql);
  $sql = "Update  actcTours a
          set
          a.wholesaler = IFNULL((select VendorName from vendors b
          where a.WholesalerID =b.VendorID),'')";
  $mysqli->query($sql);
  $sql = "Update  actcTours a
          set
          a.touroperater = IFNULL((select VendorName from vendors b
          where a.TouroperatorID =b.VendorID),'')";
  $mysqli->query($sql);

  $sql = "TRUNCATE costerdiamonds.actcTours";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.actcTours select * from costertemp.actcTours";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }


  $sql = "TRUNCATE costertemp.invoices";
  $mysqli->query($sql);
  $sql = "insert into  costertemp.invoices select * from costerdiamonds.invoices";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "TRUNCATE costertemp.customers";
  $mysqli->query($sql);
  $sql = "insert into  costertemp.customers select * from costerdiamonds.customers";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
  $sql = "select `id`, `TransDate`, `ProjID`, `ExtInvoiceNo`, `PrivateRegNo`, `SalesCountryName`,
   GROUP_CONCAT(distinct(`MainGroup`)) AS `MainGroup`, `SalesPerson`,
   GROUP_CONCAT(distinct(`Brand`)) AS `Brand`, `Showroom`, SUM(`Turnover`) as 'Turnover', SUM(`Discount`) AS 'Discount',`invoiceNumber`
    FROM `actcSales`
   where year(TransDate) = year(Now())
    group by `ExtInvoiceNo` order by TransDate Desc";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    var_dump($row);
    foreach (array_keys($row) as $ww) {
      $$ww = $mysqli->real_escape_string($row[$ww]);
    }
    $invoice_number = intval(substr($row["ExtInvoiceNo"],1));
    $q = "select ExtInvoiceNo from ActualSales where (ExtInvoiceNo='$ExtInvoiceNo' or `reference`='$ExtInvoiceNo')";
var_dump($q);
    $qr = $mysqli->query($q);
    var_dump(mysqli_num_rows($qr));
    if (mysqli_num_rows($qr) == 0) {

          $sql1 = "select invoices.*,customers.*,actcTours.*,actcSales.invoiceNumber from invoices inner join
           customers on invoices.customerid=customers.customerid
           left join actcTours on invoices.tourNo=actcTours.Ref
              left join actcSales on invoices.tourNo=actcTours.Ref
          where invoices.invoiceid='$invoice_number' LIMIT 1;
          ";

          $r1 = $mysqli->query($sql1);
          if (mysqli_num_rows($r1) > 0) {
                while ($row1 = mysqli_fetch_assoc($r1)) {
                  foreach (array_keys($row1) as $ww) {
                    $$ww = $mysqli->real_escape_string($row1[$ww]);
                  }
                  $Turnover = $dueAmount;
                  $insert = "insert into ActualSales  (`TransDate`, `salesDate`,`ProjID`, `ExtInvoiceNo`, `reference`,
                  `receipt`, `PrivateRegNo`,
                   `SalesCountryName`, `MainGroup`,
                  `SalesPerson`, `Brand`, `Showroom`, `Turnover`,
                   `Discount`,`name`,`Email`,`Country`,`zip`,`City`,`telephone`,`address1`,`address2`,
                   `ProjName`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`,
                    `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `language`,`type`
                    )
                  VALUES ('$date', '$date','$TourNo', '$ExtInvoiceNo', '$ExtInvoiceNo','$invoiceNumber','$TourNo',
                   '$SalesCountryName', '$MainGroup',
                   '$SalesPerson', '$Brand', '$Showroom', '$Turnover',
                   '$Discount','$name','$email','$country','$zip','$city','$telephone','$address1','$address2',
                   '$ProjName', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID', '$WholesalerRefNo',
                    '$TourleaderID', '$GuideID', '$HotelID', '$PAX', '$CountryID', '$EUMember',
                     '$touroperater', '$wholesaler', '$language','0')";

                   if (!mysqli_query($mysqli,$insert)) {
                   } else {


                    // insertOrUpdateAC($mysqli->insert_id,$params, $mysqli);
                   }
                  //   $to = "cobol1962@gmail.com";
                     $separator = md5(time());
                       $eol = "\r\n";
                     $rabdom_hash = $separator;
          //           $to = str_replace(" ",'', $salesPerson) . "@costerdiamonds.com,robertgroot@costerdiamonds.com,m.van.veenendaal@costerdiamonds.com,keesnoomen@costerdiamonds.com,cobol1962@gmail.com";
                     $to = "cobol1962@gmail.com,robertgroot@costerdiamonds.com";
                     $ch = curl_init();
                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                     curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com/api/invoice.php?invoice=" . $row1["pdf"] . "&secret=12dddgfgffgfgfggfgfgfg");
                     $result=curl_exec($ch);
                     curl_close($ch);
                     $attachement = $result;
                  //   $attachement = chunk_split(base64_encode(file_get_contents("https://costercatalog.com/api/invoices/" . $row1["pdf"])));
                     $headers = "";

                     $subject = "Booked invoice (" . $ExtInvoiceNo . ")";
                     $headers .= "Reply-To: <invoice@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
                     $headers .= "Return-Path: <invoice@costercatalog.com>\r\n";
                     $headers .= 'From: invoice@costercatalog.com' . "\r\n";
                     $headers .= "MIME-Version: 1.0" . $eol;
                     $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
                     $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
                     $headers .= "This is a MIME encoded message." . $eol;

                     // message
                     $htmlContent = '
                       <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
                        <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
                       <span style="font-size:30px;margin-top:24px;font-weight:bold;">Congratulations on your sale, ' . $row["SalesPerson"] . '!</span>
                       <br /> <span style="font-size:20px;">The details of this sale have been added in your name to our sales database. Reference: Invoice number ' . $ExtInvoiceNo .'.</span>
                        <br /> <span style="font-size:20px;">The total of your sales registered is: &euro; ' . number_format($row1["dueAmount"], 2, '.', ','). '</span>
                      ';

                    $htmlContent .= '<br /><br /><br /><div style="padding:15px;width:600px;text-align:center;"><img style="width:200px;" src="https://costercatalog.com/costerdemo/coster/www/images/bagsmall.png" /></div>';


                     $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please find your invoice attached.</p>';
                     $htmlContent .= '<p style="text-align:center;font-size:19px;color:#646464;"><strong>Keep it up!</strong></p>';

                     $htmlContent .= '</body></html>';
                     $body = "";


                     $body .= "--".$separator.$eol;
                     $body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
                     $body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;//optional defaults to 7bit
                     $body .= $htmlContent.$eol;
                     // attachment
                     $body .= "--".$separator.$eol;
                     $body .= "Content-Type: application/octet-stream; name=\"".$row1["pdf"]."\"".$eol;
                     $body .= "Content-Transfer-Encoding: base64".$eol;
                     $body .= "Content-Disposition: attachment".$eol.$eol;
                     $body .= $attachement.$eol;
                     $body .= "--".$separator."--";
                     mail($to, $subject, $body, $headers);



                }
          } else {

              $sql1 = "select * from actcTours where ProjId='$ProjID' limit 1";
              $rz = mysqli_fetch_assoc($mysqli->query($sql1));
              foreach (array_keys($rz) as $ww) {
                $$ww = $mysqli->real_escape_string($rz[$ww]);
              }

            if ($PrivateRegNo != "") {
                $sql1 = "select * from actcPrivates where PrivateID='$PrivateRegNo' limit 1";
                $rz = mysqli_fetch_assoc($mysqli->query($sql1));
                $name =  $mysqli->real_escape_string($rz["FirsName"] . " " . $rz["LastName"]);
                $PAX = $rz["visitors"];
                $Email = $rz["Email"];
                $SalesPerson =  $mysqli->real_escape_string($rz["IntGuideName"]);
                $HotelID =  $mysqli->real_escape_string($rz["HotelName"]);
                $inquery = $rz["inquiry"];
                $Nationality = $rz["Nationality"];
                $type = "1";
            } else {
              $sql1 = "select * from actcGroups where ProjId='$ProjID' limit 1";
              $rz = mysqli_fetch_assoc($mysqli->query($sql1));
              $touroperater =  $mysqli->real_escape_string($rz["TOName"]);

              $wholesaler =  $mysqli->real_escape_string($rz["WSName"]);
            //  $SalesPerson =  $rz["TLName"] . "<br />Internal guide: " . $rz["GDName"];
              $PAX = $rz["PAX"];
              $type = "3";
              $name = "";
            }

              $insert = "insert into ActualSales  (`TransDate`, `salesDate`,`ProjID`, `ExtInvoiceNo`,`receipt`,`reference`, `PrivateRegNo`,
               `SalesCountryName`, `MainGroup`,
              `SalesPerson`, `Brand`, `Showroom`, `Turnover`,
               `Discount`, `ProjName`, `name`,
                 `Email`, `AVisitDateTime`, `TouroperatorID`, `TouroperatorRefNo`, `WholesalerID`, `WholesalerRefNo`,
                `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`, `EUMember`, `touroperater`, `wholesaler`, `country`, `language`,
                `type`,`inquery`, `Nationality` )
              VALUES ('$TransDate', '$TransDate', '$ProjID', '$ExtInvoiceNo', '$invoiceNumber', '$ExtInvoiceNo','$PrivateRegNo',
               '$SalesCountryName', '$MainGroup',
               '$SalesPerson', '$Brand', '$Showroom', '$Turnover',
               '$Discount',
                '$ProjName', '$name', '$Email', '$AVisitDateTime', '$TouroperatorID', '$TouroperatorRefNo', '$WholesalerID',
                 '$WholesalerRefNo', '$TourleaderID', '$GuideID', '$HotelID',
                '$PAX', '$CountryID', '$EUMember', '$touroperater', '$wholesaler', '$country', '$language', '$type', '$inquery','$Nationality')";
               $mysqli->query($insert);
            //   $to = "cobol1962@gmail.com";
            // $to = str_replace(" ",'', $salesPerson) . "@costerdiamonds.com,robertgroot@costerdiamonds.com,m.van.veenendaal@costerdiamonds.com,keesnoomen@costerdiamonds.com,cobol1962@gmail.com";
             $to = "cobol1962@gmail.com,robertgroot@costerdiamonds.com";
             $subject = 'Booked invoice (' . $row["ExtInvoiceNo"] . ")";
             $headers = "From: invoices@costercatalog.com" . "\r\n";
             $headers .= "MIME-Version: 1.0\r\n";
             $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

             $htmlContent = '
               <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
                <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
               <span style="font-size:30px;margin-top:24px;font-weight:bold;">Congratulations on your sale, ' . $row["SalesPerson"] . '!</span>
               <br /> <span style="font-size:20px;">The details of this sale have been added in your name to our sales database.</span>
                <br /> <span style="font-size:20px;">The total of your sales registered is: &euro; ' . number_format($Turnover, 2, '.', ','). '</span>
              ';
              $htmlContent .= "<br /><br /><br /><table style='width:500px;margin-left:100px;'><tbody>";
              $htmlContent .= "<tr><td style='vertical-align:top;'><img src='https://costercatalog.com/catalog/images/redwarningsmall.png' style='width:150px;'</td>";
              $htmlContent .= "<td style='text-align:left;vertical-align:top;'><span style='font-weight:bold;text-align:left;color:red;font-size:20px;'>URGENT<br />You used a yellow invoice!</span><br />";
              $htmlContent .= "<span style='font-weight:bold;text-align:left;color:black;font-size:20px;'>Please copy this info to the salesapp within 2 days.<br />(or you lose a finger ;)</span></td>";
              $htmlContent .= '</tr></tbody></table>';
              $htmlContent .= "<br /><br /><br /><span style='font-size:18px;'>If you need more training, please contact IT department or watch any of the tutorial movies on viadesk.</span>";
              $htmlContent .= '</body></html>';
              mail($to, $subject, $htmlContent, $headers);
          }
      }
  }
  $sql = "select * from ActualSales order by TransDate ASC";
  $rez = $mysqli->query($sql);
  $i = 1;
  while ($row = mysqli_fetch_assoc($rez)) {
    foreach (array_keys($row) as $ww) {
      $$ww = $mysqli->real_escape_string($row[$ww]);
    }
    if ($row["Email"] == "") {

      $sup = "update ActualSales set Email='" . $row["ExtInvoiceNo"] . "@nomail.com' where id='" . $row["id"] . "'";
      $mysqli->query($sup);


  //    $to = str_replace(" ", $row["SalesPerson"], '') . "@costerdiamomds.com";
  //    $to = str_replace(" ", $row["SalesPerson"], '') . "@costerdiamomds.com,robertgroot@costerdiamonds.com,keesnoomen@costerdiamonds.com";
  //    $to = "robertgroot@costerdiamonds.com";

    }
    $i++;
  }
  //convertRedInvoices($data, $params, $mysqli);
  receipts($data, $params, $mysqli);
  updateTN($data, $params, $mysqli);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?activesales");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  echo "<br />Sales done";
  $sql = "TRUNCATE costerdiamonds.ActualSales";
  $mysqli->query($sql);
  $sql = "insert into  costerdiamonds.ActualSales select * from costertemp.ActualSales";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
  }
}
function getInvoicesV($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(customer) from invoices_view order by customer";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["customers"][] = $row;
  }
  $sql = "select distinct(invoiceid) from invoices_view order by invoiceid";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["invoices"][] = $row;
  }
  $sql = "select distinct(tourNo) from invoices_view order by tourNo";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["tourNo"][] = $row;
  }
  $sql = "select distinct(touroperater) from invoices_view order by touroperater";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["touroperater"][] = $row;
  }

  $sql = "select distinct(showroom) from invoices_view order by showroom";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["showroom"][] = $row;
  }

  $sql = "select distinct(salesPerson) from invoices_view order by salesPerson";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["salesperson"][] = $row;
  }

  $sql = "select distinct(discountApprovedName) from invoices_view order by discountApprovedName";
  $rez = $mysqli->query($sql);
  while($row = mysqli_fetch_assoc($rez)) {
    $res["discountapprovedname"][] = $row;
  }

  return $res;
}
function soldItems($data, $params, $mysqli) {
  $sql = "select * from onHandSerial where serialNo NOT IN (select serialNo from onHandSerialNew)";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach (array_keys($row) as $w) {
      $$w = $mysqli->real_escape_string($row[$w]);
    }
    $oo = floatval($onHandQnt) - 1;
    $ss = "insert into onHandSerailNew (`ItemID`, `OnhandQnt`, `WareHouseID`, `SerialNo`, `ColorId`, `ColorDesc`)
    VALUES ('$ItemID', '$oo', '$WareHouseID', '$SerialNo', '$ColorId', '$ColorDesc')";
    $mysqli->query($ss);

  }



/*  $sql = "TRUNCATE onHandSerial";
  $mysqli->query($sql);
  $sql = "insert into onHandSerial select * from onHandSerialNew";
  $mysqli->query($sql);


  $sql = "TRUNCATE costerdiamonds.onHandSerial";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.onHandSerial select * from costertemp.onHandSerial";
  $mysqli->query($sql);


  $sql = "TRUNCATE costerdiamonds.solditems";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.solditems select * from costertemp.solditems";
  $mysqli->query($sql);*/

}
function ohs($data, $params, $mysqli) {
  $sql = "TRUNCATE onHandSerialNew";
  $mysqli->query($sql);
  $sql = "LOAD DATA LOCAL INFILE '/var/www/html/api/csv/inv_OnhandSerial.csv'
  INTO TABLE onHandSerialNew FIELDS TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 ROWS
  (ItemID,OnhandQnt,WareHouseID,SerialNo,ColorId)";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }

  $sql = "select * from onHandSerial where serialNo NOT IN (select serialNo from onHandSerialNew)";

  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    foreach (array_keys($row) as $w) {
      $$w = $mysqli->real_escape_string($row[$w]);
    }
    $ss = "insert into costertemp.onHandSerialNew (`ItemID`, `OnhandQnt`, `WareHouseID`, `SerialNo`, `ColorId`, `ColorDesc`)
    VALUES ('$ItemID', '0', '$WareHouseID', '$SerialNo', '$ColorId', '$ColorDesc')";
    $mysqli->query($ss);

  }



  $sql = "TRUNCATE onHandSerial";
  $mysqli->query($sql);
  $sql = "insert into onHandSerial select * from onHandSerialNew";
  $mysqli->query($sql);


  $sql = "TRUNCATE costerdiamonds.onHandSerial";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.onHandSerial select * from costertemp.onHandSerial";
  $mysqli->query($sql);


  $sql = "TRUNCATE costerdiamonds.solditems";
  $mysqli->query($sql);
  $sql = "insert into costerdiamonds.solditems select * from costertemp.solditems";
  $mysqli->query($sql);
}
function getDiamonsForShopify($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  $sql = "select * from diamonds where CutID='Pear' limit 5";
  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res["data"][] = $row;
  }
  return $res;
}
function getQuantityBySerial($data, $params, $mysqli) {
  $res = [];
  $sql = "select OnhandQnt from onHandSerial where SerialNo='" . $data["serialno"] . "'";

  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res[] = $row;
  }
  return $res;
}
function newShopifyItems($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  $sql = "select * from products where ItemID NOT IN (Select distinct(itemid) from shopify_content)";

  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
    $res["data"][] = $row;
  }
  return $res;
}
function listInvoicesByNumber($data, $params, $mysqli) {
  $res = [];
  foreach (glob("/var/www/html/api/invoices/*" . $data["number"] . "*.pdf") as $filename) {
    $res[] = explode("/", $filename)[6];
  }
  return $res;
}
function uic($data, $params, $mysqli) {
  $res = [];
 $sql = "select CONVERT(SUBSTRING(ExtInvoiceNo, 2), SIGNED) as invoiceid,ExtInvoiceNo,SalesCountryName,name,address1,
  address2,city,zip,Email,telephone,ProjID,PrivateRegNo,HotelID from ActualSalesView where SUBSTRING(ExtInvoiceNo, 1, 1)='9'";

//  $sql = "select CONVERT(SUBSTRING(ExtInvoiceNo, 2), SIGNED) as invoiceid,ExtInvoiceNo,SalesCountryName,name,address1,
//  address2,city,zip,Email,telephone,ProjID,PrivateRegNo,HotelID from ActualSalesView where ExtInvoiceNo='901259'";
  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    foreach (array_keys($r) as $w) {
      $$w = $mysqli->real_escape_string($r[$w]);
    }
    if ($PrivateRegNo == "") {
      $tnm = $ProjID;
    } else {
      $tnm = $PrivateRegNo;
    }
    $ss1 = "INSERT INTO `customers` (`name`, `email`, `country`,  `address1`, `address2`, `city`, `zip`, `hotel`, `TourNo`, `telephone`, `ringsize`)
            Values ('$name', '$Email', '$SalesCountryName', '$address1', '$address2', '$city', '$zip','$HotelID','$tnm', '$telephone','')";
    $mysqli->query($ss1);
    $cid = $mysqli->insert_id;
    $ssu = "update invoices set customerid='$cid' where invoiceid='$invoiceid'";
  $mysqli->query($ssu);
  }
  return $res;
}
function receipts($data, $params, $mysqli) {
  $res = [];
  $sql = "select id, reference from ActualSales";
  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    $ss1 = "select invoiceNumber from actcSales where ExtInvoiceNo='" . $r["reference"] . "'";
    $r1 = mysqli_fetch_assoc($mysqli->query($ss1));
    $ss2 = "update ActualSales set receipt='" . $r1["invoiceNumber"] . "' where id='" . $r["id"] . "'";
    $mysqli->query($ss2);
  }

  return $res;
}
function updateTN($data, $params, $mysqli) {
  $res = [];
  $sql = "select id, ExtInvoiceNo,CONVERT(SUBSTRING(ExtInvoiceNo, 2), SIGNED) as iid from ActualSales";

  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    $ss1 = "select TourNo from invoices where invoiceid='" . $r["iid"] . "'";
    $r1 = $mysqli->query($ss1);

    if ($r1->num_rows > 0) {
      $r2 = mysqli_fetch_assoc($r1);
      $ss2 = "update ActualSales set ProjID='" . $r2["TourNo"] . "',PrivateRegNo='" . $r2["TourNo"] . "' where id='" . $r["id"] . "'";
      $mysqli->query($ss2);
    }
  //  $mysqli->query($ss2);
  }

  return $res;
}
function updateConvertedStatus($data, $params, $mysqli) {
  $res = [];
  $ss1 = "select id,ExtInvoiceNo,reference from ActualSales where ExtInvoiceNo<>reference and reference<>''";
  $rez = $mysqli->query($ss1);
  while ($row = mysqli_fetch_assoc($rez)) {
    $invoice = intval(substr($row["ExtInvoiceNo"],1));
    $uin = "update costerdiamonds.invoices set status='4',locked='2' where invoiceid='$invoice' and status<>'4'";

    $mysqli->query($uin);
    $uin = "update costertemp.invoices set status='4',locked='2' where invoiceid='$invoice' and status<>'4'";
    $mysqli->query($uin);
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?lockedinvoice");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
}
function convertRedInvoices($data, $params, $mysqli) {
  var_dump("covert red");
  $res = [];
  $sql = "select invoices.*, customers.* from invoices
  left join customers on invoices.customerid=customers.customerid
   where reference<>'' and year(date) = year(Now()) and `status`<>'4'";

  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    $invid = intval(substr($r["invoiceid"],1));
    foreach (array_keys($r) as $ww) {
      $$ww = $mysqli->real_escape_string($r[$ww]);
    }
    $ss1 = "select id,ExtInvoiceNo from ActualSales where ExtInvoiceNo='" . $r["reference"] . "'";

    $r1 = $mysqli->query($ss1);
    if ($r1->num_rows > 0) {
      $r2 = mysqli_fetch_assoc($r1);
      $ii = "9" . str_pad("$invoiceid", 5, "0", STR_PAD_LEFT);
      $ss2 = "update ActualSales set
      `reference`='$reference',
      ExtInvoiceNo='$ii',
      type='0',
      `TransDate`='$date',
      `ProjID`='$TourNo',
      `PrivateRegNo`='$TourNo',
      `name`='$name',
      `address1`='$address1',
      `address2`='$address2',
      `city`='$city',
      `zip`='$zip',
      `telephone`='$telephone',
      `Email`='$email',
      `CountryID`='$countryCode',
      `ActiveCampainID`=''
       where id='" . $r2["id"] . "'";

      if (!mysqli_query($mysqli,$ss2)) {
      //  var_dump("FAIL " . $ss2);
      } else {


      //  insertOrUpdateAC($r2["id"], $params, $mysqli);
      }
        $ssi = "update costertemp.invoices set status='4',locked='2' where inoiceid='$invid'";

        $mysqli->query($ssi);
        $ssi = "update costerdiamonds.invoices set status='4','locked='2' where inoiceid='$invid'";
        $mysqli->query($ssi);
          $separator = md5(time());
          $eol = "\r\n";
          $rabdom_hash = $separator;
        //  $to = str_replace(" ",'', $salesPerson) . "@costerdiamonds.com, robertgroot@costerdiamonds.com,m.van.veenendaal@costerdiamonds.com,keesnoomen@costerdiamonds.com,cobol1962@gmail.com";
          $to = "cobol1962@gmail.com,robertgroot@costerdiamonds.com";

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com/api/invoice.php?invoice=" . $pdf . "&secret=12dddgfgffgfgfggfgfgfg");
          $result=curl_exec($ch);
          curl_close($ch);
          $attachement = $result;
      //    $attachement = chunk_split(base64_encode(file_get_contents("https://costercatalog.com/api/invoices/" . $pdf)));
          $headers = "";

          $subject = "Converted invoice (" . $reference . ")";
          $headers .= "Reply-To: <invoice@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
          $headers .= "Return-Path: <invoice@costercatalog.com>\r\n";
          $headers .= 'From: invoice@costercatalog.com' . "\r\n";
          $headers .= "MIME-Version: 1.0" . $eol;
          $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
          $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
          $headers .= "This is a MIME encoded message." . $eol;

          // message
          $htmlContent = '
            <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
             <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
            <span style="font-size:30px;margin-top:24px;font-weight:bold;">Congratulations, ' . $salesPerson . '!</span>
            <br /> <span style="font-size:20px;">Red/yellow invoice ' . $reference . ' succesfully converted to regular invoice. Reference: Invoice number ' . "9" . str_pad("$invoiceid", 5, "0", STR_PAD_LEFT) .'.</span>
            <br /> <span style="font-size:20px;">The total of your sales registered is: &euro; ' . number_format($dueAmount, 2, '.', ','). '</span>

           ';

          $htmlContent .= '<br /><br /><br /><div style="padding:15px;width:600px;text-align:center;"><img style="width:200px;" src="https://costercatalog.com/costerdemo/coster/www/images/bagsmall.png" /></div>';


          $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please find your invoice attached.</p>';
          $htmlContent .= '<p style="text-align:center;font-size:19px;color:#646464;"><strong>Keep it up!</strong></p>';

          $htmlContent .= '</body></html>';
          $body = "";


          $body .= "--".$separator.$eol;
          $body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
          $body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;//optional defaults to 7bit
          $body .= $htmlContent.$eol;

          // attachment
          $body .= "--".$separator.$eol;
          $body .= "Content-Type: application/octet-stream; name=\"".$pdf."\"".$eol;
          $body .= "Content-Transfer-Encoding: base64".$eol;
          $body .= "Content-Disposition: attachment".$eol.$eol;
          $body .= $attachement.$eol;
          $body .= "--".$separator."--";

          mail($to, $subject, $body, $headers);

    }
  //  $mysqli->query($ss2);
  }
/*  $sql = "TRUNCATE costertemp.ActualSales";
  $mysqli->query($sql);
  $sql = "insert into costertemp.ActualSales select * from costerdiamonds.ActualSales";
  $mysqli->query($sql);*/
  updateConvertedStatus($data, $params, $mysqli);
  updateSaleDates($data, $params, $mysqli);
//  insertActualSalesInAC($data, $params, $mysqli);
  return $res;
}
function recentInvoices($data, $params, $mysqli) {
  $res = [];
  $sql = "select invoices.*, customers.* from invoices
  left join customers on invoices.customerid=customers.customerid
  where invoices.locked='1' or invoices.locked='2'
   order by saledate desc limit 10";
  $rez = $mysqli->query($sql);
  $str = "";
  while ($row = mysqli_fetch_assoc($rez)) {
    foreach (array_keys($row) as $ww) {
      $$ww = $mysqli->real_escape_string($row[$ww]);
    }
    $date=date_create($date);
    $dd =  date_format($date,"d.m H:i");
    $nn = explode(" ", trim($name));
    if (count($nn) > 1) {
      $nm = $nn[0] . " " . substr($nn[1],0,1) . ".";
    } else {
      $nm = $nn[0];
    }
    $sp = explode(" ", $salesPerson)[0];
    $str .= '<tr>
    <td class="text-truncate">' . $dd . '</td>
            <td class="text-truncate"><span data-container="body" data-toggle="popover" data-placement="top"
             data-content="' . getTourDetails($data, $params, $mysqli,$tourNo) . '">' . $tourNo . '</span></td>
            <td class="text-truncate"><a onclick="openPDF(' . $invoiceid . ')">' ."9" . str_pad("$invoiceid", 5, "0", STR_PAD_LEFT) . '</a></td>
            <td class="text-truncate">

                <span data-container="body" data-toggle="popover" data-placement="top"
                 data-content="' . getCustomerDetails($data, $params, $mysqli,$row,$name,$email) . '">' . $nm . '</span>
            </td>
          <td class="text-truncate">' . $sp . '</td>
            <td class="text-truncate">' . $dueAmount . '</td>
        </tr>';

  }
  $res[0] = $str;
  return $res;
}
function getTourDetails($data, $params, $mysqli,$tn) {
  $str = "";
  $sql = "select * from tours where ProjID='$tn' or ProjId='$tn'";
  $result = $mysqli->query($sql);
  $rr = [];
  while ($row   = mysqli_fetch_assoc($result)) {
    $rr[0] = $row;
  }
  foreach (array_keys($rr[0]) as $w) {
    $$w = $mysqli->real_escape_string($rr[0][$w]);
  }
  if ($ProjName != "") {
    $str .= "Name: " . $ProjName . "<br />";
  }
  if ($Email != "") {
    $str .= "Email: " . $Email . "<br />";
  }
  if ($GuideID != "") {
    $str .= "Sales Person: " . $GuideID . "<br />";
  }
  if ($touroperater != "") {
    $str .= "Touroperator: " . $touroperater . "<br />";
  }
  if ($country != "") {
    $str .= "Country: " . $country . "<br />";
  }
  if ($language != "") {
    $str .= "Language: " . $language . "<br />";
  }
  return $str;
}
function getCustomerDetails($data, $params, $mysqli,$row,$name,$email) {
  foreach (array_keys($row) as $ww) {
    $$ww = $mysqli->real_escape_string($row[$ww]);
  }
  $str = "";
  if ($name != "") {
    $str .= "Name: " . $name . "<br />";
  }
  if ($email != "") {
    $str .= "Email: " . $email . "<br />";
  }
  if ($address1 != "") {
    $str .= "Address line 1: " . $address1 . "<br />";
  }
  if ($address2 != "") {
    $str .= "Address line 2: " . $address2 . "<br />";
  }
  if ($city != "") {
    $str .= "City: " . $zip . " " . $city . "<br />";
  }
  if ($country != "") {
    $str .= "Country: " . $country . "<br />";
  }
  if ($telephone != "") {
    $str .= "Telephone: " . $telephone . "<br />";
  }
  return $str;
}
function statistic($data, $params, $mysqli) {
  $res = [];
  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE()";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["today"] = $row["amount"];

  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE()-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["yesterday"] = $row["amount"];

  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(`saledate`,1)=yearweek(NOW(),1)";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["week"] = $row["amount"];
  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(`saledate`,1)=yearweek(NOW(),1)-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["weekbefore"] = $row["amount"];


  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and month(date)=month(NOW()) and year(date)=year(NOW())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["month"] = $row["amount"];
  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and month(date)=month(NOW()) - 1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["monthbefore"] = $row["amount"];


  $sql = "select IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(date)=year(NOW())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["amount"]["year"] = $row["amount"];


  $sql = "select count(*) as a from invoices where locked<>0 and DATE(`saledate`)=CURDATE()";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["today"] = $row["a"];
  $sql = "select count(*) as a from invoices where locked<>0 and DATE(`saledate`)=CURDATE()-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["yesterday"] = $row["a"];


  $sql = "select count(*) as a from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1)";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["week"] = $row["a"];
  $sql = "select count(*) as a from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1)-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["weekbefore"] = $row["a"];



  $sql = "select count(*) as a from invoices where locked<>0 and month(DATE(`saledate`))=month(CURDATE())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["month"] = $row["a"];
  $sql = "select count(*) as a from invoices where locked<>0 and month(DATE(`saledate`))=month(CURDATE())-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["monthbefore"] = $row["a"];

  $sql = "select count(*) as a from invoices where locked<>0 and year(DATE(`saledate`))=year(CURDATE())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["customers"]["year"] = $row["a"];

  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and DATE(`saledate`)=CURDATE()";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["today"] = $row["a"];
  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and DATE(`saledate`)=CURDATE()-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["yesterday"] = $row["a"];

  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1)";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["week"] = $row["a"];
  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1)-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["weekbefore"] = $row["a"];

  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and month(DATE(invoices.`saledate`))=month(CURDATE())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["month"] = $row["a"];
  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and month(DATE(invoices.`saledate`))=month(CURDATE())-1";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["monthbefore"] = $row["a"];

  $sql = "select ifnull(sum(quantity),0) as a, invoices.* from invoice_body
  left JOIN invoices on invoices.invoiceid=invoice_body.invoiceid
  where invoices.locked<>0 and year(DATE(invoices.`saledate`))=year(CURDATE())";
  $rez = $mysqli->query($sql);
  $row = $rez->fetch_assoc();
  $res["products"]["year"] = $row["a"];




  return $res;
}
function statisticBySalesPersons($data, $params, $mysqli) {
  $res = [];
  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE() group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["today"][$row["salesPerson"]] = $row["amount"];
  }


  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE()-1 group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["yesterday"][$row["salesPerson"]] = $row["amount"];
  }

  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1) group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["week"][$row["salesPerson"]] = $row["amount"];
  }

  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=(yearweek(CURDATE(),1) - 1) group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["weekbefore"][$row["salesPerson"]] = $row["amount"];
  }

  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0  and year(DATE(`saledate`))=(year(CURDATE())) and month(DATE(`saledate`))=(month(CURDATE())) group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["month"][$row["salesPerson"]] = $row["amount"];
  }

  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE()))  and month(DATE(`saledate`))=(month(CURDATE()) - 1) group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["monthbefore"][$row["salesPerson"]] = $row["amount"];
  }

  $sql = "select salesPerson,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE())) group by salesPerson order by amount desc limit 5";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bysalesp"]["year"][$row["salesPerson"]] = $row["amount"];
  }

  //Tours
  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE() group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["today"][$row["tourNo"]] = $row["amount"];
  }


  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE()-1 group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["yesterday"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["week"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=(yearweek(CURDATE(),1) - 1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["weekbefore"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE())) and month(DATE(`saledate`))=(month(CURDATE())) group  by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["month"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE())) and month(DATE(`saledate`))=(month(CURDATE()) - 1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["monthbefore"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE())) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["year"][$row["tourNo"]] = $row["amount"];
  }
  return $res;
}
function statisticByTours($data, $params, $mysqli) {
  $res = [];

  //Tours
  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE() group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $ss = "select ProjName from tours where PrivateID='" . $row["tourNo"] . "' or ProjId='" . $row["tourNo"] . "'";
    $pn = mysqli_fetch_row($mysqli->query($ss))["ProjName"];

    $res["bytour"]["today"][$row["tourNo"]] = $pn;
  }


  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and DATE(`saledate`)=CURDATE()-1 group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["yesterday"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=yearweek(CURDATE(),1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["week"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and yearweek(DATE(`saledate`),1)=(yearweek(CURDATE(),1) - 1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["weekbefore"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE()))  and month(DATE(`saledate`))=(month(CURDATE())) group  by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["month"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE()))  and month(DATE(`saledate`))=(month(CURDATE()) - 1) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["monthbefore"][$row["tourNo"]] = $row["amount"];
  }

  $sql = "select tourNo,IFNULL(SUM(dueAmount), 0) as amount from invoices where locked<>0 and year(DATE(`saledate`))=(year(CURDATE())) group by tourNo order by amount desc limit 10";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["bytour"]["year"][$row["tourNo"]] = $row["amount"];
  }
  return $res;
}
function updateSaleDates($data, $params, $mysqli) {
  $sql = "select * from invoices where reference<>''";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $s1 = "select TransDate from actcSales where `ExtInvoiceNo`='" . $row["reference"] . "'";
    $sd = "";

    $r1 = $mysqli->query($s1);
    while ($e = mysqli_fetch_assoc($r1)) {
      $sd = $e["TransDate"];
    }

    $su = "update costerdiamonds.invoices set saledate='" . $sd . "' where invoiceid='" . $row["invoiceid"] . "'";
    $mysqli->query($su);
    $su = "update costertemp.invoices set saledate='" . $sd . "' where invoiceid='" . $row["invoiceid"] . "'";
    $mysqli->query($su);
  }
}
function yearVSYear($data, $params, $mysqli) {
  $res = [];
  $res["2019"] = ["0" => "0","1" => "0","2" => "0","3" => "0","4" => "0","5" => "0","6" => "0","7" => "0","8" => "0","9" => "0","10" => "0","11" => "0"];
  $res["2020"] = ["0" => "0","1" => "0","2" => "0","3" => "0","4" => "0","5" => "0","6" => "0","7" => "0","8" => "0","9" => "0","10" => "0","11" => "0"];
  $res["2021"] = ["0" => "0","1" => "0","2" => "0","3" => "0","4" => "0","5" => "0","6" => "0","7" => "0","8" => "0","9" => "0","10" => "0","11" => "0"];

  $sql = "select year(`salesDate`) as y , (month(`salesDate`) -1) as m,IFNULL(SUM(`Turnover`), 0) as amount
    from ActualSales group by year(`salesDate`), month(`salesDate`)";
  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    $res[$r["y"]][$r["m"]] = $r["amount"];
  }
  return $res;
}
function getUsersForAdmin($data, $params, $mysqli) {
  $res = [];
  $res["data"] = [];
  $sql = "select `id`, `EmplID`, `Employee`, `AreaID`, `Email`, `SalesApp`, `Admin`, `Dashboard`, `firebaseid`, `status`, '' as Action
  from salespersons order by `Employee`";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res["data"][] = $row;
  }
  return $res;
}
function insertNewAppUser($data, $params, $mysqli) {
  $res = [];
  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }
  if ($email == "") {
    $email = str_replace(" ",'', $emplName) . "@costerdiamonds.com";
  }
  $sql = "INSERT INTO `salespersons`(`EmplID`, `Employee`, `AreaID`, `Email`, `SalesApp`, `Admin`, `Dashboard`, `firebaseid`, `status`, `pin`)
  Values ('$emplID','$emplName','','$email','$salesApp','$admin','$dashboard','','0','1840')";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
  }
  return $res;
}
function updateAppUser($data, $params, $mysqli) {
  $res = [];
  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }
  $sql = "update `salespersons` set `$field`='$value' where id='$id'";
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
      $res["sql"] = $sql;
  }
  return $res;
}
function deleteAppUser($data, $params, $mysqli) {
  $res = [];
  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }
  $sql = "delete from `salespersons` where id='$id'";

  $ts = array(
      "user" => array(
        "uid" => $firebaseid,
        "action" => "delete"
     )
   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp = json_decode($server_output);
  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
    $res["status"] = "ok";
      $res["sql"] = $sql;
  }
  return $res;
}
function checkUserRights($data, $params, $mysqli) {
  $res = [];
  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }
  $sql = "select `$application` as access, status from `salespersons` where firebaseid='$firebaseid'";
  $acc = "";
  $st = "";

  $rez = $mysqli->query($sql);
  while ($r = mysqli_fetch_assoc($rez)) {
    $acc = $r["access"];
    $st = $r["status"];
  }
  if ($st != "2") {
    $su = "update salespersons set status='2' where firebaseid='$firebaseid'";
    $mysqli->query($su);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?useractivated");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result=curl_exec($ch);
  }
  $res["access"] = $acc;
  $res["sql"] = $sql;

  return $res;
}
function encryptData($data, $params, $mysqli) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://localhost:9000/?toencrypt=" . $_GET["d"]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $res = [];
  $res["result"] = $result;
  return $res;
}
function sendActivationLink($data, $params, $mysqli) {
  $res = ["ok"];

  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }


  $ts = array(
      "user" => array(
        "mail" => $mail,
        "displayName" => $displayName,
        "action" => "register"
     )
   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp = json_decode($server_output);
    $res["userid"] = $rsp->user->user->uid;
    curl_close();
   $to = $mail;
   $headers = "";

   $subject = "Mail confirmation";
   $headers .= "Reply-To: <accounts@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
   $headers .= "Return-Path: <accounts@costercatalog.com>\r\n";
   $headers .= 'From: accounts@costercatalog.com' . "\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
   // message
   $htmlContent = '
     <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
      <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
     <span style="font-size:30px;margin-top:24px;font-weight:bold;">Dear ' . $displayName . ',</span>
     <br /> <span style="font-size:20px;">you are invited to became a part of Coster Diamonds team.
    ';
     $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please follow link given below to confirm your account.</p>';
     $htmlContent .= '<a href="' . base64_decode($rsp->link) . '">Set password and activate account</a>';

     $htmlContent .= '</p>';
     $ud = $rsp->user->user->uid;
//   $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please follow link given below to confirm your mail.</p>';
/*   $htmlContent .= '<p style="text-align:center;font-size:19px;color:#646464;"><strong>';
   $htmlContent .= '<a href="https://costercatalog.com:9000/?userid=' . "23333" . '&displayName=' . "bbbbbb" . '>Confirm email ' . $mail . ' please</a></strong></p>';*/

   $sql = "update salespersons set status='1',firebaseid='$ud' where id='$id'";
   $mysqli->query($sql);
   $res["html"] = $htmlContent;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?useractivated");
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   $result=curl_exec($ch);
   mail($to, $subject, $htmlContent, $headers);

   return $res;
}
function sendActivationLinkAgain($data, $params, $mysqli) {
  $res = ["ok"];

  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }


  $ts = array(
      "user" => array(
        "mail" => $mail,
        "displayName" => $displayName,
        "firebaseid" => $firebaseid,
        "action" => "register_again"
     )
   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp = json_decode($server_output);
    $res["userid"] = $rsp->user->user->uid;
    curl_close();
   $to = $mail;
   $headers = "";

   $subject = "Mail confirmation";
   $headers .= "Reply-To: <accounts@costercatalog.com>\r\n" . "X-Mailer: php\r\n";
   $headers .= "Return-Path: <accounts@costercatalog.com>\r\n";
   $headers .= 'From: accounts@costercatalog.com' . "\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
   // message
   $htmlContent = '
     <html !DOCTYPE><body style="padding:30px;max-width:600px;min-width:600px;width:600px;min-height:100vh;text-align:center;">
      <div style="padding:15px;width:600px;text-align:center;"><img style="max-width:120px;width:120px;" src="https://costercatalog.com/costerdemo/coster/www/images/logosmall.png" /></div>
     <span style="font-size:30px;margin-top:24px;font-weight:bold;">Dear ' . $displayName . ',</span>
     <br /> <span style="font-size:20px;">you are invited to became a part of Coster Diamonds team.
    ';
     $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please follow link given below to confirm your account.</p>';
     $htmlContent .= '<a href="' . base64_decode($rsp->link) . '">Set password and activate account</a>';

     $htmlContent .= '</p>';
     $ud = $rsp->user->user->uid;
//   $htmlContent .= '<br /><br /><br /><p style="text-align:center;font-size:17px;color:#646464;">Please follow link given below to confirm your mail.</p>';
/*   $htmlContent .= '<p style="text-align:center;font-size:19px;color:#646464;"><strong>';
   $htmlContent .= '<a href="https://costercatalog.com:9000/?userid=' . "23333" . '&displayName=' . "bbbbbb" . '>Confirm email ' . $mail . ' please</a></strong></p>';*/

   $sql = "update salespersons set status='1',firebaseid='$ud' where id='$id'";
   $mysqli->query($sql);
   $res["html"] = $htmlContent;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?useractivated");
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   $result=curl_exec($ch);
   mail($to, $subject, $htmlContent, $headers);

   return $res;
}
function resetUserPassword($data, $params, $mysqli) {
  $res = [];
  $sql = "select * from salespersons where Email='" . $data["email"] . "'";
  $rez = $mysqli->query($sql);
  $rr = mysqli_fetch_assoc($rez);
  if ($rr["status"] != "2") {
    $res["status"] = "error";
    $res["message"] = "You have to login once before you can change/reset password.";
    return $res;
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

  curl_setopt($ch, CURLOPT_URL, "https://localhost:9000/?resetpassword=" . $data["email"]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  $rs = json_decode($result);

  if ($rs->status == "ok") {
    $res["status"] = "ok";
    $res["message"] = "Mail with reset password link has been sent to you.";
  }  else {
    $res["status"] = "error";
    $res["message"] = $rs->error;
  }

  return $res;
}
function getCode($data, $params, $mysqli) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:4000/?getcode");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  $result=curl_exec($ch);
  curl_close($ch);
  $r = json_decode($result);
  $res = [];
  $res["code"] = $r->result;
  return $res;
}
function loginSalesApp($data, $params, $mysqli) {
  $res = [];
  foreach (array_keys($data) as $w) {
    $$w = $data[$w];
  }
  $ts = array(
      "username" => $username,
      "password" => $password,
      "action" => "login",
      "nodecrypt" => 1

   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp =  json_decode($server_output);
    if ($rsp->status == "error") {
      $res["status"] = "error";
      $res["error"] = $rsp->error;
    } else {

      $sql = "SELECT * FROM `salespersons` where firebaseid='" . $rsp->user->user->uid . "' and (SalesApp='1' OR SalesApp='2')";
      if (!mysqli_query($mysqli,$sql)) {
        $res["status"] = "fail";
        $res["type"] = "Mysql error";
        $res["title"] = mysqli_error($mysqli);
        $res["sql"] = $sql;
        return $res;
      }
        $r = $mysqli->query($sql);
        if ($r->num_rows == 0) {
          $res["status"] = "error";
          $res["error"] = "You have not rights to access SalesApp";
          return $res;
        } else {


          $rr = mysqli_fetch_assoc($r);
          $res["sp"] = $rr;
          if ($rr["status"] != "2") {
            $su = "update salespersons set status='2' where firebaseid='" . $rr["firebaseid"] . "'";
            $mysqli->query($su);
          }
          $res["status"] = "ok";
          return $res;
        }
    }
    return $res;
}
function getSPByEmail($data, $params, $mysqli) {
  $res["data"] = [];
  foreach ($data as $k => $v) {
    $$k = $v;
  }
  $sql = "select * from salespersons  where `Email`='$email'";

  if (!mysqli_query($mysqli,$sql)) {
    $res["status"] = "fail";
    $res["type"] = "Mysql error";
    $res["title"] = mysqli_error($mysqli);
    $res["sql"] = $sql;
  } else {
      $res["status"] = "ok";
      $res["sql"] = $sql;
    $r = $mysqli->query($sql);
    while ($row = mysqli_fetch_assoc($r)) {
      $res["sp"][] = $row;

    }
  }
  return $res;
}
function getPage($data, $params, $mysqli) {
      header('Content-Type: text/html');
  $res = [];
  $rr = file_get_contents("/var/www/html/pages/html/" . $data["page"]);
  $res["page"] = ($rr);
  return $res;
}
function getPage_test($data, $params, $mysqli) {
      header('Content-Type: text/html');
  $res = [];
  $rr = file_get_contents("/var/www/coster_test/www/pages/html/" . $data["page"]);
  $res["page"] = ($rr);
  return $res;
}
function getScript($data, $params, $mysqli) {
    header('Content-Type: text/html');
  $res = [];
  $rr = file_get_contents("/var/www/html/pages/js/" . $_GET["page"]);

  $res["script"] = ($rr);
  return $res;
}
function getScript_test($data, $params, $mysqli) {
    header('Content-Type: text/html');
  $res = [];
  $rr = file_get_contents("/var/www/coster_test/www/pages/js/" . $_GET["page"]);

  $res["script"] = ($rr);
  return $res;
}
function tryAC($data, $params, $mysqli) {
  $url = 'https://costerdiamonds.api-us1.com';
  $params = array(
    'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
    'api_action'   => 'contact_list',
    'api_output'   => 'serialize',
    'filters[fields][%CITY%]' => 'Amsterdamm',
      'full' => 0,
  );
  $query = "";
  foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
  $query = rtrim($query, '& ');

  // clean up the url
  $url = rtrim($url, '/ ');

  $api = $url . '/admin/api.php?' . $query;

  $request = curl_init($api); // initiate curl object
  curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
  curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
  //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
  curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

  $response = (string)curl_exec($request); // execute curl fetch and store results in $response
  var_dump(unserialize($response));
}
function insertOrUpdateAC($aSalesId, $params, $mysqli) {
  $sql = "select * from ActualSalesTemp where id='$aSalesId'";
var_dump($sql);
  $r = $mysqli->query($sql);
  $rr = mysqli_fetch_assoc($r);

  foreach (array_keys($rr) as $k) {
    $$k = $mysqli->real_escape_string($rr[$k]);
  }
  $url = 'http://costerdiamonds.api-us1.com';
  $srch = searchAC($Email,$params,$mysqli);

  if ($srch != "") {
    $sqlu = "update ActualSales set ActiveCampainID='$srch' where id='$aSalesId'";
    var_dump($sqlu);
    $mysqli->query($sqlu);
    $params = array(
      'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
      'api_action'   => 'contact_edit',
      'api_output'   => 'serialize',
    );
  } else {
    $params = array(
      'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
      'api_action'   => 'contact_add',
      'api_output'   => 'serialize',
    );
  }
  if ($srch != "") {
    $qvr = "select `TransDate` as TransDate, (`salesDate`) as SalesDate, group_concat(`ProjID`) as ProjID,
    group_concat(`ExtInvoiceNo`) as ExtInvoiceNo, ActualSales.`reference`, group_concat(`receipt`) as receipt,
     group_concat(`PrivateRegNo`) as PrivateRegNo , `SalesCountryName`, `MainGroup`,
      group_concat(ActualSales.`SalesPerson`) as SalesPerson, group_concat(`Brand`) as Brand,
      group_concat(ActualSales.`Showroom`) as Showroom,
      sum(`Turnover`) as Turnover, sum(ActualSales.`Discount`) as Discount, `name`, `address1`, `address2`,
       `city`, `zip`, `telephone`, `ProjName`, `Email`,
       (`AVisitDateTime`) as AVisitDateTime, group_concat(`TouroperatorID`) as TouroperatorID,
        group_concat(`TouroperatorRefNo`) as TouroperatorRefNo, group_concat(`WholesalerID`) as WholesalerID,
       `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`,
        `EUMember`, group_concat(`touroperater`) as touroperater, group_concat(`wholesaler`) as wholesaler, `country`, `language`, `type`,
         `inquery`, `Nationality`,'' AS AciveCampainID, group_concat(`TourNo`) as TourNo  from ActualSales
          where ActiveCampainID='$srch' group by Email";
      $rqvr = $mysqli->query($qvr);
      $rrr = mysqli_fetch_assoc($rqvr);
      foreach (array_keys($rrr) as $k) {
        $$k = $mysqli->real_escape_string($rrr[$k]);
      }
  }

  $nm = explode(" ", $name);
  $fn = $nm[0];
  if (isset($nm[1])) {
    $l = [];
    for ($i=1;$i<count($nm);$i++) {
      $l[] = $nm[$i];
    }
    $ln = implode(" ", $l);
  } else {
    $ln = "";
  }
  $tags = [];
  $email = $Email;
  $pos = strpos($Email, "@nomail");
  if ($pos !== false) {
    $tags[] = "mist email";
  //  $email = $ExtInvoiceNo . "@nomail.com";
  }
  if ($telephone == "" || $telephone == NULL) {
    $tags[] = "no phone";
    $telephone = "";
  }
  if ((floatval($Turnover) + floatval($Discount)) > 2000) {
    $tags[] = "VIP";
  }
  var_dump($email);
  $tg = implode(",", $tags);
     $post = array(
       'email'                    => $email,
       'first_name'               => $fn,
       'last_name'                => $ln,
       'phone'                    => $telephone,
       'tags' => $tg,
       'status[123]'              => 1,
       'p[11]'                    => 11,
       'sdate[123]' =>  $salesDate,
       'field[%DATETIMETRANSACTION%,0]' => $salesDate,
       'field[%ADDRESS%,0]' => $address1,
       'field[%ZIPCODE%,0]' => $zip,
       'field[%CITY%,0]' => $city,
       'field[%NATIONALITY%,0]' => $country,
       'field[%ACCEPTS_MARKETING_CONSENT%,0]' => FALSE,
       'field[%WHOLESALERID%,0]' => $WholesalerID,
       'field[%TOUROPERATOR%,0]' => $touroperater,
       'field[%PROJNAME%,0]' => $ProjName,
       'field[%TOUR_DATE%,0]' => $AVisitDateTime,
       'field[%TURNOVER%,0]' => $Turnover,
       'field[%DISCOUNT%,0]' => $Discount,
       'field[%SALES_PERSONS%,0]' => $SalesPerson,
       'field[%MAINGROUPNAME%,0]'  => $MainGroup,
      // 'field[%PRIVATEID$,0]' => $PrivateRegNo,
       'field[%INVOICE_NUMBER%,0]' => $ExtInvoiceNo,
       'field[%INVOICE_PDF%,0]' => "https://costercatalog.com/api/ACinvoices.php?invoices=" . $ExtInvoiceNo,
       'field[%SHOWROOM%,0]' => $Showroom,
       'field[%TOURNUMBER%,0]' => $TourNo,
       'field[%WHOLESALER%, 0]' => $wholesaler,
       'field[%RECEIPT%, 0]' => $receipt,
       'field[%PRIVATEREGNO%, 0]' => $PrivateRegNo,
       'field[%SALESCOUNTRYNAME%,0]' => $SalesCountryName,
       'field[%TOUROPERATORID%,0]' => $TouroperatorID,
       'field[%WHOLESALER_REF%,0]' => $WholesalerRefNo,
       'field[%PAX%,0]' => $PAX

     );
     if ($srch != "") {
       $post["id"] = $srch;
     }

   $query = "";
  foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
  $query = rtrim($query, '& ');
  $data = "";
  foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
  $data = rtrim($data, '& ');
  $url = rtrim($url, '/ ');
  $api = $url . '/admin/api.php?' . $query;
  $request = curl_init($api); // initiate curl object
  curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
  curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
  curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
  curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
  $response = (string)curl_exec($request); // execute curl post and store results in $response
  curl_close($request);
  $rsp = unserialize($response);
var_dump($rsp);
  return $rsp;
}
function updateActualSalesMails($data, $params, $mysqli) {
  $sql = "select * from ActualSales where Email='' or Email like ('%@nomail%')";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    var_dump($row["id"]);
    $sqlu = "update ActualSales set Email='" . $row["ExtInvoiceNo"] . "@nomail.com' where id='". $row["id"] . "'";
    $mysqli->query($sqlu);
  }

}
function insertActualSalesInAC($data, $params, $mysqli) {
  var_dump("insertActualSalesInAC started");
  $res = [];
  $sql = "select id,ExtInvoiceNo from ActualSales";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sql1 = "select total,TourNo from invoices where invoiceid='" . intval(substr($row["ExtInvoiceNo"],1)) . "'";
    $r1 = $mysqli->query($sql1);
    if ($r1->num_rows > 0) {
      $rr1 = mysqli_fetch_assoc($r1);
      $su = "Update ActualSales set Turnover='" . $rr1["total"] . "', TourNo='" . $rr1["TourNo"] . "' where id='" . $row["id"] . "'";

      $mysqli->query($su);
    }
  }
  $sql = "TRUNCATE ActualSalesTemp";
  var_dump($sql);
  $mysqli->query($sql);
  $sql = "INSERT INTO ActualSalesTemp (select id,`TransDate` as TransDate, (`salesDate`) as SalesDate, group_concat(`ProjID`) as ProjID,
  group_concat(`ExtInvoiceNo`) as ExtInvoiceNo, ActualSales.`reference`, group_concat(`receipt`) as receipt,
   group_concat(`PrivateRegNo`) as PrivateRegNo , `SalesCountryName`, `MainGroup`,
    group_concat(ActualSales.`SalesPerson`) as SalesPerson, group_concat(`Brand`) as Brand,
    group_concat(ActualSales.`Showroom`) as Showroom,
    sum(`Turnover`) as Turnover, sum(ActualSales.`Discount`) as Discount, `name`, `address1`, `address2`,
     `city`, `zip`, `telephone`, `ProjName`, `Email`,
     (`AVisitDateTime`) as AVisitDateTime, group_concat(`TouroperatorID`) as TouroperatorID,
      group_concat(`TouroperatorRefNo`) as TouroperatorRefNo, group_concat(`WholesalerID`) as WholesalerID,
     `WholesalerRefNo`, `TourleaderID`, `GuideID`, `HotelID`, `PAX`, `CountryID`,
      `EUMember`, group_concat(`touroperater`) as touroperater, group_concat(`wholesaler`) as wholesaler, `country`, `language`, `type`,
       `inquery`, `Nationality`,'' AS AciveCampainID,group_concat(`TourNo`) as TourNo,'' AS ACDealID   from ActualSales
        where (ActiveCampainID='' and SUBSTRING(ExtInvoiceNo, 1, 1)='9') group by Email)";
  $rez = $mysqli->query($sql);

  $sql = "select * from ActualSalesTemp";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $r = insertOrUpdateAC($row["id"], $params, $mysqli);
var_dump($r);
    if(isset($r["subscriber_id"])) {
      $uu = "update ActualSales set 	ActiveCampainID='" . $r["subscriber_id"] . "' where ExtInvoiceNo IN (" . $row["ExtInvoiceNo"] . ")";
      var_dump($uu);
      $c = updateContactInfoAC($r["subscriber_id"], $params, $mysqli);
      $mysqli->query($uu);
    }
  }
  var_dump("??????????????????????????????????????????????????");
  $ss = "TRUNCATE costerdiamonds.ActualSales";
  $mysqli->query($ss);
  $ss = "insert into costerdiamonds.ActualSales (select * from costertemp.ActualSales)";
  $mysqli->query($ss);
  $res["done"] = "insertActualSalesInAC";
  var_dump($res);
  createDeals($data, $params, $mysqli);
  return $res;
}
function searchAC($email, $params, $mysqli) {
  $url = 'https://costerdiamonds.api-us1.com';
  $params = array(
    'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
    'api_action'   => 'contact_list',
    'api_output'   => 'serialize',
    'filters[email]' => $email,
    'full' => 0
  );
  $query = "";
  foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
  $query = rtrim($query, '& ');

  // clean up the url
  $url = rtrim($url, '/ ');
  $api = $url . '/admin/api.php?' . $query;
  $request = curl_init($api); // initiate curl object
  curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
  curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
  //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
  curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

  $response = (string)curl_exec($request); // execute curl fetch and store results in $response
  curl_close($request); // close curl object
  $rsp = unserialize($response);
  var_dump($rsp);
  if (isset($rsp[0]["id"])) {
    return $rsp[0]["id"];
  } else {
    return "";
  }
}
function showInvoices($data, $params, $mysqli) {
  setlocale(LC_MONETARY, 'nl_NL');
  $res = [];
  $res["data"] = [];
  $ics = explode(",", $_GET["invoices"]);
  $rii = [];
  foreach ($ics as $ic) {
    $rii[] = intval(substr($ic,1));
  }
  $rinv = implode(",", $rii);
  $sql = "select *,c.customer,tours.touroperater from invoices
  left join (select customerid,concat(name, ', ', email, ', ',telephone, ', ', country, ', ', IFNULL(countryCode,'')) as customer from customers) c
    on invoices.customerid=c.customerid
  left join tours on invoices.tourno=tours.ProjId   where invoiceid IN (" . $rinv . ") order by date desc ";

  $r = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($r)) {
        $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
        $rsum = $mysqli->query($ssum);
        $rs = mysqli_fetch_assoc($rsum);
        $startingTotal = floatval($rs["startingTotal"]);
        $row["startingTotal"] = $startingTotal;

        $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$row["invoiceid"]."' and version=''";
        $rsum = $mysqli->query($ssum);
        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["initialdue"] = floatval($tp);


        $ssum = "select sum(original) as paid from invoice_payments where  invoiceid='" . $row["invoiceid"] . "'";
        $row["due"] = "";
        $rsum = $mysqli->query($ssum);

        $tp = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
        $res["currentdue"] = floatval($tp);


        if ($res["initialdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed</span>";
        }

        if ($res["initialdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed, change " . number_format($res["initialdue"] * -1, 2, ',', '.') . "<span>";
        }


        if ($res["initialdue"] > 0 && $res["currentdue"] > 0) {
          $row["due"] .= "<span style='min-width:200px;color:red;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " Current " . number_format($res["currentdue"], 2, '.', ',') . "</span>";
        }

        if ($res["initialdue"] > 0 && $res["currentdue"] == 0) {
          $row["due"] .= "<span style='min-width:200px;color:black;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed</span> ";
        }
        if ($res["initialdue"] > 0 && $res["currentdue"] < 0) {
          $row["due"] .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Initial " . number_format($res["initialdue"], 2, ',', '.') . " -> Completed, change " .  number_format($res["currentdue"] * -1, 2, '.', ','). "</span>";
        }
        $row["due"] .= "<realvalue realvalue='" . floatval($row["dueAmount"]) . "'></realvalue>";
        $row["due"] .= "<br />€ " . number_format(floatval($row["dueAmount"]), 2, ',', '.');
      /*  if ($res["initialdue"] > 0 && ($res["initialdue"] == $res["currentdue"])) {
          $row["due"] .= "<span style='min-width:200px;color:red' onclick='showPayments(this);'>&nbsp;Due " . number_format($res["initialdue"], 2, '.', ','). "</span>";
        }*/
        if ((floatval($startingTotal) - floatval($row["dueAmount"])) > 0) {
          $row["hasDicount"] = 1;
          $raz = floatval($startingTotal) - floatval($row["dueAmount"]);
          $row["discount"] =  "(" .   number_format(floor((floatval($raz) / floatval($row["startingTotal"]) * 100)), 0, '.', ',') . "%)<br />
          <realvalue realvalue='" .(floatval($startingTotal) - floatval($row["dueAmount"])) . "'>€ " . number_format(floatval($startingTotal) - floatval($row["dueAmount"]), 2, ',', '.') . "</realvalue>";
        } else {
          $row["discount"] = "";
          $row["discount"] = "<realvalue style='display:none;' realvalue='0'></realvalue>";
          $row["hasDicount"] = 0;
        }
        $ss = "select DATE(date) as date,original as paid,payment, version from invoice_payments
                inner join paymentMethods on invoice_payments.paymentID=paymentMethods.PaymentID
                 where  invoiceid='" .$row["invoiceid"]."'
                ";

        $r1 = $mysqli->query($ss);
        $row["due"] .= "<div payments style='display:none;min-width:100%;text-align:left;'>";
        while ($rr = mysqli_fetch_assoc($r1)) {

          if ($rr["paid"] > 0) {
            if ($rr["version"] == "") {
              $rr["version"] = "&nbsp;";
            }
            $row["due"] .= "<pay>" . $rr["version"] . "|" . $rr["date"] . "|" . $rr["payment"] . "| € " . number_format($rr["paid"], 2, '.', ',') . "</pay>";
          }

        }
        $row["due"] .= "<invoice>" . $row["invoiceid"] . "</invoice>" . "<version>" . $row["version"] . "</version><pdf>" . $row["pdf"] . "</pdf>";

        $row["due"] .= "</div>";
        $row["currentdue"] = $res["currentdue"];
        $res["data"][] = $row;
    }


  return $res;
}
function updateACInvoiceLink($data, $params, $mysqli) {
    $url = 'http://costerdiamonds.api-us1.com';
  $sql = "select group_concat(ExtInvoiceNo) as ExtInvoiceNo, ActiveCampainID from ActualSales where
   SUBSTRING(ExtInvoiceNo, 1, 1)='9' and ActiveCampainID<>'' group by ActiveCampainID";
 $rez = $mysqli->query($sql);
 $params = array(
   'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
   'api_action'   => 'contact_edit',
   'api_output'   => 'serialize',
   'overwrite'    =>  0
 );
 while ($row = mysqli_fetch_assoc($rez)) {
   $post = array(
     "id" => $row["ActiveCampainID"],
     "field[%INVOICE_PDF%,0]" => "https://costercatalog.com/api/ACinvoices.php?invoices=" . $row["ExtInvoiceNo"]
   );
   var_dump($post);
  $query = "";
  foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
  $query = rtrim($query, '& ');
  $data = "";
  foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
  $data = rtrim($data, '& ');
  $url = rtrim($url, '/ ');
  $api = $url . '/admin/api.php?' . $query;
  var_dump($api);
  $request = curl_init($api); // initiate curl object
  curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
  curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
  curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
  curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
  $response = (string)curl_exec($request); // execute curl post and store results in $response
  curl_close($request);
  $rsp = unserialize($response);
  var_dump($rsp);
 }
}
function checkACLogin($data, $params, $mysqli) {
  $res = [];
  if ($data["username"] == "ACCoster" && $data["password"] == "Ac!Coster#2021") {
    $res["login"] = "1";
  } else {
    $res["login"] = "0";
  }
  return $res;
}
function createDeals($data, $params, $mysqli) {
  $cfields = [];
  $ch = curl_init( "https://costerdiamonds.api-us1.com/api/3/dealCustomFieldMeta?limit=100" );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Api-Token: b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7'));
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  $result = json_decode(curl_exec($ch));

  curl_close($ch);
  foreach ($result as $r) {
    foreach($r as $f) {

      $cfields[$f->personalization] = $f->id;
    }
  }

  $res = [];
  $sql = "TRUNCATE invoice_body";
  $mysqli->query($sql);
  $sql = "insert into invoice_body (select * from costerdiamonds.invoice_body)";
  $mysqli->query($sql);
  $sql = "TRUNCATE invoice_payments";
  $mysqli->query($sql);
  $sql = "insert into invoice_payments (select * from costerdiamonds.invoice_payments)";
  $mysqli->query($sql);


  $sql = "select * from ActualSales where  ACDealID='' and ActiveCampainID<>'' and SUBSTRING(ExtInvoiceNo, 1, 1)='9' order by id desc";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $invoice_number = intval(substr($row["ExtInvoiceNo"],1));
    $asid = $row["id"];
    $itms = "";
    $ptms = "";
    $inv = "select * from invoice_body where invoiceid='$invoice_number'";

    $rinv = $mysqli->query($inv);
    while ($rri = mysqli_fetch_assoc($rinv)) {
      $itms .= $rri["SerialNo"] . " " . $rri["productName"] . " | " . $rri["CompName"] . " | " . $rri["quantity"] . " X " . $rri["startRealPrice"] . " | " . $rri["Discount"] . " | " . $rri["realPrice"] . "\r\n";
    }
    $inv = "select * from invoice_payments where invoiceid='$invoice_number'";
    $rinv = $mysqli->query($inv);
    while ($rri = mysqli_fetch_assoc($rinv)) {
      $ptms .= $rri["date"] . " | " . $rri["paymentMethod"] . " " . $rri["currency"] . " | " . $rri["amount"] . " | " . $rri["amount"] . "\r\n";
    }
    $headers = [
      'Api-Token: b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
    //  'Content-Type:application/json'
    ];
    $fields =  array(
        array(
            "customFieldId"=> $cfields["DEAL_SALESPERSON"],
            "fieldValue"=> $row["SalesPerson"]
          ),
          array(
              "customFieldId"=> $cfields["DEAL_INVOICEID"],
              "fieldValue"=> $row["ExtInvoiceNo"]
            ),
          array(
              "customFieldId"=> $cfields["DEAL_INVOICE_SALES_DATE"],
              "fieldValue"=> $row["salesDate"]
            ),
          array(
              "customFieldId"=> $cfields["DEAL_INVOICE_SALES_PERSON"],
              "fieldValue"=> $row["SalesPerson"]
            ),
            array(
                "customFieldId"=> $cfields["DEAL_SHOWROOM"],
                "fieldValue"=> $row["Showroom"]
              ),
          array(
              "customFieldId"=> $cfields["DEAL_TURNOVER"],
              "fieldValue"=> $row["Turnover"],
              "fieldCurrency"=> "EUR"
            ),
          array(
              "customFieldId"=> $cfields["DEAL_DISCOUNT"],
              "fieldValue"=> $row["Discount"],
              "fieldCurrency"=> "EUR"
            ),
            array(
                "customFieldId"=> $cfields["DEAL_PDF_LINK"],
                "fieldValue"=> "https://costercatalog.com/api/ACinvoices.php?invoices=" . $row["ExtInvoiceNo"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_TOUR_NO"],
                "fieldValue"=> $row["TourNo"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_PRIVATE_REG_NO"],
                "fieldValue"=> $row["PrivateRegNo"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_PROJECT_NAME"],
                "fieldValue"=> $row["ProjName"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_TOUROPERATER"],
                "fieldValue"=> $row["touroperater"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_WHOLESALER"],
                "fieldValue"=> $row["wholesaler"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_COUNTRY"],
                "fieldValue"=> ($row["country"] == NULL) ? "" : $row["country"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_NATIONALITY"],
                "fieldValue"=> ($row["Nationality"] == NULL) ? "" : $row["Nationality"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_HOTEL"],
                "fieldValue"=> ($row["HotelID"] == NULL) ? "" : $row["HotelID"],
            ),
            array(
                "customFieldId"=> $cfields["DEAL_PAYMENTS"],
                "fieldValue"=> $ptms,
            ),
            array(
                "customFieldId"=> $cfields["DEAL_ITEMS"],
                "fieldValue"=> $itms
              ),
        );


          $data = array(

                  "contact" => $row["ActiveCampainID"],
                  "description" =>"Invoice " . $row["ExtInvoiceNo"],
                  "currency" => "eur",
                  "owner" => "1",
                  "percent" => 100,
                  "title" => "SalesApp Invoice " . $row["ExtInvoiceNo"],
                  "value" => $row["Turnover"] * 100,
                  "group" => 7,
                  "status" => 1,
                  "fields" => $fields


          );
            $payload = json_encode(array("deal" => $data));

            $ch = curl_init( "https://costerdiamonds.api-us1.com/api/3/deals" );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Api-Token: b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7'));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = json_decode(curl_exec($ch));

            $ssu = "update ActualSales set ACDealID='" . $result->deal->id . "'  where id='$asid'";
            var_dump($ssu);
            $mysqli->query($ssu);
            curl_close($ch);
      }
}
function updateContactInfoAC($acSalesId, $params, $mysqli) {
  $sql = " SELECT ActiveCampainID, MAX(id) AS id FROM ActualSales where ActiveCampainId='$acSalesId' GROUP BY ActiveCampainId";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sql1 = "select * from ActualSales where id='" . $row["id"] . "'";

    $row1 = mysqli_fetch_assoc($mysqli->query($sql1));
    $sql2 = "select sum(Turnover) as T, sum(Discount) as D from ActualSales where ActiveCampainId='" . $row["ActiveCampainID"] . "'";
    $row2 = mysqli_fetch_assoc($mysqli->query($sql2));
    $row1["Turnover"] = $row2["T"];
    $row1["Discount"] = $row2["D"];
    foreach (array_keys($row1) as $k) {
      $$k = $mysqli->real_escape_string($row1[$k]);
    }

    $url = 'https://costerdiamonds.api-us1.com';
    $params = array(
      'api_key'      => 'b3c3b856b341bb9bb20c9b1a6af7eed75e6a395e6f9e0fee919936347636404300bc17c7',
      'api_action'   => 'contact_edit',
      'api_output'   => 'serialize',
    );
    $post = array(
      'id'                       => $row["ActiveCampainID"],
      'sdate[123]' =>  $salesDate,
      'status[123]'              => 1,
      'p[11]'                    => 11,
      'field[%DATETIMETRANSACTION%,0]' => $salesDate,
      'field[%TURNOVER%,0]' => $Turnover,
      'field[%DISCOUNT%,0]' => $Discount,
      'field[%SALES_PERSONS%,0]' => $SalesPerson,
      'field[%MAINGROUPNAME%,0]'  => $MainGroup,
     // 'field[%PRIVATEID$,0]' => $PrivateRegNo,
      'field[%INVOICE_NUMBER%,0]' => $ExtInvoiceNo,
      'field[%INVOICE_PDF%,0]' => "https://costercatalog.com/api/ACinvoices.php?invoices=" . $ExtInvoiceNo,
      'field[%SHOWROOM%,0]' => $Showroom,
      'field[%TOURNUMBER%,0]' => $TourNo,
      'field[%WHOLESALER%, 0]' => $wholesaler,
      'field[%RECEIPT%, 0]' => $receipt,
      'field[%PRIVATEREGNO%, 0]' => $PrivateRegNo,
      'field[%SALESCOUNTRYNAME%,0]' => $SalesCountryName,
      'field[%TOUROPERATORID%,0]' => $TouroperatorID,
      'field[%WHOLESALER_REF%,0]' => $WholesalerRefNo,
      'field[%PAX%,0]' => $PAX,
      'field[%TOUROPERATOR%,0]' => $touroperater,
      'field[%TOUR_DATE%,0]' => $AVisitDateTime,
      'field[%TOURNUMBER%,0]' => $TourNo,
      'field[%WHOLESALER%, 0]' => $wholesaler,
      'field[%WHOLESALERID%,0]' => $WholesalerID,
    );
    $query = "";
   foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
   $query = rtrim($query, '& ');
   $data = "";
   foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
   $data = rtrim($data, '& ');
   $url = rtrim($url, '/ ');
   $api = $url . '/admin/api.php?' . $query;
   $request = curl_init($api); // initiate curl object
   curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
   curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
   curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
   curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
   $response = (string)curl_exec($request); // execute curl post and store results in $response
   curl_close($request);
   $rsp = unserialize($response);
   var_dump($row["ActiveCampainID"] . " ===> " . $rsp["result_message"]);
  }
}
function checkCustomerGB($data, $params, $mysqli) {
  $res = [];
  $ts = array(
      "country" => $data["country"],
      "amount" => $data["amount"],
      "action" => "checkGBE",
  //    "nodecrypt" => 1

   );
    $pload = json_encode($ts);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://costercatalog.com:9000");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $rsp =  json_decode($server_output);
    $res["response"] = $rsp;
    $res["data"] = $data;
    return $res;
}
function getGBdata($data, $params, $mysqli) {
  $res = [];
  $sql = "select * from invoices where invoiceid=" . $data["invoiceid"];
  $rez = $mysqli->query($sql);
  $rr = mysqli_fetch_assoc($rez);
  $res["invoice"]["due"] = $rr["dueAmount"];
  $res["invoice"]["date"] = explode(" ",$rr["saledate"])[0];
  $sql = "select * from customers where customerid='" . $rr["customerid"] . "'";
  $rrc = $mysqli->query($sql);
  $rrc = mysqli_fetch_assoc($rrc);
  $nm = explode(" ", $rrc["name"]);
  $fn = $nm[0];
  if (isset($nm[1])) {
    $l = [];
    for ($i=1;$i<count($nm);$i++) {
      $l[] = $nm[$i];
    }
    $ln = implode(" ", $l);
  } else {
    $ln = "";
  }
  $rrc["firstName"] = $fn;
  $rrc["lastName"] = $ln;
  $res["customer"] = $rrc;
  $res["GBid"] = $rr["GBid"];
  $res["GBinitial"] = $rr["GBinitial"];
  return $res;
}
function setInvoiceGB($data, $params, $mysqli) {
  $res = [];
  $sql = "update invoices set GBid='" . $data["dif"] . "',GBinitial='" . $data["amount"] . "' where invoiceid='" . $data["invoiceid"] . "'";
  $mysqli->query($sql);
  $res["sql"] = $sql;
  return res;
}
function deleteInvoiceGB($data, $params, $mysqli) {
  $res = [];
  $sql = "update invoices set GBid='',GBinitial='' where invoiceid='" . $data["invoiceid"] . "'";
  $mysqli->query($sql);
  $res["sql"] = $sql;
  return res;
}
function updateSchedule($data, $params, $mysqli) {
  $res = [];
  $sql = "select * from schedule where name=departement";
  $rez = $mysqli->query($sql);
  $sqlu = [];
  while ($row = mysqli_fetch_assoc($rez)) {

    for ($i=1;$i<32;$i++) {
      $sqlu[] = "update schedule set `$i`='" . $row["departement"] . "' where `$i`<>'' and departement='" . $row["departement"] . "' and name<>departement";
    }

  }
  $sqluu = implode(";",$sqlu);
  foreach($sqlu as $ss) {
    var_dump($ss);
    $mysqli->query($ss);
  }
  $sql = "TRUNCATE schedule_personal";
  $mysqli->query($sql);
  $sql = "insert into schedule_personal (select id,year,month,name,
    group_concat(`1`) as '1',
    group_concat(`2`) as '2',
    group_concat(`3`) as '3',
    group_concat(`4`) as '4',
    group_concat(`5`) as '5',
    group_concat(`6`) as '6',
    group_concat(`7`) as '7',
    group_concat(`8`) as '8',
    group_concat(`9`) as '9',
    group_concat(`10`) as '10',
    group_concat(`11`) as '11',
    group_concat(`12`) as '12',
    group_concat(`13`) as '13',
    group_concat(`14`) as '14',
    group_concat(`15`) as '15',
    group_concat(`16`) as '16',
    group_concat(`17`) as '17',
    group_concat(`18`) as '18',
    group_concat(`19`) as '19',
    group_concat(`20`) as '20',
    group_concat(`21`) as '21',
    group_concat(`22`) as '22',
    group_concat(`23`) as '23',
    group_concat(`24`) as '24',
    group_concat(`25`) as '25',
    group_concat(`26`) as '26',
    group_concat(`27`) as '27',
    group_concat(`28`) as '28',
    group_concat(`29`) as '29',
    group_concat(`30`) as '30',
    group_concat(`31`) as '31'
    from schedule
    where departement<>name group by name
  )";
  $mysqli->query($sql);
  $sql = 'update schedule_personal set
  `1`=REPLACE(`1`,",",""),
  `2`=REPLACE(`2`,",",""),
  `3`=REPLACE(`3`,",",""),
  `4`=REPLACE(`4`,",",""),
  `5`=REPLACE(`5`,",",""),
  `6`=REPLACE(`6`,",",""),
  `7`=REPLACE(`7`,",",""),
  `8`=REPLACE(`8`,",",""),
  `9`=REPLACE(`9`,",",""),
  `10`=REPLACE(`10`,",",""),
  `11`=REPLACE(`11`,",",""),
  `12`=REPLACE(`12`,",",""),
  `13`=REPLACE(`13`,",",""),
  `14`=REPLACE(`14`,",",""),
  `15`=REPLACE(`15`,",",""),
  `16`=REPLACE(`16`,",",""),
  `17`=REPLACE(`17`,",",""),
  `18`=REPLACE(`18`,",",""),
  `19`=REPLACE(`19`,",",""),
  `20`=REPLACE(`20`,",",""),
  `21`=REPLACE(`21`,",",""),
  `22`=REPLACE(`22`,",",""),
  `24`=REPLACE(`23`,",",""),
  `24`=REPLACE(`24`,",",""),
  `25`=REPLACE(`25`,",",""),
  `26`=REPLACE(`26`,",",""),
  `27`=REPLACE(`27`,",",""),
  `28`=REPLACE(`28`,",",""),
  `29`=REPLACE(`29`,",",""),
  `30`=REPLACE(`30`,",",""),
  `31`=REPLACE(`31`,",","")';
    $mysqli->query($sql);
  return $res;
}
function getScheduleResources($data, $params, $mysqli) {
  $res =[];
  $sql = "select distinct(department) from schedule";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {

    $chld = [];
    $sqlc = "select distinct(empid) as empid, empname from schedule_new where department='" . $row["department"] . "'";
    //var_dump($sqlc);
    $rc = $mysqli->query($sqlc);
    while ($rcr = mysqli_fetch_assoc($rc)) {
      $nm = explode(" ", $rcr["empname"]);

      $n = $nm[0];
      for ($i=1;$i<count($nm);$i++) {
        $n .= " " . substr($nm[$i], 0, 1) . ".";
      }

      $chld[] = [
          "id" => $row["department"] . "_" . $rcr["empid"],
          "title" => $n,
          "resourceId" => trim($row["department"]),
      ];
    }
    $chld[] = [
        "id" => $row["department"] . "_sum",
        "title" => "Z - Summary",
        "resourceId" => trim($row["department"]),
    ];
    $res[] = [
        "id" => trim($row["department"]),
        "title" => $row["department"],
        "children" => $chld
    ];
  }
  return $res;
}
function getScheduleResourcesGroup($data, $params, $mysqli) {
  $res =[];
  $sql = "select distinct(department) as department from schedule_new";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $res[] = [
        "id" => $row["department"],
        "title" => $row["department"],

    ];
  }
  return $res;
}
function getScheduleEvents($data, $params, $mysqli) {
  $res = [];
  $as = $_GET["abscence"];
  $sql = "update schedule_new set abscence='False' where type='DR' or type='TW'";
  $mysqli->query($sql);
  $sql = "select *,group_concat(type) as t from schedule_new where description<>'' group by date,empid";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $bclr = "transparent";
    if ($row["abscence"] == "True") {
      $bclr = "green";
    }

    if ($as == "-1") {
      if ($row["abscence"] == 'True') {
        $bclr = "red";
      }
      if ($row["abscence"] == 'False') {
        $bclr = "green";
      }
    }
    if ($row["type"] == "DR") {
      $bclr = "#00C7FE";
    }
        $res[] = [
          "id" => $row["id"],
          "resourceId" => $row["department"] . "_" . $row["empid"],
          "color" => $bclr,
          "textColor" => ($as != "-1") ? "black" : "white",
          "title" => "(" . $row["t"] . ") " . $row["desciptionEN"],
          "start" =>  $row["date"],
          "abscence" => ($row["abscence"] == "True") ? "1" : "0"
       ];
  }
  $sql ="select date,department,count(distinct(empid)) as cnt from schedule_new where abscence='False' group by date,department";
  $rw = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rw)) {
    $res[] = [
      "resourceId" => $row["department"] . "_sum",
      "title" => $row["cnt"],
      "start" =>  $row["date"]
   ];
  }
  return $res;
}
function getScheduleEventsGroup($data, $params, $mysqli) {
  $res = [];
  $as = $_GET["abscence"];
  $sql = "update schedule_new set abscence='False' where type='DR' or type='TW'";
  $mysqli->query($sql);
  $sql = "select *,group_concat(type) as t from schedule_new where description<>'' group by date,empid";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $bclr = "transparent";
    if ($row["type"] == "DR") {
      $bclr = "#00C7FE";
    }
    $nm = explode(" ", $row["empname"]);
    $n = $nm[0];
    for ($i=1;$i<count($nm);$i++) {
      $n .= " " . substr($nm[$i], 0, 1) . ".";
    }
    if ($as == "-1") {
      if ($row["abscence"] == 'True') {
        $bclr = "red";
      }
      if ($row["abscence"] == 'False') {
        $bclr = "green";
      }
    }
    if ($row["type"] == "DR") {
      $bclr = "#00C7FE";
    }
    $res[] = [
      "id" => $row["id"],
      "resourceId" => $row["department"],
      "color" => $bclr,
        "textColor" => ($as != "-1") ? "black" : "white",
      "title" => $n,
      "start" =>  $row["date"],
      "abscence" => ($row["abscence"] == "True") ? "1" : "0"
    ];
  }
  $sql ="select date,department,count(distinct(empid)) as cnt from schedule_new where abscence='False' group by date,department";
  $rw = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rw)) {
    $res[] = [
      "resourceId" => $row["department"],
      "title" => $row["cnt"],
      "start" =>  $row["date"]
   ];
  }
  return $res;
}
function getScheduleResourcesEmployee($data, $params, $mysqli) {
  $res =[];

  $sql = "select * from schedule_new order by empname";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $nm = explode(" ", $row["empname"]);
    $n = $nm[0];
    for ($i=1;$i<count($nm);$i++) {
      $n .= " " . substr($nm[$i], 0, 1) . ".";
    }

    $res[] = [
        "id" => $row["empid"],
        "title" => $n,

    ];
  }
  $res[] = [
    "id" => "sum",
    "title" => "Z - Summary",
  ];
  return $res;
}
function getScheduleEventsEmployees($data, $params, $mysqli) {
  $res = [];
  $as = $_GET["abscence"];
  $sql = "update schedule_new set abscence='False' where type='DR' or type='TW'";
  $mysqli->query($sql);
  $q = "";
  if ($as == "0") {
    $q = " where abscence='False' ";
  }
  if ($as == "1") {
    $q = " where abscence='True' ";
  }
  $sql = "select *,group_concat(type) as t,group_concat(desciptionEN) as d from schedule_new " . $q . " group by date,empid";

  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $nm = explode(" ", $row["empname"]);
    $n = $nm[0];
    for ($i=1;$i<count($nm);$i++) {
      $n .= " " . substr($nm[$i], 0, 1) . ".";
    }
    $bclr = "transparent";

    if ($row["type"] == "DR") {
      $bclr = "#00C7FE";
    }
    if ($as == "-1") {
      if ($row["abscence"] == 'True') {
        $bclr = "red";
      }
      if ($row["abscence"] == 'False') {
        $bclr = "green";
      }
    }
    if ($row["type"] == "DR") {
      $bclr = "#00C7FE";
    }
    $res[] = [
      "id" => $row["id"],
      "resourceId" => $row["empid"],
      "color" => $bclr,
      "title" => $n,
      "textColor" => ($as != "-1") ? "black" : "white",
      "start" =>  $row["date"],
      "abscence" => ($row["abscence"] == "True") ? "1" : "0"
   ];
  }
  $sql ="select date,count(distinct(empid)) as cnt from schedule_new where (abscence='False' or type='DR') group by date";
  $rw = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rw)) {
    $res[] = [
      "resourceId" => "sum",
      "title" => $row["cnt"],
      "start" =>  $row["date"]
   ];
  }
  return $res;
}
function update_producsts_short_attr($data, $params, $mysqli) {
  $res = [];
  $sql = "select * from jewelCompositions";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    foreach (array_keys($row) as $ww) {
      $$ww = $row[$ww];
    }
    $sqlu = "update products_short set ClarityID_1='$ClarityID',ColourID_1='$ColourID',CutID_1='$CutID',TypeID_1='$TypeID'
    where products_short.ProductID='$ProductID'";
    var_dump($sqlu);
    $mysqli->query($sqlu);
  }
  return $res;
}
function generateAttributesColor($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(MainAttributeValue) as MAV from attributes where AtributeType='Colour' and MainAttributeValue<>''";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sqls = "select distinct(Atribute) as ATR from attributes where MainAttributeValue='" . $row["MAV"] . "'";
    $rs = $mysqli->query($sqls);
    while ($rows = mysqli_fetch_assoc($rs)) {
      $res[$row["MAV"]][] = $rows["ATR"];
    }
  }
  return $res;
}
function generateAttributesType($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(MainAttributeValue) as MAV from attributes where AtributeType='Type' and MainAttributeValue<>''";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sqls = "select distinct(Atribute) as ATR from attributes where MainAttributeValue='" . $row["MAV"] . "'";
    $rs = $mysqli->query($sqls);
    while ($rows = mysqli_fetch_assoc($rs)) {
      $res[$row["MAV"]][] = $rows["ATR"];
    }
  }
  return $res;
}
function generateAttributesCut($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(MainAttributeValue) as MAV from attributes where AtributeType='Cut' and MainAttributeValue<>''";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sqls = "select distinct(Atribute) as ATR from attributes where MainAttributeValue='" . $row["MAV"]  . "'";
    $rs = $mysqli->query($sqls);
    while ($rows = mysqli_fetch_assoc($rs)) {
      $res[$row["MAV"]][] = $rows["ATR"];
    }
  }
  return $res;
}
function generateAttributesClarity($data, $params, $mysqli) {
  $res = [];
  $sql = "select distinct(MainAttributeValue) as MAV from attributes where AtributeType='Clarity' and MainAttributeValue<>''";
  $rez = $mysqli->query($sql);
  while ($row = mysqli_fetch_assoc($rez)) {
    $sqls = "select distinct(Atribute) as ATR from attributes where MainAttributeValue='" . $row["MAV"]  . "'";
    $rs = $mysqli->query($sqls);
    while ($rows = mysqli_fetch_assoc($rs)) {
      $res[$row["MAV"]][] = $rows["ATR"];
    }
  }
  return $res;
}
function getcalendarClient() {
  $client = new Google_Client();
   $client->setApplicationName('Google Calendar API PHP Quickstart');
   $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
   $client->setAuthConfig('credentials.json');
   $client->setAccessType('offline');
   $client->setPrompt('select_account consent');

   // Load previously authorized token from a file, if it exists.
   // The file token.json stores the user's access and refresh tokens, and is
   // created automatically when the authorization flow completes for the first
   // time.
   $tokenPath = 'token.json';
   if (file_exists($tokenPath)) {
       $accessToken = json_decode(file_get_contents($tokenPath), true);
       $client->setAccessToken($accessToken);
   }

   // If there is no previous token or it's expired.
   if ($client->isAccessTokenExpired()) {
       // Refresh the token if possible, else fetch a new one.
       if ($client->getRefreshToken()) {
           $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
       } else {
           // Request authorization from the user.
           $authUrl = $client->createAuthUrl();
           printf("Open the following link in your browser:\n%s\n", $authUrl);
           print 'Enter verification code: ';
           $authCode = trim(fgets(STDIN));

           // Exchange authorization code for an access token.
           $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
           $client->setAccessToken($accessToken);

           // Check to see if there was an error.
           if (array_key_exists('error', $accessToken)) {
               throw new Exception(join(', ', $accessToken));
           }
       }
       // Save the token to a file.
       if (!file_exists(dirname($tokenPath))) {
           mkdir(dirname($tokenPath), 0700, true);
       }
       file_put_contents($tokenPath, json_encode($client->getAccessToken()));
   }

   return $client;
}
?>
