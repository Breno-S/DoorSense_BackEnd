<?php
include_once '../../../include/conexao.php';
include_once '../../../include/funcoes.php';
require '../../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$allowedOrigin = getenv("ALLOWED_ORIGIN");

$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$email = $row['EMAIL_ADMIN'];

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

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
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

    // Verifica se o argumento é um email válido
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

         //token JWT
         $key = 'senha';
         $tokenId = base64_encode(random_bytes(32));
         $issuedAt = time();
         $expire = $issuedAt + 600; //


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

        // Link
        $link = 'https://example.com?t=' . $token;

        // Configuração e envio do e-mail
        $mail = new PHPMailer();

        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // descomente se quiser ler os logs
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'doorsenseteste@gmail.com'; 
            $mail->Password = 'zvtj djjl prvm gzzm'; 
            $mail->Port = 587;

            $mail->setFrom('doorsenseteste@gmail.com', 'AcessoTech'); 
            $mail->addAddress($email); 

            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de senha';
            $mail->Body = 'Prezado(a),<br><br>Você solicitou a redefinição de senha.<br><br>Para continuar o processo de recuperação de senha, clique no link abaixo ou cole o endereço no seu navegador:<br><br><a href="' . $link . '">' . $link . '</a><br><br>Se você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.<br><br>';
            $mail->AltBody = 'Prezado(a),\n\nVocê solicitou a redefinição de senha.\n\nPara continuar o processo de recuperação de senha, clique no link abaixo ou cole o endereço no seu navegador:\n\n' . $link . '\n\nSe você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.\n\n';

            $mail->send();

            $response['status'] = "200 OK";
            $response['message'] = "E-mail de recuperação de senha enviado com sucesso";
        } catch (Exception $e) {
            http_response_code(500);
            $response['status'] = "500 Internal Server Error";
            $response['message'] = "Erro no envio de e-mail: " . $e->getMessage();
        }
    } else {
        http_response_code(400);
        $response['status'] = "400 Bad Request";
        $response['message'] = "E-mail fornecido é inválido";
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
