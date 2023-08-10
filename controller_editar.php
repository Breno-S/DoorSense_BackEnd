<?php
session_start();
include_once('conexao.php');

var_dump($_POST);

if (isset($_POST['id_sala'])) {
    $id_sala = $_SESSION['sala'];

    // Verificar se o usuário está logado
    if (!isset($_SESSION['ID_ADMIN'])) {
        header('Location: login.html');
        exit();
    }

    $nomeSala = filter_input(INPUT_POST, 'nomeSala', FILTER_SANITIZE_STRING);
    $numeroSala = filter_input(INPUT_POST, 'numeroSala', FILTER_SANITIZE_NUMBER_INT);
    $statusSala = filter_input(INPUT_POST, 'statusSala', FILTER_SANITIZE_STRING);

    // Verificar se já existe uma sala com o mesmo nome e número
    $verificarSala = "SELECT * FROM sala WHERE (NOME_SALA = ? OR NUMERO_SALA = ?) AND ID_SALA != ?";
    $stmtVerificacao = mysqli_prepare($conn, $verificarSala);
    mysqli_stmt_bind_param($stmtVerificacao, "ssi", $nomeSala, $numeroSala, $id_sala);
    mysqli_stmt_execute($stmtVerificacao);
    $resultadoVerificacao = mysqli_stmt_get_result($stmtVerificacao);

    if (mysqli_num_rows($resultadoVerificacao) > 0) {
        $_SESSION['msg'] = "<p class='text-center' style='color: red;'>Já existe uma sala com o mesmo nome ou número.</p>";
        header('Location: ./home.php');
        exit();
    } else {
        // Atualizar as informações no banco
        $atualizarSala = "UPDATE sala SET NOME_SALA = ?, NUMERO_SALA = ?, STATUS_SALA = ? WHERE ID_SALA = ?";
        $stmtAtualizacao = mysqli_prepare($conn, $atualizarSala);
        mysqli_stmt_bind_param($stmtAtualizacao, "sssi", $nomeSala, $numeroSala, $statusSala, $id_sala);
        $resultadoAtualizacao = mysqli_stmt_execute($stmtAtualizacao);

        if ($resultadoAtualizacao) {
            $_SESSION['msg'] = "<p class='text-center' style='color: green;'>Sala atualizada com sucesso!</p>";
           header('Location: ./home.php');
            exit();
        } else {
            $_SESSION['msg'] = "<p class='text-center' style='color: red;'>Erro ao atualizar sala.</p>";
           header('Location: ./home.php');
            exit();
        }
    }
}
?>
