<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';
require 'vendor/autoload.php'; // Certifique-se de incluir o autoload do Firebase JWT

use \Firebase\JWT\JWT;

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Array associativo.
$response = [];

// Verifica o método da requisição.
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Verifica a presença do token de autorização
    $headers = getallheaders();
    $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

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
        $decoded = JWT::decode($token, $key);

        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if ($data !== null && is_array($data)) { //Se são válidos e se são um array.
            if (isset($data['nome']) && isset($data['numero']) && is_string($data['nome']) && is_string($data['numero'])) {
                $nome_sala = trim($data['nome']); //Remove espaço no início e final de uma string
                $numero_sala = trim($data['numero']);

                if ($nova_sala = create_sala($conn, $nome_sala, $numero_sala)) {
                    $response['status'] = "200 OK";
                    $response['message'] = "Sala adicionada com sucesso";
                    $response['data'] = [
                        "id" => $nova_sala['ID_SALA'],
                        "nome" => $nova_sala['NOME_SALA'],
                        "numero" => $nova_sala['NUMERO_SALA'],
                        "arduino" => null,
                        "status" => null
                    ];
                } else {
                    $response['status'] = "500 Internal Server Error";
                    $response['message'] = "Erro ao criar a sala";
                }
            } else {
                $response['status'] = "400 Bad Request"; // requisição do cliente não está correta
                $response['message'] = "Parâmetros inválidos";
            }
        } else {
            $response['status'] = "400 Bad Request";  // requisição do cliente não está correta
            $response['message'] = "JSON inválido";
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
        exit;
    }
} else {
    http_response_code(405);
    $response['status'] = "405 Method Not Allowed";  
    $response['message'] = "Método da requisição inválido";
}

// Resposta
echo json_encode($response);

exit;
?>
