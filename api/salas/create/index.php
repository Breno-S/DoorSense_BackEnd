<?php
include_once '../../../include/conexao.php';
include_once '../../../include/funcoes.php';
require '../../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$allowedOrigin = getenv("ALLOWED_ORIGIN");

// Headers
// Verifique se o valor está presente e defina o cabeçalho Access-Control-Allow-Origin
if ($allowedOrigin) {
    header("Access-Control-Allow-Origin: " . $allowedOrigin);
} else {
    header("Access-Control-Allow-Origin: http://localhost:3000");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Parâmetros permitidos pelo endpoint
$allowed_params = ["nome", "numero"];

// Array associativo.
$response = [];

// Verifica o método da requisição.
$method = $_SERVER['REQUEST_METHOD'];

// Se a requisição for uma solicitação OPTIONS, retorne os cabeçalhos permitidos
if ($method === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

if ($method == 'POST') {
    // Pega todos os headers do request
    $headers = getallheaders();

    // Transformar as chaves do $headers em lowercase
    foreach ($headers as $key => $value) {
        // Remover a chave original
        unset($headers[$key]);
    
        // Adicionar a chave em minúsculas com o valor original
        $headers[strtolower($key)] = $value;
    }

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

        // Verifica se tem os parâmetros obrigatórios
        if (isset($data['nome']) && isset($data['numero'])) {

            // Verifica se os parâmetros obrigatórios são strings
            if (is_string($data['nome']) && is_string($data['numero'])) {
                $nome_sala = trim($data['nome']); //Remove espaço no início e final de uma string
                $numero_sala = ($data['numero'] === "") ? null : $data['numero'];
                
                // Verificar se já existe uma sala com o mesmo nome e número no banco de dados
                if (sala_existe_create($conn, $nome_sala, $numero_sala)) {
                    http_response_code(400);
                    $response['status'] = "400 Bad Request"; 
                    $response['message'] = "Sala com mesmo nome e número já existe no banco de dados.";
                } else {
                    if ($nova_sala = create_sala($conn, $nome_sala, $numero_sala)) {
                        $response['status'] = "200 OK"; 
                        $response['message'] = "Sala adicionada com sucesso";
                        $response['data'] = [
                            "id" => $nova_sala['ID_SALA'],
                            "nome" => $nova_sala['NOME_SALA'],
                            "numero" => $nova_sala['NUMERO_SALA'],
                            "arduino" => null,
                            "status" => null
                        ];
                    } else {
                        $response['status'] = "500 Internal Server Error";
                        $response['message'] = "Erro ao criar a sala";
                    }
                }
            } else {
                http_response_code(400);
                $response['status'] = "400 Bad Request"; // requisição do cliente não está correta
                $response['message'] = "Argumento(s) inválido(s)";
            }
        } else {
            http_response_code(400);
            $response['status'] = "400 Bad Request"; // requisição do cliente não está correta
            $response['message'] = "Parâmetro obrigatório ausente";
        }
    } else {
        http_response_code(400);
        $response['status'] = "400 Bad Request";  // requisição do cliente não está correta
        $response['message'] = "Requisição sem body";
    }
} else {
    http_response_code(405);
    $response['status'] = "405 Method Not Allowed";  
    $response['message'] = "Método da requisição inválido";
}

// Resposta
echo json_encode($response);

exit;
?>
