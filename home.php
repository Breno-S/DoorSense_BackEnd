<?php
        session_start();
        include_once('conexao.php');
          
       

        // Verificar se o usuário está logado
        if (!isset($_SESSION['ID_ADMIN'])) {
            header('Location: login.html');
            exit(); 
        }

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
                echo '<a href="login.html">LOGIN</a>';
            }

            // Loop para exibir informações sobre as salas
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div>';
                echo '<h2>ID: ' . $row['ID_SALA'] . ' / NOME: ' . $row['NOME_SALA'] . ' / NUMERO: ' . $row['NUMERO_SALA'] . ' / STATUS: ' . $row['STATUS_SALA'] . '</h2>';
                
                // Adicionar o botão de exclusão
                echo '<form method="post" action="controller_excluir.php">';
                echo '<input type="hidden" name="id_sala" value="' . $row['ID_SALA'] . '">';
                echo '<input type="submit" value="Excluir">';
                echo '</form>';

                echo'<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#criarSalaModal">Criar Sala</button>';

                echo'<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editarSalaModal">Editar Sala</button>';
                
                echo '</div><br>';
            }
            
            mysqli_stmt_close($stmt);
            // leva a tela de login caso não seja feita a validação do loop
        } else {
            header('Location: ../login.html');
        }

        mysqli_close($conn);
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Salas</title>
    <!-- Adicione essas linhas no cabeçalho do seu HTML -->

    <!-- Importação do CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Importação do JavaScript do Bootstrap (opcional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>

<!-- Modal de Criação de Sala -->
<div class="modal fade" id="editarSalaModal" tabindex="-1" role="dialog" aria-labelledby="editarSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editarSalaForm" action="controller_editar.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarSalaModalLabel">Editar Sala</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_sala" id="idSala">
                    <label for="nomeSala">Nome da Sala:</label>
                    <input type="text" id="nomeSala" name="nomeSala" class="form-control">
                    <label for="numeroSala">Número da Sala:</label>
                    <input type="number" id="numeroSala" name="numeroSala" class="form-control">
                    <div class="form-check">
                        <input type="radio" id="statusAtivo" name="statusSala" class="form-check-input" value="Ativo">
                        <label class="form-check-label" for="statusAtivo">Ativo</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="statusInativo" name="statusSala" class="form-check-input" value="Inativo">
                        <label class="form-check-label" for="statusInativo">Inativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <!-- Modal de Edição de Sala -->
    <div class="modal fade" id="editarSalaModal" tabindex="-1" role="dialog" aria-labelledby="editarSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarSalaModalLabel">Editar Sala</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="./controller_editar.php" method="post">
                    <div class="modal-body">
                        <label for="nomeSala">Nome da Sala:</label>
                        <input type="text" id="nomeSala" class="form-control">
                        <label for="numeroSala">Número da Sala:</label>
                        <input type="number" id="numeroSala" class="form-control">
                        <label for="statusSala">Status da Sala:</label>
                        <select name="statusSala" id="status" class="form-control required">
                            <option value="status">Ativo</option>
                            <option value="status">Inativo</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <input type="hidden" name="id_sala" value="<?php' . $row['ID_SALA'] . '?>">
                <input type="submit" value="Okay" id="editarSalaBtn">
            </div>
            </form>
        </div>
    </div>
</div>


    
    
</body>
</html>




