<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';
require '../../../vendor/autoload.php'; // Certifique-se de incluir o autoload do Firebase JWT

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// array associativo.
$response = [];

// Verifica o método da requisição.
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'DELETE') {
    // Pega todos os headers do request
    $headers = getallheaders();

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
        $token = false;
    }

    if (!$token) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Token de autorização ausente']);
        exit;
    }

    // Chave secreta 
    $key = 'arduino';

    try { 
         // Decodifica o token usando a chave secreta
         $decoded = JWT::decode($token, new Key($key, 'HS256'));


        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if ($data !== null && is_array($data)) { //Se são válidos e se são um array.
            if (isset($data['id']) && is_numeric($data['id'])) { //se o 'id' está presente nos dados e se o valor associado a essa chave é um número válido.
                $id_sala = intval($data['id']); 

                if (delete_sala($conn, $id_sala)) {
                    $response['status'] = "200 OK";
                    $response['message'] = "Sala deletada com sucesso";
                } else {
                    http_response_code(500);
                    $response['status'] = "500 Internal Server Error"; // caso de erro interno.
                    $response['message'] = "Erro ao deletar sala: " . mysqli_error($conn);
                }
            } else {
                http_response_code(400);
                $response['status'] = "400 Bad Request"; // caso a validação de entrada falhe.
                $response['message'] = "ID da sala inválido";
            }
        } else {
            http_response_code(400);
            $response['status'] = "400 Bad Request";
            $response['message'] = "JSON inválido";
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
        exit;
    }
} else {
    http_response_code(405);
    $response['status'] = "405 Method Not Allowed"; //solicitações de método não permitidas.
    $response['message'] = "Método da requisição inválido";
}

// Resposta
echo json_encode($response);

exit;
?>
