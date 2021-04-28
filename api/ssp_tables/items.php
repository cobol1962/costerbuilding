<?php

// DB table to use
session_start();

include ("../config/database.php");

$table =  "itemsTableView";
$db_database = "costerdiamonds";
$db_user = "spydernet";
$db_password = "Rm150620071010";
// Table's primary key
$primaryKey = 'SerialNo';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id', 'dt' => 0),
	array(
        'db'        => 'id',
        'dt'        => 1,
		'database'  => $db_database,
		'user'  => $db_user,
		'password'  => $db_password,
		'source' => "erp",
        'image' => function( $d, $row ) {
			 $dbname = "db737328123";
			$sql = "select `" . $dbname . "`.`cards`.*,`" . $dbname . "`.
			`categories`.name as categoryName,`imgs`.* from `" . $dbname . "`.`cards`
			left join ( SELECT `card_id` AS gid , MIN(`id`), `image` AS min_imgsort FROM `" . $dbname . "`.`cards_images` GROUP BY `gid` )
			AS imgs on `" . $dbname . "`.`cards`.`id`=`imgs`.`gid`
			left join `" . $dbname . "`.`categories` on `" . $dbname . "`.`cards`.`category`=`" . $dbname . "`.`categories`.`id`
			where `" . $dbname . "`.`cards`.`id`='" . $d . "'";
		    return  $sql;
        }
    ),
	array(
		'db' => 'name',
		'dt' => 2
  ),
	array(
		'db' => 'category',
		'dt'        => 3
	),
	array(
		'db' => 'OverallVotes',
		'dt' => 4),
	array(
		'db' => 'LitRank',
		'dt' => 5)
	/*,
    array( 'db' => 'name',  'dt' => 2 ),
	array(
        'db'        => 'quantity',
        'dt'        => 3,
        'formatter' => function( $d, $row ) {
			return number_format($d,2);
        }
    ),
	array(
        'db'        => 'type',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
			$rst = (($d == "0") ? "Goods" : "Service");
            return  $rst;
        }
    ),

	array(
        'db'        => 'inventory',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
			$rst = (($d == "1") ? "Yes" : "No");
            return  $rst;
        }
    ),
	array( 'db' => 'sku',  'dt' => 7 ),
	array( 'db' => 'bar_code',  'dt' => 8 ),
	array(
        'db'        => 'tax_group',
        'dt'        => 9,
        'formatter' => function( $d, $row ) {
			return "<div style='max-width:180px;position:absolute;'><select onchange='setTDHeight(this);' chosen_tax_products multiple='true' chosen='" . $d . "' id='tax_products'></select></div>";
        }
    ),
	array(
        'db'        => 'price_before_tax',
        'dt'        => 10,
        'formatter' => function( $d, $row ) {
			return number_format($d,2);
        }
    ),

	array(
        'db'        => 'good_id',
        'dt'        => 11,
        'formatter' => function( $d, $row ) {
			$rst = "<i  onclick='deleteGood(this);' class='glyphicon glyphicon-trash' aria-hidden='true'></i>
							<i  edit onclick='editGood(this);' class='glyphicon glyphicon-edit' aria-hidden='true'></i>
							<i  style='display:none;' additional onclick='additionalsGood(this);' class='glyphicon glyphicon-new-window'></i>
							<button type='button' style='display:none;' save class='btn btn-success' onclick='saveGood(this, true);'>Save</button>
							<button type='button' style='display:none;'  cancel class='btn btn-danger' onclick='cancelUpdatedGood(this);'>Cancel</button>";
            return  $rst;
        }
    )*/


);

// SQL server connection information
$sql_details = array(
    'user' => "spydernet",
    'pass' => "Rm150620071010",
    'db'   => "costerdiamonds",
    'host' => 'localhost'
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
require("ssp.class.php");

echo json_encode(
    SSP::simple( $_REQUEST, $sql_details, $table, $primaryKey, $columns )
);
