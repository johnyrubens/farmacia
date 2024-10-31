<?php
session_start();

// Verifica se o usuário está autenticado como prescritor
if (!isset($_SESSION['prescritor_id']) || $_SESSION['tipo_usuario'] != 'prescritor') {
    header("Location: login.html");
    exit();
}

// Inclui a navbar
include 'navbar.php';

// Conexão com o banco de dados
$servername = "193.203.175.98";
$username = "u704604173_mateusligoski1";
$password = "a7@9&s>N4";
$dbname = "u704604173_farmacia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para obter as receitas com detalhes do cliente e prescritor
$sql = "SELECT r.id AS receita_id, r.cid, r.data_prescricao, p.status, 
        c.nome AS cliente_nome, pr.nome AS prescritor_nome
        FROM receitas r
        INNER JOIN clientes c ON r.cliente_id = c.id
        INNER JOIN prescritores pr ON r.prescritor_id = pr.id
        LEFT JOIN pedidos p ON p.receita_id = r.id
        ORDER BY r.data_prescricao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Receitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-pendente { color: orange; font-weight: bold; }
        .status-em-contato { color: blue; font-weight: bold; }
        .status-em-producao { color: purple; font-weight: bold; }
        .status-despachado { color: green; font-weight: bold; }
        .status-sem-retorno { color: gray; font-weight: bold; }
        .status-finalizado-sem-venda { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Lista de Receitas</h2>
    <table>
        <tr>
            <th>ID da Receita</th>
            <th>Cliente</th>
            <th>Prescritor</th>
            <th>CID</th>
            <th>Data de Prescrição</th>
            <th>Status do Pedido</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <?php
            // Define a classe CSS com base no status
            $status_class = '';
            switch ($row['status']) {
                case 'Pendente':
                    $status_class = 'status-pendente';
                    break;
                case 'Em Contato':
                    $status_class = 'status-em-contato';
                    break;
                case 'Em Produção':
                    $status_class = 'status-em-producao';
                    break;
                case 'Despachado':
                    $status_class = 'status-despachado';
                    break;
                case 'Sem Retorno':
                    $status_class = 'status-sem-retorno';
                    break;
                case 'Finalizado sem Venda':
                    $status_class = 'status-finalizado-sem-venda';
                    break;
            }
            ?>
            <tr>
                <td><?php echo $row['receita_id']; ?></td>
                <td><?php echo $row['cliente_nome']; ?></td>
                <td><?php echo $row['prescritor_nome']; ?></td>
                <td><?php echo $row['cid']; ?></td>
                <td><?php echo $row['data_prescricao']; ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $row['status']; ?></td>
                <td>
                    <a href="visualizar_receita.php?id=<?php echo $row['receita_id']; ?>">Visualizar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>