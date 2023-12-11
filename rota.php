<?php

// Método da requisição
$method = $_SERVER['REQUEST_METHOD'];

	// Se a requisição for uma solicitação OPTIONS, retorna os cabeçalhos permitidos
	// Necessário para evitar CORS no navegador
	if ($method === 'OPTIONS') {
		header("HTTP/1.1 200 OK");
		exit;
	}

// Caminho da requisição
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	// Array para armazenar possíveis identificadores no final do caminho do URL
	$matches = [];

// Pega todos os headers do request
$headers = getallheaders();

	// Transformar as chaves do $headers em lowercase
	foreach ($headers as $key => $value) {
		// Remover a chave original
		unset($headers[$key]);

		// Adicionar a chave em minúsculas com o valor original
		$headers[strtolower($key)] = $value;
	}

// Headers de resposta padrão para toda a API
$allowedOrigin = getenv("ALLOWED_ORIGIN");

if ($allowedOrigin) {
	// Necessário para evitar o CORS no navegador
    header("Access-Control-Allow-Origin: " . $allowedOrigin);
} else {
    header("Access-Control-Allow-Origin: http://localhost:3000");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

/*****************************************************************************/
// GET DOORSENSES

	if ($path === '/doorsenses' || preg_match('/^\/doorsenses\/(\d+)$/', $path, $matches)) {
		
		header("Access-Control-Allow-Methods: GET, OPTIONS");

		include_once "./include/conexao.php";
		include_once "./include/funcoes.php";
		require './vendor/autoload.php';

		switch ($method) {
			case 'GET':
				include_once "./api/doorsenses/index.php";
				break;
			default:
				http_response_code(405);
				echo json_encode([
					'status' => '405 Method Not Allowed',
					'message' => 'Método da requisição inválido'
				]);
				exit;
		}
	}

/*****************************************************************************/
// SALAS
	if ($path === '/salas' || preg_match('/^\/salas\/(\d+)$/', $path, $matches)) {

		include_once "./include/conexao.php";
		include_once "./include/funcoes.php";
		require './vendor/autoload.php';
		
		if ($path === '/salas') {
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
			
			switch ($method) {
				case 'GET':
					include_once "./api/salas/index.php";
					break;
				case 'POST':
					include_once "./api/salas/create/index.php";
					break;
				default:
					http_response_code(405);
					echo json_encode([
						'status' => '405 Method Not Allowed',
						'message' => 'Método da requisição inválido'
					]);
					exit;
			}
		} else {
			header("Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS");

			switch ($method) {
				case 'GET':
					include_once "./api/salas/index.php";
					break;
				case 'PUT':
					include_once "./api/salas/update/index.php";
					break;
				case 'DELETE':
					include_once "./api/salas/delete/index.php";
					break;
				default:
					http_response_code(405);
					echo json_encode([
						'status' => '405 Method Not Allowed',
						'message' => 'Método da requisição inválido'
					]);
					exit;
			}
		}
	} 

/*****************************************************************************/
// LOGIN

if ($path === '/login') {

	
	include_once "./include/conexao.php";
	include_once "./include/funcoes.php";
	require './vendor/autoload.php';
	include_once "./api/login/index.php";
}

/*****************************************************************************/
// REGISTRAR USUÁRIO

if ($path === '/login/register-user') {
	include_once "./include/conexao.php";
	include_once "./include/funcoes.php";
	require './vendor/autoload.php';
	include_once "./api/login/register-user/index.php";
}

/*****************************************************************************/
// ESQUECEU A SENHA

if ($method === 'POST' && $path === '/login/forgot-password') {
	include_once "./include/conexao.php";
	include_once "./include/funcoes.php";
	require './vendor/autoload.php';
	include_once "./api/login/forgot-password/index.php";
}

/*****************************************************************************/
// REDEFINIR A SENHA

if ($method === 'PUT' && $path === '/login/reset-password') {
	include_once "./include/conexao.php";
	include_once "./include/funcoes.php";
	require './vendor/autoload.php';
	include_once "./api/login/reset-password/index.php";
}

/*****************************************************************************/
// NOT FOUND
	
	// Rota não encontrada, erro 404
	http_response_code(404);
	echo json_encode(['Error' => 'Not Found']);
	exit;

?>