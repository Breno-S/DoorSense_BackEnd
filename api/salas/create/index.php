<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';

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

    if ( (isset($data['nome'])) && (isset($data['numero'])) ) {
        $nome_sala = $data['nome'];
        $numero_sala = intval($data['numero']);

        if ($nova_sala = create_sala($conn, $nome_sala, $numero_sala)) {
                $response['status'] = "200 OK";
                $response['message'] = "Sala adicionada com sucesso";
                $response['data'] = [
                    "id" => $nova_sala['ID_SALA'],
                    "nome" => $nova_sala['NOME_SALA'],
                    "numero" => $nova_sala['NUMERO_SALA'],
                    "status" => $nova_sala['STATUS_SALA']
                ];
        } else {
            $response['status'] = "401 Unauthorized";
            $response['message'] = "Credenciais inválidas";
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