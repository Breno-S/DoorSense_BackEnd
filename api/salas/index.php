<?php
include_once '../include/conexao.php';
include_once '../include/funcoes.php';
require '../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Verifica a presença do token de autorização
    $headers = getallheaders();
    $authorizationHeader = isset($headers['authorization']) ? $headers['authorization'] : '';
    
    // Verifica se o cabeçalho de autorização está no formato "Bearer <token>"
    list(, $token) = explode(' ', $authorizationHeader);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Token de autorização ausente']);
        exit;
    }

    // Chave secreta usada para assinar e verificar o token
    $key = 'arduino';

    try {
        // Decodifica o token usando a chave secreta
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if (!empty($data)) {
            if (isset($data['id'])) {
                $id = intval($data['id']);
                if ($sala = get_sala($conn, $id)) {
                    $response['status'] = "200 OK";
                    $response['message'] = "Sala encontrada";
                    $response['data'] = [
                        "id" => $sala['ID_SALA'],
                        "nome" => $sala['NOME_SALA'],
                        "numero" => $sala['NUMERO_SALA'],
                        "arduino" => $sala['ARDUINO_SALA'],
                        "status" => $sala['STATUS_SALA']
                    ];
                } else {
                    http_response_code(404);
                    $response['status'] = "404 Not Found";
                    $response['message'] = "Sala não encontrada";
                }
            } else {
                $response['status'] = "400 Bad Request";
                $response['message'] = "Parâmetros inválidos";
            }
        } else {
            if ($all_salas = get_all_salas($conn)) {
                $response['status'] = "200 OK";
                $response['message'] = "Todas as salas registradas";

                $total = get_total_salas($conn);

                $response['data'] = [
                    "total" => $total,
                    "salas" => []
                ];

                foreach ($all_salas as $indice => $dados_sala) {
                    array_push($response['data']['salas'], $dados_sala);
                }
            }
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
        exit;
    } 
} else {
    http_response_code(400);
    $response['status'] = "400 Bad Request";
    $response['message'] = "Método da requisição inválido";
}

// Resposta
echo json_encode($response);

exit;
?>
