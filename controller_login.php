<?php
session_start();
include_once("conexao.php");

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['password'];

$senha_md5 = md5($senha);

// $result_usuario = "SELECT * FROM admin WHERE EMAIL_ADMIN = '$email'";
// $result_usuario = mysqli_query($conn, $result_usuario);

// Preparação da consulta
$query = "SELECT * FROM admin WHERE EMAIL_ADMIN = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);

// $row_usuario = mysqli_fetch_assoc($result_usuario);

$result_usuario = mysqli_stmt_get_result($stmt);

if ($row_usuario = mysqli_fetch_assoc($result_usuario)) {
    $senha_banco = $row_usuario['SENHA_ADMIN'];
    $id = $row_usuario['ID_ADMIN'];

    if ($senha_md5 == $senha_banco && $email == $row_usuario['EMAIL_ADMIN']) {
        $_SESSION['ID_ADMIN'] = $id; // Armazena o ID na sessão
        header('Location: home.php');
        exit();
    } else {
        echo 'Senha incorreta.';
    }
} else {
    echo 'Usuário não encontrado.';
    
}
?>
