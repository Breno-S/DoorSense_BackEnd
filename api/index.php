<?php
include_once '../conexao.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

// Response (deve ser um array associativo)
$response = [];

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

if ($_REQUEST == 'GET') {
    if (isset($_GET['id'])) {
        
    }
} elseif ()


// Resposta
answer($response);

exit;

function answer($data) {
    header("Content-Type: application/json");
    echo json_encode($data);
}