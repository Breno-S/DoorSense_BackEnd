<?php
$hostname = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');
$port = getenv('DB_PORT');
$ca_path = getenv('DB_CA_CERT_PATH');

// Inicializar o objeto mysqli
$conn = mysqli_init();

// Configurações de SSL/TLS
mysqli_ssl_set($conn, NULL, NULL, $ca_path, NULL, NULL);

// Estabelecer a conexão
if (!mysqli_real_connect($conn, $hostname, $username, $password, $database, $port)) {
    die("Conexão falhou: " . mysqli_connect_error());
}
?>
