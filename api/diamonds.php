<?php

// DB table to use
session_start();
header("Access-Control-Allow-Origin: *");
include ("config/database.php");

  if (!isset($_SERVER["HTTP_REFERER"]) && !isset($_POST["secret"])) {
    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }
  if (!strpos($_SERVER["HTTP_REFERER"], $_SERVER['HTTP_HOST']) && !isset($_POST["secret"])) {
    header('HTTP/1.0 403 Forbidden', true, 403);
    die;
  }

  unset($_POST["secret"]);
$table =  "diamonds";
$table_search =  "diamonds";
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
    array( 'db' => 'SerialNo', 'dt' => 'SerialNo'),
    array( 'db' => 'ItemID', 'dt' => 'ItemID'),
    array( 'db' => 'WarehouseID', 'dt' => 'WarehouseID'),
    array( 'db' => 'ItemName', 'dt' => 'productName'),
    array( 'db' => 'TotalWeight', 'dt' => 'TotalWeight'),
    array( 'db' => 'ColourID', 'dt' => 'ColourID'),
    array( 'db' => 'ClarityID', 'dt' => 'ClarityID'),
    array( 'db' => 'CutID', 'dt' => 'CutID'),
    array( 'db' => 'SalesPrice', 'dt' => 'SalesPrice'),
    array( 'db' => 'OnhandQnt', 'dt' => 'Qty'),
    array( 'db' => 'SerialName', 'dt' => 'SerialName'),
    array( 'db' => 'Discount', 'dt' => 'Discount'),
    array( 'db' => 'Warehouse', 'dt' => 'Warehouse'),
    array( 'db' => 'OnhandQnt', 'dt' => 'OnhandQnt'),
    array( 'db' => 'ImageName', 'dt' => 'ImageName'),
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
require("ssp.diamonds.php");
echo json_encode(
    SSP::simple( $_REQUEST, $sql_details, $table,$table_search, $primaryKey, $columns )
);
