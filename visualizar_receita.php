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
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o ID da receita da URL e armazena na sessão
if (isset($_GET['id'])) {
    $_SESSION['receita_id'] = $_GET['id'];
}
$receita_id = $_SESSION['receita_id'] ?? null;

// Verifica se o ID da receita está definido
if (!$receita_id) {
    echo "Erro: ID da receita não especificado.";
    exit();
}

// Busca os detalhes da receita com informações do cliente e prescritor
$sql = "SELECT r.id, r.cid, r.data_prescricao, c.nome AS cliente_nome, pr.nome AS prescritor_nome
        FROM receitas r
        INNER JOIN clientes c ON r.cliente_id = c.id
        INNER JOIN prescritores pr ON r.prescritor_id = pr.id
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receita_id);
$stmt->execute();
$receita = $stmt->get_result()->fetch_assoc();

// Busca os medicamentos associados à receita
$sql_medicamentos = "SELECT m.nome, m.tipo, m.concentracao, mr.posologia, mr.quantidade, m.valor
                     FROM medicamentos_receita mr 
                     INNER JOIN produtos m ON mr.produto_id = m.id 
                     WHERE mr.receita_id = ?";
$stmt_medicamentos = $conn->prepare($sql_medicamentos);
$stmt_medicamentos->bind_param("i", $receita_id);
$stmt_medicamentos->execute();
$medicamentos_result = $stmt_medicamentos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Receita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding-top: 80px;
            margin: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 0;
            position: fixed;
            top: 0;
            width: 100%;
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
            max-width: 800px;
            width: 100%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .details {
            margin-bottom: 20px;
            font-size: 16px;
            color: #555;
        }
        .details p {
            margin: 8px 0;
        }
        .details strong {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .button-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Detalhes da Receita</h2>
    <div class="details">
        <p><strong>ID da Receita:</strong> <?php echo $receita['id']; ?></p>
        <p><strong>Paciente:</strong> <?php echo $receita['cliente_nome']; ?></p>
        <p><strong>Prescritor:</strong> <?php echo $receita['prescritor_nome']; ?></p>
        <p><strong>CID:</strong> <?php echo $receita['cid']; ?></p>
        <p><strong>Data de Prescrição:</strong> <?php echo date('d/m/Y', strtotime($receita['data_prescricao'])); ?></p>
    </div>

    <h3>Medicamentos Prescritos</h3>
    <table>
        <tr>
            <th>Medicamento</th>
            <th>Posologia</th>
            <th>Quantidade</th>
            <th>Valor</th> <!-- Nova coluna para valor -->
        </tr>
        <?php while ($medicamento = $medicamentos_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $medicamento['nome']; ?></td>
                <td><?php echo $medicamento['posologia']; ?></td>
                <td><?php echo $medicamento['quantidade']; ?></td>
                <td>R$ <?php echo number_format($medicamento['valor'] * $medicamento['quantidade'], 2, ',', '.'); ?></td> <!-- Multiplicação e formatação do valor -->
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Botão para baixar em PDF -->
    <div class="button-container">
        <form action="baixar_pdf.php" method="post" target="_blank">
            <input type="hidden" name="receita_id" value="<?php echo $receita['id']; ?>">
            <button type="submit">Baixar PDF</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
$stmt->close();
$stmt_medicamentos->close();
$conn->close();
?>
