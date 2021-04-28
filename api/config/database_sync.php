<?php
define('DB_SERVER','localhost');
define('DB_USER','cobol1962');
define('DB_PASS' ,'uMuRZfZsTety6RJ3');
define('DB_NAME', 'costertemp');

$mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
$mysqli->query("SET NAMES 'utf8'");

mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, "1");
mysqli_real_connect($mysqli,DB_SERVER,DB_USER,DB_PASS,DB_NAME);

$sql_details = array(
    'user' => "cobol1962",
    'pass' => "uMuRZfZsTety6RJ3",
    'db'   => "costertemp",
    'host' => 'localhost'
);
echo " ";

if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
