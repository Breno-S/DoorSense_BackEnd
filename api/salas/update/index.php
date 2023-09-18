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

// Parâmetros permitidos pelo endpoint
$allowed_params = ["id", "nome", "numero", "arduino"];

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
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Token de autorização ausente']);
        exit;
    }

    // Chave secreta usada para assinar e verificar o token
    $key = 'arduino';

    try {
        // Decodifica o token usando a chave secreta
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['status' => '401 Unauthorized', 'message' => 'Acesso não autorizado: ' . $e->getMessage()]);
        exit;
    }

    // Verifica se há um body na requisição
    if ($json_data = file_get_contents('php://input')) {

        // Verifica se o JSON é válido
        if (!($data = json_decode($json_data, true))) {
            http_response_code(400); 
            $response['status'] = "400 Bad Request";
            $response['message'] = "JSON inválido";
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


        // Verificação inicial (existe id e mais algum parâmetro?)
        if (isset($data['id']) && (!empty($data['id'])) && (isset($data['nome'])
                                                        || isset($data['numero'])
                                                        || isset($data['arduino'])) ) {
            
            // Verifica se o valor da chave id é numérico
            if (filter_var($data['id'], FILTER_VALIDATE_INT) === false ) {
                http_response_code(400);
                $response['status'] = "400 Bad Request";
                $response['message'] = "Argumento inválido";
                echo json_encode($response);
                exit;
            }

            $id_sala = $data['id'];

            // verifica possível chave de 'nome'
            if (isset($data['nome'])) {
                if (empty($data['nome'])) {
                    $response['status'] = "400 Bad Request";
                    $response['message'] = "Argumento(s) inválido(s)";
                    goto enviar_resposta;
                } else {
                    $nome_sala = $data['nome'];
                }
            } else {
                $nome_sala = null;
            }

            // verifica possível chave de 'numero'
            if (isset($data['numero'])) {
                if (empty($data['numero']) && !(is_string($data['numero']))) {
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
            http_response_code(400);
            $response['status'] = "400 Bad Request";
            $response['message'] = "Parâmetros inválidos";
        }
    } else {
        http_response_code(405);
        $response['status'] = "405 Method Not Allowed";
        $response['message'] = "Método da requisição inválido";
    }
}

enviar_resposta:

// Resposta
echo json_encode($response);

exit;
?>
