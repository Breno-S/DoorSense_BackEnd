<?php
session_start();
include_once("conexao.php");

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['password'];

// Preparação da consulta
$query = "SELECT * FROM admin WHERE EMAIL_ADMIN = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);

$result_usuario = mysqli_stmt_get_result($stmt);

if ($row_usuario = mysqli_fetch_assoc($result_usuario)) {
    $senha_banco = $row_usuario['SENHA_ADMIN'];

    // Verifique a senha fornecida pelo usuário usando password_verify
    if (password_verify($senha, $senha_banco)) {
        $_SESSION['ID_ADMIN'] = $row_usuario['ID_ADMIN']; // Armazena o ID na sessão
        header('Location: home.php');
        exit();
    } else {
        echo 'Credenciais incorretas.';
    }
} else {
    echo 'Usuário não encontrado.';
}
?>
