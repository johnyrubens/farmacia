<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'funcionario') {
    header("Location: acesso_negado.php");
    exit();
}

include 'db_config.php';

// Buscar todos os pedidos no banco de dados
$sql = "SELECT r.id, r.paciente_nome, r.telefone, r.medicamento, r.status, r.data_prescricao, r.data_saida, p.nome AS prescritor_nome 
        FROM receitas r
        JOIN prescritores p ON r.prescritor_id = p.id";
$result = $conn->query($sql);

// Função para definir a cor baseada no status
function getStatusColor($status) {
    switch ($status) {
        case 'Pendente':
            return '#f39c12'; // Amarelo
        case 'Produção Iniciada':
            return '#3498db'; // Azul
        case 'Despachado':
            return '#2ecc71'; // Verde
        case 'Sem Retorno':
            return '#e74c3c'; // Vermelho
        case 'Não Fechado':
            return '#95a5a6'; // Cinza
        case 'Atendimento Iniciado':
            return '#e67e22'; // Laranja
        default:
            return '#bdc3c7'; // Padrão: Cinza claro
    }
}

// Atualizar status e data de saída
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $data_saida = $_POST['data_saida'];

    $sql_update = "UPDATE receitas SET status = '$status', data_saida = '$data_saida' WHERE id = $id";
    $conn->query($sql_update);
    header("Location: todos_pedidos.php"); // Redirecionar para a mesma página após atualizar
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Completa de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 0;
        }

        .navbar a {
            float: left;
            display: block;
            color: #fff;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 18px;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #6699db;
            color: white;
        }

        .status-cell {
            font-weight: bold;
            color: white;
            text-align: center;
            padding: 8px;
            border-radius: 5px;
        }

        a {
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        input, select {
            padding: 5px;
            margin-top: 5px;
            font-size: 14px;
        }

        form {
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="dashboard_funcionario.php">Home</a>
        <a href="todos_pedidos.php">Lista Completa de Pedidos</a>
        <a href="logout.php">Sair</a>
    </div>

    <h2>Lista Completa de Pedidos</h2>

    <table>
        <thead>
            <tr>
                <th>Prescritor</th>
                <th>Paciente</th>
                <th>Telefone</th>
                <th>Produto</th>
                <th>Status</th>
                <th>Data de Prescrição</th>
                <th>Data de Saída</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['prescritor_nome']; ?></td>
                        <td><?php echo $row['paciente_nome']; ?></td>
                        <td><?php echo $row['telefone']; ?></td>
                        <td><?php echo $row['medicamento']; ?></td>
                        <td>
                            <div class="status-cell" style="background-color: <?php echo getStatusColor($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['data_prescricao'])); ?></td>
                        <td><?php echo !empty($row['data_saida']) ? date('d/m/Y', strtotime($row['data_saida'])) : 'Ainda não saiu'; ?></td>
                        <td>
                            <form action="todos_pedidos.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status">
                                    <option value="Pendente" <?php if($row['status'] == 'Pendente') echo 'selected'; ?>>Pendente</option>
                                    <option value="Produção Iniciada" <?php if($row['status'] == 'Produção Iniciada') echo 'selected'; ?>>Produção Iniciada</option>
                                    <option value="Despachado" <?php if($row['status'] == 'Despachado') echo 'selected'; ?>>Despachado</option>
                                    <option value="Sem Retorno" <?php if($row['status'] == 'Sem Retorno') echo 'selected'; ?>>Sem Retorno</option>
                                    <option value="Não Fechado" <?php if($row['status'] == 'Não Fechado') echo 'selected'; ?>>Não Fechado</option>
                                    <option value="Atendimento Iniciado" <?php if($row['status'] == 'Atendimento Iniciado') echo 'selected'; ?>>Atendimento Iniciado</option>
                                </select>
                                <br>
                                <label for="data_saida">Data de Saída:</label>
                                <input type="date" name="data_saida" value="<?php echo !empty($row['data_saida']) ? date('Y-m-d', strtotime($row['data_saida'])) : ''; ?>">
                                <br>
                                <input type="submit" value="Atualizar">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Nenhum pedido encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
