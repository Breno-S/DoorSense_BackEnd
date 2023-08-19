<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Response (deve ser um array associativo)
$response = [];

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'DELETE') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['id'])) {
        $id_sala = intval($data['id']); 

        if (delete_sala($conn, $id_sala)) {
            $response['status'] = "200 OK";
            $response['message'] = "Sala deletada com sucesso";
        } else {
            $response['status'] = "500 Internal Server Error";
            $response['message'] = "Erro ao deletar sala: " . mysqli_error($conn);
        }
    } else {
        $response['status'] = "400 Bad Request";
        $response['message'] = "ID da sala inválido";
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
