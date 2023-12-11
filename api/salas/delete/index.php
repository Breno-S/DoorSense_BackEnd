<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Verifica a presença do cabeçalho de autorização
if (isset($headers['authorization'])) {
    $authorizationHeader = $headers['authorization'];
} else {
    http_response_code(400);
    echo json_encode(['status' => '400 Bad Request', 'message' => 'Cabeçalho de autorização ausente']);
    exit;
}

// Verifica se o cabeçalho de autorização está no formato "Bearer <token>"
if (preg_match('/^Bearer [A-Za-z0-9\-._~+\/]+=*$/', $authorizationHeader)) {
    list(, $token) = explode(' ', $authorizationHeader);
} else {
    http_response_code(401);
    echo json_encode(['status' => '401 Unauthorized', 'message' => 'Token de autorização ausente']);
    exit;
}

// Chave secreta 
$key = 'arduino';

try {
    // Decodifica o token usando a chave secreta
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
    exit;
}

// array associativo.
$response = [];

// ID da sala
if (isset($matches[1])) {
    $pathID = $matches[1];
} else {
    http_response_code(400);
    echo json_encode(['status' => '400 Bad Request', 'message' => 'Identificador ausente']);
    exit;
}


// Verifica se o valor da chave id é numérico
if (filter_var($pathID, FILTER_VALIDATE_INT) === false ) {
    http_response_code(400);
    $response['status'] = "400 Bad Request";
    $response['message'] = "Argumento inválido";
    echo json_encode($response);
    exit;
}

$id_sala = $pathID; 

if (delete_sala($conn, $id_sala)) {
    $response['status'] = "200 OK";
    $response['message'] = "Sala deletada com sucesso";
} else {
    http_response_code(404);
    $response['status'] = "404 Not Found";
    $response['message'] = "Sala não existe";
}

// Resposta
echo json_encode($response);

exit;
?>
