<?php
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS' ,'Rm150620071010');
define('DB_NAME', 'costerdiamonds');

$mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
$mysqli->query("SET NAMES 'utf8'");

mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, "On");
mysqli_real_connect($mysqli,DB_SERVER,DB_USER,DB_PASS,DB_NAME);

$sql_details = array(
    'user' => "root",
    'pass' => "Rm150620071010",
    'db'   => "costerdiamonds",
    'host' => 'localhost'
);
echo " ";

if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
