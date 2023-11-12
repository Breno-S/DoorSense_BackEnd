<?php

phpinfo();

$hostname = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');
$port     = getenv('DB_PORT');
$ca_path  = getenv('DB_CA_CERT_PATH');

echo $hostname;
echo $username;
echo $password;
echo $database;
echo $port;
echo $ca_path;
?>
