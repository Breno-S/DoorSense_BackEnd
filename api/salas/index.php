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

// Chave secreta usada para assinar e verificar o token
$key = 'arduino';

try {
    // Decodifica o token usando a chave secreta
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
    exit;
} 

// Response (deve ser um array associativo)
$response = [];

// Request com ID -> Obter sala específica
if (isset($matches[1])) {
    $pathID = $matches[1];
    
    // Verifica se o valor do id é numérico
    if (filter_var($pathID, FILTER_VALIDATE_INT) === false ) {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Argumento inválido";
        echo json_encode($response);
        exit;
    }

    if ($sala = get_sala($conn, $pathID)) {
        $response['status'] = "200 OK";
        $response['message'] =   "Sala encontrada";
        $response['data'] = [
            "id" => $sala['ID_SALA'],
            "nome" => $sala['NOME_SALA'],
            "numero" => $sala['NUMERO_SALA'],
            "doorsense" => $sala['UNIQUE_ID'],
            "status" => $sala['STATUS_ARDUINO']
        ];
    } else {
        http_response_code(404);
        $response['status'] = "404 Not Found";
        $response['message'] = "Sala não encontrada";
    }
} else {
    // Request sem ID -> Obter todas as salas
    if ($all_salas = get_all_salas($conn)) {
        $response['status'] = "200 OK";
        $response['message'] = "Todas as salas registradas";

        $response['data'] = [
            "total" => count($all_salas),
            "salas" => []
        ];

        foreach ($all_salas as $indice => $dados_sala) {
            array_push($response['data']['salas'], $dados_sala);
        }
    } else {
        http_response_code(500);
        $response['status'] = "500 Internal Server";
        $response['message'] = "Erro ao obter todas as salas";
    }
}

// Resposta
echo json_encode($response);

exit;
?>
