<?php
include_once '../include/conexao.php';
include_once '../include/funcoes.php';
require '../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Parâmetros permitidos pelo endpoint
$allowed_params = ["id"];

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {

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
        
        // Request com body -> Obter sala específica
        if (isset($data['id'])) {

            // Verifica se o valor da chave id é numérico
            if (filter_var($data['id'], FILTER_VALIDATE_INT) === false ) {
                http_response_code(400);
                $response['status'] = "400 Bad Request";
                $response['message'] = "Argumento inválido";
                echo json_encode($response);
                exit;
            }

            $id = $data['id'];

            if ($sala = get_sala($conn, $id)) {
                $response['status'] = "200 OK";
                $response['message'] =   "Sala encontrada";
                $response['data'] = [
                    "id" => $sala['ID_SALA'],
                    "nome" => $sala['NOME_SALA'],
                    "numero" => $sala['NUMERO_SALA'],
                    "arduino" => $sala['ARDUINO_SALA'],
                    "status" => $sala['STATUS_SALA']
                ];
            } else {
                http_response_code(404);
                $response['status'] = "404 Not Found";
                $response['message'] = "Sala não encontrada";
            }
        } else {
            // roda quando o valor da chave id é null
            http_response_code(400);
            $response['status'] = "400 Bad Request";
            $response['message'] = "Argumento inválido";
        }
    } else {
        // Request sem body -> Obter sala específica
        if ($all_salas = get_all_salas($conn)) {
            $response['status'] = "200 OK";
            $response['message'] = "Todas as salas registradas";

            $total = get_total_salas($conn);

            $response['data'] = [
                "total" => $total,
                "salas" => []
            ];

            foreach ($all_salas as $indice => $dados_sala) {
                array_push($response['data']['salas'], $dados_sala);
            }
        } else {
            http_response_code(500);
            $response['status'] = "500 Internal Server";
            $response['message'] = "Erro ao obter todas as salas";
        }
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
