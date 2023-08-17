<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// No código que recebe o JSON e faz a chamada da função update_sala:
if ($method == 'PUT') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (
        isset($data['id']) &&
        isset($data['nome']) &&
        isset($data['numero']) &&
        isset($data['status'])
    ) {
        $id_sala = intval($data['id']);
        $nome_sala = $data['nome'];
        $numero_sala = intval($data['numero']);
        $status_sala = $data['status'];

        $atualizacao_sucesso = update_sala($conn, $id_sala, $nome_sala, $numero_sala, $status_sala);
        
        if ($atualizacao_sucesso) {
            $response['status'] = "200 OK";
            $response['message'] = "Sala atualizada com sucesso";
        } else {
            $response['status'] = "500 Internal Server Error";
            $response['message'] = "Erro ao atualizar sala";
        }
    } else {
        $response['status'] = "400 Bad Request";
        $response['message'] = "Parâmetros inválidos";
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
