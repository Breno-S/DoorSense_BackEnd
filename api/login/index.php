<?php
include_once '../include/conexao.php';
include_once '../include/funcoes.php';
require '../../vendor/autoload.php'; // autoload do Firebase JWT

use \Firebase\JWT\JWT;

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Parâmetros permitidos pelo endpoint
$allowed_params = ["username", "password"];

// Response
$response = [];

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Verifica se há um body na requisição
    if (!($json_data = file_get_contents('php://input'))) {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Requisição sem body";
        echo json_encode($response);
        exit;
    }
    
    // Verifica se o JSON é válido
    if (!($data = json_decode($json_data, true))) {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Body mal estruturado";
        echo json_encode($response);
        exit;
    }

    // Obtém todas as chaves do JSON do body
    $body_params = array_keys($data);

    // Verifica se há chaves inválidas na requisição
    if (array_diff($body_params, $allowed_params)) {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Parâmetros desconhecidos na requisição";
        echo json_encode($response);
        exit;
    }

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        if (login($conn, $username, $password)) {
            //token JWT
            $key = 'arduino';
            $tokenId = base64_encode(random_bytes(32));
            $issuedAt = time();
            $expire = $issuedAt + 86400; // 1 dia de validade

            //criação do token
            $tokenData = [
                'iat'  => $issuedAt,
                'jti'  => $tokenId,
                'exp'  => $expire,
                'data' => [
                    'username' => $username
                ]
            ];

            $token = JWT::encode($tokenData, $key, 'HS256');

            $response = [
                'status' => "200 OK",
                'message' => "Login realizado com sucesso",
                'token' => $token
            ];
        } else {
            http_response_code(401); 
            $response['status'] = "401 Unauthorized";
            $response['message'] = "Credenciais inválidas";
        }
    } else {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "Parâmetro obrigatório ausente";
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
