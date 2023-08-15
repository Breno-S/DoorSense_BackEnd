<?php

function answer($response) {
    header("Content-Type: application/json");
    echo json_encode($response);
}

function login($conn, $username, $password) {
    // String de consulta
    $sql = "SELECT * FROM admin WHERE EMAIL_ADMIN = ? AND SENHA_ADMIN = ?";
    
    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    // Guardar TODOS os resultados da consulta
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) == 1) {
        return true;
    }

    return false;
}

function get_sala($conn, $id) {
    // String de consulta
    $sql = "SELECT * FROM sala WHERE ID_sala = ?";

    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    // Guardar TODOS os resultados da consulta
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    return false;
}

function create_sala($conn, $nome_sala, $numero_sala) {
    $sql = "INSERT INTO sala VALUES (DEFAULT, '$nome_sala', $numero_sala, DEFAULT)";
    $result = mysqli_query($conn, $sql);

    $id = mysqli_insert_id($conn) ? mysqli_insert_id($conn) : null;

    if ($id) {
        $sql = "SELECT * FROM sala WHERE ID_SALA = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row;
    }

    return false;
}

?>
