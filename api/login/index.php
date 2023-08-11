<?php
include_once '../conexao.php';
include_once '../funcoes.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_POST['login']) && isset($_POST['senha'])) {
        $login = $_POST['login'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM admin WHERE EMAIL_ADMIN = '$login'";
        $result = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            if (($senha == $row['SENHA_ADMIN'])) {
                $response['status'] = "200 OK";
                $response['message'] = "Login realizado com sucesso";
            } else {
                $response['status'] = "401 Unauthorized";
                $response['message'] = "Credenciais inválidas";
            }
        } else {
            $response['status'] = "401 Unauthorized";
            $response['message'] = "Credenciais inválidas";
        }
    }
} else {
    http_response_code(400);
    $response['status'] = "400 Bad Request";
    $response['message'] = "Método da requisição incorreto";
}

// Resposta
echo json_encode($response);

exit;
?>
