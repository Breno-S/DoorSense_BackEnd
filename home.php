<?php
        session_start();
        include_once('conexao.php');
          
        $tabela = [];

        // Verificar se o usuário está logado
        if (!isset($_SESSION['ID_ADMIN'])) {
            header('Location: login.html');
            exit(); 
        }

        $id = $_SESSION['ID_ADMIN'];
        $sql = "SELECT * FROM sala ";
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
                $tabela[$row['ID_SALA']] = $row;
                echo '<div>';
                echo '<h2>ID: ' . $row['ID_SALA'] . ' / NOME: ' . $row['NOME_SALA'] . ' / NUMERO: ' . $row['NUMERO_SALA'] . ' / STATUS: ' . $row['STATUS_SALA'] . '</h2>';
                
                // Adicionar o botão de exclusão
                echo '<form method="post" action="controller_excluir.php">';
                echo '<input type="hidden" name="id_sala" value="' . $row['ID_SALA'] . '">';
                $_SESSION['sala'] = $row['ID_SALA'];
                echo '<input type="submit" value="Excluir">';
                echo '</form>';
                //Adicionar o botão de 
                echo'<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editarSalaModal">Editar Sala</button>';
                
                echo '</div><br>';
            }
            
            mysqli_stmt_close($stmt);
            // leva a tela de login caso não seja feita a validação do loop
        } else {
            header('Location: ../login.html');
        }

        mysqli_close($conn);
        print_r($tabela);
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Salas</title>
    <header><?php echo'<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#criarSalaModal">Criar Sala</button>';?></header>

    <!-- Importação do CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Importação do JavaScript do Bootstrap (opcional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>

<!-- Modal de Criação de Sala -->
<div class="modal fade" id="criarSalaModal" tabindex="-1" role="dialog" aria-labelledby="criarSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="criarSalaForm" action="controller_criar.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="criarSalaModalLabel">Criar Sala</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="nomeSalaCriar">Nome da Sala:</label>
                    <input type="text" id="nomeSalaCriar" name="nomeSalaCriar" class="form-control">
                    <label for="numeroSalaCriar">Número da Sala:</label>
                    <input type="number" id="numeroSalaCriar" name="numeroSalaCriar" class="form-control">
                    <div class="form-check">
                        <input type="radio" id="statusAtivoCriar" name="statusSalaCriar" class="form-check-input" value="Ativo">
                        <label class="form-check-label" for="statusAtivoCriar">Ativo</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="statusInativoCriar" name="statusSalaCriar" class="form-check-input" value="Inativo">
                        <label class="form-check-label" for="statusInativoCriar">Inativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
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
                        <input type="text" id="nomeSala" value="<?php $tabela[$row['NOME_SALA']] ?>" class="form-control">
                        <label for="numeroSala">Número da Sala:</label>
                        <input type="number" id="numeroSala" value="<?php $tabela[$row['NUMERO_SALA']] ?>" class="form-control">
                        <label for="statusSala">Status da Sala:</label>
                        <select name="statusSala" id="status" value="<?php $tabela[$row['STATUS_SALA']] ?>" class="form-control required">
                            <option value="status">Ativo</option>
                            <option value="status">Inativo</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <input type="submit" value="Okay" id="editarSalaBtn">

            </div>
            </form>
        </div>
    </div>
</div>


    
    
</body>
</html>




