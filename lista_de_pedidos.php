<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'funcionario') {
    header("Location: acesso_negado.php");
    exit();
}

include 'db_config.php';

// Buscar todos os pedidos no banco de dados
$sql = "SELECT r.id AS receita_id, r.paciente_nome, r.telefone, r.data_prescricao, r.data_saida, r.status, 
        p.nome AS prescritor_nome, 
        GROUP_CONCAT(prd.nome SEPARATOR ', ') AS medicamentos, 
        SUM(mr.quantidade * prd.valor) AS valor_total
        FROM receitas r
        JOIN prescritores p ON r.prescritor_id = p.id
        JOIN medicamentos_receita mr ON mr.receita_id = r.id
        JOIN produtos prd ON mr.produto_id = prd.id
        GROUP BY r.id";
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

    $sql_update = "UPDATE receitas SET status = ?, data_saida = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssi", $status, $data_saida, $id);
    $stmt->execute();
    header("Location: todos_pedidos.php");
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
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        .container {
            margin-top: 60px;
            max-width: 1000px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .pedido {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pedido-header {
            font-weight: bold;
            color: #555;
            margin-bottom: 10px;
        }

        .status-cell {
            display: inline-block;
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }

        .valor-total {
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
            margin-top: 10px;
        }

        .link-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .link-actions a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .link-actions a:hover {
            text-decoration: underline;
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

    <div class="container">
        <h2>Lista Completa de Pedidos</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="pedido">
                    <div class="pedido-header">Pedido ID: <?php echo $row['receita_id']; ?> | Data: <?php echo date('d/m/Y H:i:s', strtotime($row['data_prescricao'])); ?></div>
                    <p><strong>Cliente:</strong> <?php echo $row['paciente_nome']; ?> | <strong>Telefone:</strong> <?php echo $row['telefone']; ?></p>
                    <p><strong>Prescritor:</strong> <?php echo $row['prescritor_nome']; ?></p>
                    <p><strong>Medicamentos:</strong> <?php echo $row['medicamentos']; ?></p>
                    <div class="status-cell" style="background-color: <?php echo getStatusColor($row['status']); ?>;">
                        Status: <?php echo $row['status']; ?>
                    </div>
                    <div class="valor-total">Valor Total: R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?></div>
                    <div class="link-actions">
                        <a href="visualizar_receita.php?id=<?php echo $row['receita_id']; ?>">Visualizar</a>
                        <a href="alterar_status.php?id=<?php echo $row['receita_id']; ?>">Alterar Status</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum pedido encontrado.</p>
        <?php endif; ?>

    </div>

</body>
</html>

<?php
$conn->close();
?>
