<?php

// DB table to use
session_start();
header("Access-Control-Allow-Origin: *");

/*  if (!isset($_SERVER["HTTP_REFERER"]) && !isset($_POST["secret"])) {
    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }
  if (!strpos($_SERVER["HTTP_REFERER"], $_SERVER['HTTP_HOST']) && !isset($_POST["secret"])) {
    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }*/

  unset($_POST["secret"]);
$table =  "products";
$table_search =  "products_search";
$db_database = "costerdiamonds";
$db_user = "cobol1962";
$db_password = "uMuRZfZsTety6RJ3";
// Table's primary key
$primaryKey = 'ItemID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(

    array( 'db' => 'ItemID', 'dt' => 'DT_RowId'),
    array( 'db' => 'ItemID', 'dt' => 'ItemID'),
    array( 'db' => 'imageName', 'dt' => 'imageName',
      "formatter" => function($d, $row) {
        $dd = explode(".", $d);
        return strtoupper($dd[0]) . "." . strtolower($dd[1]);
      }
    ),
    array( 'db' => 'ItemName',
    'dt' => 'productName',
    "formatter" => function($d, $row) {
        return $d;
      }
    ),
    array( 'db' => 'MainGroup', 'dt' => 'MainGroup'),
    array( 'db' => 'SalesPrice', 'dt' => 'SalesPrice'),
    array( 'db' => 'Discount', 'dt' => 'Discount',
      "formatter" => function($d, $row) {
        if ($d == "" || $d == NULL) {
           return 0;
        } else {
          return $d;
        }
      }
    ),
    array( 'db' => 'Warehouse', 'dt' => 'Warehouse'),
    array( 'db' => 'BrandID', 'dt' => 'BrandID'),
    array( 'db' => 'CutId', 'dt' => 'CutID'),
    array( 'db' => 'TypeID', 'dt' => 'TypeID'),
    array( 'db' => 'SerialNo', 'dt' => 'SerialNo'),
    array( 'db' => 'OnhandQnt', 'dt' => 'OnhandQnt'),
    array( 'db' => 'CompName', 'dt' => 'CompName',
        "formatter" => function($d, $row) {
          return implode("|",explode(",", $d));
        }
    ),
    array( 'db' => 'ColorDesc', 'dt' => 'ColorDesc',
      "formatter" => function($d, $row) {
        $dsc = explode(",", $d);
        $str = "";
        $i = 0;
        $res = [];
        $ddd = explode(" ", $dsc[0]);
        $i = 0;
        foreach ($ddd as $aa) {
          $str .= ($i == 0) ? ($aa . " " ) : strtoupper(substr($aa,0,1));
          $i++;
        }
        if (count($dsc) > 1) {
          $res["abbrv"] = $str . " + " . (count($dsc) - 1);
        } else {
          $res["abbrv"] = $str;
        }
        $res["full"] = implode("|",explode(",", $d));
        return $res;
      }
    ),
    array( 'db' => 'ClarityID', 'dt' => 'ClarityID'),
    array( 'db' => 'ColourID', 'dt' => 'ColourID'),
    array( 'db' => 'Qnt', 'dt' => 'Qnt'),
    array( 'db' => 'TotalWeight', 'dt' => 'TotalWeight'),
    array( 'db' => 'SerialName', 'dt' => 'SerialName'),
    array( 'db' => 'realPrice', 'dt' => 'realPrice'),
);

// SQL server connection information
$sql_details = array(
    'user' => "cobol1962",
    'pass' => "uMuRZfZsTety6RJ3",
    'db'   => "costerdiamonds",
    'host' => 'localhost'
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
require("ssp.class.php");
$resp = SSP::simple( $_REQUEST, $sql_details, $table,$table_search, $primaryKey, $columns );

$_SESSION["mastersql"] = $resp["sql"];
echo json_encode(
    $resp
);
