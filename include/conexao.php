<?php
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');
$port = getenv('DB_PORT');

// Inicializar o objeto mysqli
$conn = mysqli_init();

// Configurações de SSL/TLS
mysqli_ssl_set($conn, NULL, NULL, "C:\\Users\\breno\\Downloads\\DigiCertGlobalRootCA.crt.pem", NULL, NULL);

// Estabelecer a conexão
if (!mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
    die("Conexão falhou: " . mysqli_connect_error());
}
?>
