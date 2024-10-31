<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_config.php';

$prescritor_id = $_SESSION['user_id'];

// Buscar dados de receitas do prescritor logado
$sql = "SELECT COUNT(*) AS total_receitas FROM receitas WHERE prescritor_id = $prescritor_id";
$result = $conn->query($sql);
$total_receitas = $result->fetch_assoc()['total_receitas'];

$sql_ultima_receita = "SELECT paciente_nome, medicamento FROM receitas WHERE prescritor_id = $prescritor_id ORDER BY data_prescricao DESC LIMIT 1";
$result_ultima_receita = $conn->query($sql_ultima_receita);
$ultima_receita = $result_ultima_receita->fetch_assoc();

$sql_receitas_hoje = "SELECT COUNT(*) AS receitas_hoje FROM receitas WHERE prescritor_id = $prescritor_id AND DATE(data_prescricao) = CURDATE()";
$result_receitas_hoje = $conn->query($sql_receitas_hoje);
$receitas_hoje = $result_receitas_hoje->fetch_assoc()['receitas_hoje'];

// Consultar o total de receitas por mês para o gráfico
$sql_receitas_por_mes = "
    SELECT MONTH(data_prescricao) AS mes, COUNT(*) AS total
    FROM receitas
    WHERE prescritor_id = $prescritor_id
    GROUP BY MONTH(data_prescricao)";
$result_receitas_por_mes = $conn->query($sql_receitas_por_mes);

// Criar um array de receitas por mês
$receitas_por_mes = [];
for ($i = 1; $i <= 12; $i++) {
    $receitas_por_mes[$i] = 0;
}
while ($row = $result_receitas_por_mes->fetch_assoc()) {
    $receitas_por_mes[$row['mes']] = $row['total'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #6699db;
            color: white;
            margin: 0;
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

        .container {
            margin: 20px;
        }

        .dashboard-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .card {
            background-color: #ffffff;
            color: #333;
            width: 30%;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            margin-top: 0;
            color: #333;
        }

        .chart-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            color: #333;
            border-radius: 10px;
        }

        .chart {
            width: 100%;
            height: 300px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- Barra de Navegação -->
    <div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="lista_receitas.php">Minhas Receitas</a> <!-- Link para a lista de receitas -->
    <a href="criar_receita.php">Nova Receita</a> <!-- Link para criar uma nova receita -->
    <a href="perfil.php">Perfil</a>
    <a href="logout.php">Sair</a>
</div>




    <!-- Container principal -->
    <div class="container">
        <h2>Bem-vindo, <?php echo $_SESSION['user_nome']; ?>!</h2>

        <!-- Cards de informações rápidas -->
        <div class="dashboard-section">
            <div class="card">
                <h3>Total de Receitas</h3>
                <p><?php echo $total_receitas; ?> Receitas Criadas</p>
            </div>
            <div class="card">
                <h3>Última Receita</h3>
                <p><?php echo $ultima_receita ? $ultima_receita['medicamento'] . ' - Paciente: ' . $ultima_receita['paciente_nome'] : 'Nenhuma receita criada ainda'; ?></p>
            </div>
            <div class="card">
                <h3>Receitas Hoje</h3>
                <p><?php echo $receitas_hoje; ?> Receitas Criadas</p>
            </div>
        </div>

        <!-- Seção de gráficos e dados -->
        <div class="chart-section">
            <h3>Receitas ao Longo do Ano</h3>
            <canvas id="receitasChart" class="chart"></canvas>
        </div>
    </div>

    <script>
        // Dados do gráfico
        const ctx = document.getElementById('receitasChart').getContext('2d');
        const receitasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Receitas Criadas',
                    data: <?php echo json_encode(array_values($receitas_por_mes)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
