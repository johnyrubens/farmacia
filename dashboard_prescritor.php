<?php
session_start(); // Inicia a sessão para acessar os dados do usuário logado

// Verifica se o usuário está autenticado como prescritor
if (!isset($_SESSION['prescritor_id']) || $_SESSION['tipo_usuario'] != 'prescritor') {
    header("Location: login.html");
    exit();
}

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

// Contar o número de receitas criadas hoje
$hoje = date('Y-m-d');
$sql_receitas_hoje = "SELECT COUNT(*) AS total_receitas_hoje FROM receitas WHERE DATE(data_prescricao) = '$hoje'";
$result_receitas_hoje = $conn->query($sql_receitas_hoje);
$receitas_hoje = $result_receitas_hoje->fetch_assoc()['total_receitas_hoje'];

// Calcular o valor total das receitas de hoje
$sql_valor_total_hoje = "
    SELECT SUM(mr.valor * mr.quantidade) AS total_valor_hoje 
    FROM receitas r
    JOIN medicamentos_receita mr ON r.id = mr.receita_id
    WHERE DATE(r.data_prescricao) = '$hoje'";
$result_valor_total_hoje = $conn->query($sql_valor_total_hoje);
$valor_total_hoje = $result_valor_total_hoje->fetch_assoc()['total_valor_hoje'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Prescritor</title>
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

        .navbar img {
            float: right; /* Alinha o logo à direita */
            height: 50px; /* Ajuste a altura conforme necessário */
            margin-right: 20px; /* Espaçamento à direita */
        }

        .container {
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 80px auto;
        }

        .statistics {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .statistics .stat {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 30%;
        }

        .statistics .stat h3 {
            margin-bottom: 10px;
            font-size: 22px;
            color: #333;
        }

        .statistics .stat p {
            font-size: 28px;
            font-weight: bold;
            color: #007BFF;
        }

        .action-buttons {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .action-buttons a {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .action-buttons a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="dashboard_prescritor.php">Home</a>
        <a href="cadastro_de_pacientes.php">Cadastro de Pacientes</a>
        <a href="lista_receitas.php">Minhas Receitas</a>
        <a href="criar_nova_receita.php">Criar Nova Receita</a>
        <a href="logout.php">Sair</a>
        <img src="imagens/logo.png" alt="Logo"> <!-- Caminho do logo -->
    </div>

    <!-- Conteúdo principal do dashboard -->
    <div class="container">
        <h2>Bem-vindo, Prescritor!</h2>
        <p>Aqui estão as principais informações e ações disponíveis para você.</p>

        <!-- Estatísticas -->
        <div class="statistics">
            <div class="stat">
                <h3>Receitas Criadas Hoje</h3>
                <p><?php echo $receitas_hoje; ?></p>
            </div>
           
            <div class="stat">
                <h3>Valor Total do Dia</h3>
                <p>R$ <?php echo number_format($valor_total_hoje, 2, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Ações rápidas -->
        <div class="action-buttons">
            <a href="cadastro_de_pacientes.php">Cadastrar Novo Paciente</a>
            <a href="criar_nova_receita.php">Criar Nova Receita</a>
            <a href="lista_receitas.php">Ver Todas as Receitas</a>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
