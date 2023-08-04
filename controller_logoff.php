<?php 
    session_start();
    session_destroy(); // Destruir todas as informações da sessão
    echo "<script>location.href='login.html';</script>";
?>
