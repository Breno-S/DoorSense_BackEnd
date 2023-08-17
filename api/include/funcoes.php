<?php

function answer($response) {
    header("Content-Type: application/json");
    echo json_encode($response);
}

/*****************************************************************************/

function login($conn, $username, $password) {
    // String de consulta
    $sql = "SELECT * FROM admin WHERE EMAIL_ADMIN = ? AND SENHA_ADMIN = ?";
    
    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    // Guardar TODO o result set da consulta
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) == 1) {
        return true;
    }

    return false;
}

/*****************************************************************************/

function get_sala($conn, $id) {
    // String de consulta
    $sql = "SELECT * FROM sala WHERE ID_sala = ?";

    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    // Obter o resultado da consulta
    $result = mysqli_stmt_get_result($stmt);

    // Verificar o número de linhas retornadas
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    return false;
}

/*****************************************************************************/

function get_all_salas($conn) {
    // String de consulta
    $sql = "SELECT * FROM sala";

    // Execução da consulta
    if ($result = mysqli_query($conn, $sql)) {
        $result_set = [];
            
        // Agrupar os resultados
        while ($row = mysqli_fetch_assoc($result)) {
            
            // Colocar os nomes das chaves no padrão vigente
            $new_row["id"] = $row['ID_SALA'];
            $new_row["nome"] = $row['NOME_SALA'];
            $new_row["numero"] = $row['NUMERO_SALA'];
            $new_row["status"] = $row['STATUS_SALA'];
            
            $result_set[] = $new_row;
        }
        
        // // String de consulta
        // $sql = "SELECT COUNT(*) AS total FROM sala";
        
        // // Execução da consulta
        // if ($result = mysqli_query($conn, $sql)) {
        //     $total_salas = mysqli_fetch_assoc($result);
        //     array_unshift($result_set, $total_salas);
        // }

        return $result_set;
    }

    return false;
}

/*****************************************************************************/

function get_total_salas($conn) {
    // String de consulta
    $sql = "SELECT COUNT(*) AS total FROM sala";
        
    // Execução da consulta
    if ($result = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];

        return $total;
    }

    return false;
    
}

/*****************************************************************************/

function create_sala($conn, $nome_sala, $numero_sala) {
    // String de consulta
    $sql = "INSERT INTO sala VALUES (DEFAULT, ?, ?, DEFAULT)";

    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $nome_sala, $numero_sala);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    $id = mysqli_insert_id($conn) ? mysqli_insert_id($conn) : null;

    if ($id) {
        $sql = "SELECT * FROM sala WHERE ID_SALA = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row;
    }

    return false;
}

/*****************************************************************************/


?>
