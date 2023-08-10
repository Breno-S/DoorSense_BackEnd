<?php
session_start();
include_once('conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['ID_ADMIN'])) {
    header('Location: login.html');
    exit();
}

// recebendo os inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeSala = $_POST['nomeSalaCriar'];
    $numeroSala = $_POST['numeroSalaCriar'];
    $statusSala = $_POST['statusSalaCriar'];
    var_dump($_POST);
    // Preparar e executar a consulta SQL para inserção
    $query = "INSERT INTO sala (NOME_SALA, NUMERO_SALA, STATUS_SALA) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sis", $nomeSala, $numeroSala, $statusSala);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Sala criada com sucesso, redirecionar para a página de salas
        header('Location: home.php');
        exit();
    } else {
        // Tratar erro, redirecionar ou mostrar mensagem de erro
        echo "Erro ao criar sala: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
