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

// Request com ID na rota -> Obter doorsense específico
if (isset($matches[1])) {
    
    $pathID = $matches[1];

    // Verifica se o valor da chave id é numérico
    if (filter_var($pathID, FILTER_VALIDATE_INT) === false ) {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Argumento inválido";
        echo json_encode($response);
        exit;
    }

    if ($doorsense = get_doorsense($conn, $pathID)) {
        $response['status'] = "200 OK";
        $response['message'] = "DoorSense encontrado";
        $response['data'] = [
            "id" => $doorsense['ID_ARDUINO'],
            "uniqueId" => $doorsense['UNIQUE_ID'],
            "status" => $doorsense['STATUS_ARDUINO'],
            "lastUpdate" => $doorsense['LAST_UPDATE'],
            "sala" => $doorsense['NOME_SALA'],
            "numero" => $doorsense['NUMERO_SALA']
        ];
    } else {
        http_response_code(404);
        $response['status'] = "404 Not Found";
        $response['message'] = "DoorSense não encontrado";
    }
} else {
    // Request ao caminho, sem ID -> Obter todos os Doorsenses
    if ($all_doorsenses = get_all_doorsenses($conn)) {
        $response['status'] = "200 OK";
        $response['message'] = "Todos os DoorSenses registrados";

        $response['data'] = [
            "total" => count($all_doorsenses),
            "doorsenses" => []
        ];

        foreach ($all_doorsenses as $indice => $dados_doorsense) {
            array_push($response['data']['doorsenses'], $dados_doorsense);
        }
    } else {
        http_response_code(500);
        $response['status'] = "500 Internal Server";
        $response['message'] = "Erro ao obter todos os DoorSenses";
    }
}

// Resposta
echo json_encode($response);

exit;
?>
