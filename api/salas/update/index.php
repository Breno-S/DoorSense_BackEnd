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

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// No código que recebe o JSON e faz a chamada da função update_sala:
if ($method == 'PUT') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if ( (!empty($data['id'])) && ( isset($data['nome']) ||
                                    isset($data['numero']) ||
                                    isset($data['status'])  )
        )   
    {
        $id_sala = intval($data['id']);

        // verifica possível chave de 'nome'
        if (isset($data['nome'])) {
            if (empty($data['nome'])) {
                $response['status'] = "400 Bad Request";
                $response['message'] = "Parâmetros inválidos";
                goto enviar_resposta;
            } else {
                $nome_sala = $data['nome'];
            }
        } else {
            $nome_sala = null;
        }

        // verifica possível chave de 'numero'
        if (isset($data['numero'])) {
            if (empty($data['numero'])) {
                $response['status'] = "400 Bad Request";
                $response['message'] = "Parâmetros inválidos";
                goto enviar_resposta;
            } else {
                $numero_sala = $data['numero'];
            }
        } else {
            $numero_sala = null;
        }

        // verifica possível chave de 'status'
        if (isset($data['status'])) {
            if (empty($data['status'])) {
                $response['status'] = "400 Bad Request";
                $response['message'] = "Parâmetros inválidos";
                goto enviar_resposta;
            } else {
                $status_sala = $data['status'];
            }
        } else {
            $status_sala = null;
        }

        $update_values = [];
        $update_values['id'] = $id_sala;
        $update_values['nome'] = $nome_sala;
        $update_values['numero'] = $numero_sala;
        $update_values['status'] = $status_sala;
        

        // $numero_sala = empty($data['numero']) ? null : intval($data['numero']);
        // $status_sala = empty($data['status']) ? null : $data['status'];

        $atualizacao_sucesso = update_sala($conn, $update_values);
        
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


enviar_resposta:

// Resposta
echo json_encode($response);

exit;
?>
