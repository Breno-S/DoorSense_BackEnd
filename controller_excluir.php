<?php
session_start();

include_once('conexao.php');

if (isset($_POST['id_sala'])) {
    $id_sala = $_POST['id_sala'];

    // Verificar se o usuário está logado
    if (!isset($_SESSION['ID_ADMIN'])) {
        header('Location: login.html');
        exit();
    }

    // Executar a exclusão da sala no banco de dados
    $delete_query = "DELETE FROM sala WHERE ID_SALA = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $id_sala);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header('Location: home.php'); // Redirecionar de volta para a página de salas
        exit();
    } else {
        echo "Erro ao excluir a sala: " . mysqli_error($conn);
    }
} else {
    header('Location: home.php'); // Redirecionar de volta para a página de salas
    exit();
}

mysqli_close($conn);
?>
