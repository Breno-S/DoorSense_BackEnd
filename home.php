<?php
session_start();

include_once('conexao.php');

$id = $_SESSION['ID_ADMIN'];

$sql = "SELECT * FROM sala";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Exibir informações do usuário logado
    if (isset($_SESSION['ID_ADMIN'])) {
        $query = "SELECT * FROM admin WHERE ID_ADMIN = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result2 = mysqli_stmt_get_result($stmt);
        $row2 = mysqli_fetch_assoc($result2);

        echo '<a href="controller_logoff.php">LOGOFF</a>';
        
    } else {
        echo '<a  href="login.html">LOGIN</a>';
    }

    // Loop para exibir informações sobre as salas
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<br><h2> [ ID: " . $row['ID_SALA'] . ' / '.'  NOME: ' . $row['NOME_SALA'] . ' / '.'   NUMERO:   ' . $row['NUMERO_SALA'] . ' / ' . '  STATUS:  ' . $row['STATUS_SALA'] . ' ]'. "</h2><br>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Location: ../login.html');
}

mysqli_close($conn);
?>


