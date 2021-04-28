<?php

// DB table to use
session_start();
header("Access-Control-Allow-Origin: *");
include "config/database.php";
$table =  "invoices_view";
$table_search =  "invoices_view";
$db_database = "costerdiamonds";
$db_user = "cobol1962";
$db_password = "uMuRZfZsTety6RJ3";
// Table's primary key
$primaryKey = 'invoiceid';
define('DB_SERVER','localhost');
define('DB_USER','cobol1962');
define('DB_PASS' ,'uMuRZfZsTety6RJ3');
define('DB_NAME', 'costerdiamonds');


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
    array( 'db' => 'invoiceid', 'dt' => 'invoiceid'),
    array( 'db' => 'version', 'dt' => 'version'),
    array( 'db' => 'customerid', 'dt' => 'customerid'),
    array( 'db' => 'showroom', 'dt' => 'showroom'),
    array( 'db' => 'salesPerson', 'dt' => 'salesPerson'),
    array( 'db' => 'tourNo', 'dt' => 'tourNo'),
    array( 'db' => 'total', 'dt' => 'total'),

    array( 'db' => 'discountAmount', 'dt' => 'discountAmount'),
    array( 'db' => 'discountApproved', 'dt' => 'discountApproved'),
    array( 'db' => 'discountApprovedName', 'dt' => 'discountApprovedName'),
    array( 'db' => 'dueAmount', 'dt' => 'dueAmount'),
    array( 'db' => 'date', 'dt' => 'date'),
    array( 'db' => 'saledate', 'dt' => 'saledate'),
    array( 'db' => 'pdf', 'dt' => 'pdf'),
    array( 'db' => 'documentName', 'dt' => 'documentName'),
    array( 'db' => 'documentLanguages', 'dt' => 'documentLanguages'),
    array( 'db' => 'status', 'dt' => 'status'),
    array( 'db' => 'vatExcluded', 'dt' => 'vatExcluded'),
    array( 'db' => 'vat', 'dt' => 'vat'),
    array( 'db' => 'vatRefund', 'dt' => 'vatRefund'),
    array( 'db' => 'locked', 'dt' => 'locked'),
    array( 'db' => 'reference', 'dt' => 'reference'),
    array( 'db' => 'remark', 'dt' => 'remark'),
    array( 'db' => 'isproform', 'dt' => 'isproform'),
    array( 'db' => 'shopifyid', 'dt' => 'shopifyid'),
    array( 'db' => 'customer', 'dt' => 'customer'),
    array( 'db' => 'touroperater', 'dt' => 'touroperater'),
    array( 'db' => 'total', 'dt' => 'startingTotal'      ),
      array( 'db' => 'initialdue', 'dt' => 'initialdue'),
      array( 'db' => 'currentdue', 'dt' => 'currentdue'),
      array( 'db' => 'isDue', 'dt' => 'isDue'),
      array( 'db' => 'totaldiscount', 'dt' => 'totaldiscount'),
      array( 'db' => 'isDiscounted', 'dt' => 'isDiscounted'),
      array( 'db' => 'paid', 'dt' => 'paid'),
      array( 'db' => 'invoiceid', 'dt' => 'due',
              "formatter" => function($d, $row) {
                $mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
                $mysqli->query("SET NAMES 'utf8'");
                mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, "On");
                mysqli_real_connect($mysqli,DB_SERVER,DB_USER,DB_PASS,DB_NAME);
                $ssum = "select sum(original) as paid ,IFNULL(version,'') as version from invoice_payments where  invoiceid='".$row["invoiceid"]."' and version=''";
                $rsum = $mysqli->query($ssum);
                $initialdue = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
                $ssum = "select sum(original) as paid from invoice_payments where  invoiceid='" . $row["invoiceid"] . "'";
                $row["due"] = "";
                $rsum = $mysqli->query($ssum);
                $currentdue = floatval($row["dueAmount"]) - floatval(mysqli_fetch_assoc($rsum)["paid"]);
                  $due = "";

                  if ($initialdue == 0) {
                    $due .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed</span>";
                  }

                  if ($initialdue < 0) {
                    $due .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Completed, change " . number_format($initialdue * -1, 2, ',', '.') . "<span>";
                  }


                  if ($initialdue > 0 && $currentdue > 0) {
                    $due .= "<span style='min-width:200px;color:red;' onclick='showPayments(this);'>Initial " . number_format($initialdue, 2, ',', '.') . " Current " . number_format($currentdue, 2, '.', ',') . "</span>";
                  }

                  if ($initialdue > 0 && $currentdue == 0) {
                    $due .= "<span style='min-width:200px;color:black;' onclick='showPayments(this);'>Initial " . number_format($initialdue, 2, ',', '.') . " -> Completed</span> ";
                  }
                  if ($initialdue > 0 && $currentdue < 0) {
                    $due .= "<span style='min-width:200px;color:green;' onclick='showPayments(this);'>Initial " . number_format($initialdue, 2, ',', '.') . " -> Completed, change " .  number_format($res["currentdue"] * -1, 2, '.', ','). "</span>";
                  }
                  $due .= "<realvalue realvalue='" . floatval($row["dueAmount"]) . "'></realvalue>";
                  $due .= "<br />&#8364; " . number_format(floatval($row["dueAmount"]), 2, ',', '.');
                  $ss = "select DATE(date) as date,original as paid,payment, version from invoice_payments
                          inner join paymentMethods on invoice_payments.paymentID=paymentMethods.PaymentID
                           where  invoiceid='" .$row["invoiceid"]."'
                          ";

                  $r1 = $mysqli->query($ss);
                  $due .= "<div payments style='display:none;min-width:100%;text-align:left;'>";
                  while ($rr = mysqli_fetch_assoc($r1)) {

                    if ($rr["paid"] > 0) {
                      if ($rr["version"] == "") {
                        $rr["version"] = "&nbsp;";
                      }
                      $due .= "<pay>" . $rr["version"] . "|" . $rr["date"] . "|" . $rr["payment"] . "| &#8364; " . number_format($rr["paid"], 2, '.', ',') . "</pay>";
                    }

                  }
                $due .= "<invoice>" . $row["invoiceid"] . "</invoice>" . "<version>" . $row["version"] . "</version><pdf>" . $row["pdf"] . "</pdf>";

                $due .= "</div>";
                  return  base64_encode($due);
                }
              ),
              array( 'db' => 'dueAmount', 'dt' => 'dueAmount'),
              array( 'db' => 'discount', 'dt' => 'discount',
              "formatter" => function($d, $row) {
                $ssum = "select sum(quantity * SalesPrice) as startingTotal from invoice_body where  invoiceid='".$row["invoiceid"]."'";
                $mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
                $mysqli->query("SET NAMES 'utf8'");
                mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, "On");
                mysqli_real_connect($mysqli,DB_SERVER,DB_USER,DB_PASS,DB_NAME);
                $rsum = $mysqli->query($ssum);
                $rs = mysqli_fetch_assoc($rsum);
                $startingTotal = floatval($rs["startingTotal"]);
                $dsc = "";
                if ((floatval($startingTotal) - floatval($row["dueAmount"])) > 0) {
                  $raz = floatval($startingTotal) - floatval($row["dueAmount"]);
                  $dsc =  "(" .   number_format(floor((floatval($raz) / floatval($startingTotal) * 100)), 0, '.', ',') . "%)<br />
                  <realvalue realvalue='" .(floatval($startingTotal) - floatval($row["dueAmount"])) . "'>&#8364; " . number_format(floatval($startingTotal) - floatval($row["dueAmount"]), 2, ',', '.') . "</realvalue>";
                } else {
                  $dsc = "";
                  $dsc = "<realvalue style='display:none;' realvalue='0'></realvalue>";
                }
                  return base64_encode($dsc);
                }
              )
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
require("ssp.invoices_list.php");

echo json_encode(
    SSP::simple( $_REQUEST, $sql_details, "invoices_view", "invoices_view", $primaryKey, $columns )
);
