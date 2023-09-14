<?php
include_once '../../include/conexao.php';
include_once '../../include/funcoes.php';
require '../../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

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

    // Chave secreta usada para assinar e verificar o token
    $key = 'arduino';

    try {
        // Decodifica o token usando a chave secreta
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if (isset($data['id']) && (!empty($data['id'])) && (isset($data['nome'])
                                                        || isset($data['numero'])
                                                        || isset($data['arduino'])) ) {
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

            // verifica possível chave de 'arduino'
            if (isset($data['arduino'])) {
                if (empty($data['arduino'])) {
                    $response['status'] = "400 Bad Request";
                    $response['message'] = "Parâmetros inválidos";
                    goto enviar_resposta;
                } else {
                    $arduino_sala = $data['arduino'];
                }
            } else {
                $arduino_sala = null;
            }

            // Verificar se já existe uma sala com o mesmo nome ou número no banco de dados.
            if (sala_existe_update($conn, $id_sala, $nome_sala, $numero_sala)) {
                $response['status'] = "400 Bad Request";
                $response['message'] = "Sala com mesmo nome ou número já existe no banco de dados.";
            } else {
                $update_values = [];
                $update_values['id'] = $id_sala;
                $update_values['nome'] = $nome_sala;
                $update_values['numero'] = $numero_sala;
                $update_values['arduino'] = $arduino_sala;

                $atualizacao = update_sala($conn, $update_values);

                if ($atualizacao) {
                    $response['status'] = "200 OK";
                    $response['message'] = "Sala atualizada com sucesso";
                    $response['data'] = $atualizacao;
                } else {
                    $response['status'] = "500 Internal Server Error";
                    $response['message'] = "Erro ao atualizar sala";
                }
            }
        } else {
            $response['status'] = "400 Bad Request";
            $response['message'] = "Parâmetros inválidos";
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

enviar_resposta:

// Resposta
echo json_encode($response);

exit;
?>
