<?php

function login($conn, $username, $password) {
    // String de consulta
    $sql = "SELECT * FROM admin WHERE email_admin = ? AND senha_admin = ?";
    
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
    $sql = "SELECT * FROM sala WHERE id_sala = ?";

    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    // Execução da consulta
    mysqli_stmt_execute($stmt);

    // Obter o resultado da consulta
    $result = mysqli_stmt_get_result($stmt);

    // Verificar o número de linhas retornadas
    if (mysqli_num_rows($result) == 1) {
        $row_sala = mysqli_fetch_assoc($result);

        $data = $row_sala;
        
        if (empty($row_sala['FK_ARDUINO'])) {

            $data['ARDUINO_SALA'] = null;
            $data['STATUS_SALA'] = null;

        } else {
            // String de consulta
            $sql = "SELECT unique_id as ARDUINO_SALA,
                    status_arduino as STATUS_SALA FROM arduino
                    INNER JOIN sala ON id_arduino = fk_arduino
                    WHERE fk_arduino = ?";
            
            // Preparação da consulta
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $row_sala['FK_ARDUINO']);
        
            // Execução da consulta
            mysqli_stmt_execute($stmt);

            // Obter o resultado da consulta
            $result = mysqli_stmt_get_result($stmt);
            $row_arduino = mysqli_fetch_assoc($result);

            $data = array_merge($data, $row_arduino);
        }
        return $data;
    }

    return false;
}

/*****************************************************************************/

function get_all_salas($conn) {
    // String de consulta
    $sql = "SELECT * FROM sala
            LEFT JOIN arduino ON fk_arduino = id_arduino;";

    // Execução da consulta
    if ($result = mysqli_query($conn, $sql)) {
        $result_set = [];
            
        // Agrupar os resultados
        while ($row = mysqli_fetch_assoc($result)) {
            
            // Colocar os nomes das chaves no padrão vigente
            $new_row["id"] = $row['ID_SALA'];
            $new_row["nome"] = $row['NOME_SALA'];
            $new_row["numero"] = $row['NUMERO_SALA'];
            $new_row["arduino"] = $row['UNIQUE_ID'];
            $new_row["status"] = $row['STATUS_ARDUINO'];
            
            $result_set[] = $new_row;
        }

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
        $sql = "SELECT * FROM sala WHERE id_sala = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row;
    }
    return false;
}

/*****************************************************************************/

function delete_sala($conn, $id_sala) {
    // String de consulta
    $sql = "DELETE FROM sala WHERE id_sala = ?";

    // Preparação da consulta
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_sala);

    // Execução da consulta

    if (mysqli_stmt_execute($stmt)) {
        $rows_affected = mysqli_affected_rows($conn);

        if ($rows_affected > 0) {
            return true; // Registro deletado com sucesso
        } else {
            return false; // Nenhum registro encontrado com o ID fornecido
        }
    } else {
        return false; // Erro ao deletar registro
    }
}

/*****************************************************************************/

function update_sala($conn, array $update_values) {
    $id_sala = $update_values['id'];

    // Obtem os dados da sala a ser alterada
    $sql = "SELECT * FROM sala WHERE id_sala = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_sala);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $row_sala_old = mysqli_fetch_assoc($result);

    // Verifica se quer adicionar/alterar o arduino
    if (!empty($update_values['arduino']) && !empty($update_values['status'])) {
        $unique_id = $update_values['arduino'];
        $status_arduino = $update_values['status'];

        // Verificar se o arduino já existe no banco
        $sql = "SELECT * FROM arduino WHERE unique_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $unique_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // SE houver o arduino no banco
        if (mysqli_num_rows($result) == 1) {
            // obtenha o id dele
            $row_arduino = mysqli_fetch_assoc($result);
            $id_arduino = $row_arduino['ID_ARDUINO'];

            // e atualize seu status
            $sql = "UPDATE arduino SET status_arduino = ? WHERE id_arduino = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $status_arduino, $id_arduino);
            mysqli_stmt_execute($stmt);
        } else {
            // SENÃO, crie um registro
            $sql = "INSERT INTO arduino (id_arduino, unique_id, status_arduino, last_update)
                    VALUES (DEFAULT, ?, ?, DEFAULT)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $unique_id, $status_arduino);
            mysqli_stmt_execute($stmt);
            
            // e obtenha seu id
            $id_arduino = mysqli_insert_id($conn) ? mysqli_insert_id($conn) : null;
        }
    } else {
        $id_arduino = $row_sala_old['FK_ARDUINO'];
    }

    // verificacao de quais campos que serao atualizados
    $nome_sala = empty($update_values['nome']) ?
                    $row_sala_old['NOME_SALA'] : $update_values['nome'];
    $numero_sala = empty($update_values['numero']) ?
                      $row_sala_old['NUMERO_SALA'] : $update_values['numero'];

    // string de consulta
    $sql = "UPDATE sala SET nome_sala = ?, numero_sala = ?, fk_arduino = ? WHERE id_sala = ?";
    
    // Atualizar as informações no banco
    $stmtAtualizacao = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmtAtualizacao, "siii", $nome_sala, $numero_sala, $id_arduino, $id_sala);
    
    return mysqli_stmt_execute($stmtAtualizacao);
}

?>
