<?php
include_once '../include/conexao.php';
include_once '../include/funcoes.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        if (login($conn, $username, $password)) {
            $response = [
                'status' => "200 OK",
                'message' => "Login realizado com sucesso"
            ];
        } else {
            $response['status'] = "401 Unauthorized";
            $response['message'] = "Credenciais inválidas";
        }
    } else {
        $response['status'] = "401 Unauthorized";
        $response['message'] = "Credenciais inválidas";
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
