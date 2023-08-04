<?php
session_start();
include_once('conexao.php');

$id = $_GET["ID_ADMIN"];

$_SESSION['ID_ADMIN'] = $id;

$query = "SELECT * FROM sala";
$query = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($query);

if (empty($row)) {
  header('Location: ../login.html');
}

//VERIFICANDO SE TEM UM USUARIO LOGADO
if (isset($_SESSION['ID_ADMIN'])) {
    $id = $_SESSION['ID_ADMIN'];

    $query = "SELECT * FROM admin 
      WHERE ID_ADMIN='$id'";
    $query = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($query);
    //SE ESTIVER LOGADO APARECERÁ AS SEGUINTES INFORMAÇÕES
    echo '<li><a class="getstarted scrollto" href="user.php?id=' . $row["ID_USUARIO"] . '" style="margin-left: 80px;">Ver perfil</a></li>';
    echo '<li><a class="nav-link scrollto" href="../back/controller/controller_logoff.php">LOGOFF</a></li>';
  } else {
    //SE NÃO ESTIVER LOGADO APARECERÁ AS SEGUINTES INFORMAÇÕES
    echo '<li><a class="nav-link scrollto" href="login.html" style="margin-left: 80px;">LOGIN</a></li>';
    echo '<li><a class="getstarted scrollto" href="cadastro.php">CADASTRE-SE</a></li>';
  }

  echo "<br><h2> ID: " . $row['ID_SALA'] . ' ' . $row['NOME_SALA'] . ' ' . $row['NUMERO_SALA'] . ' ' . $row['STATUS_SALA'] . "</h2><br>";

