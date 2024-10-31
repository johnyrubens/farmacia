<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
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
            float: left;
            padding: 0 15px;
            height: 40px;
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
        <a href="#">Home</a>
        <a href="#">Resumo de Prescritores</a>
        <a href="#">Todos os Pedidos</a>
        <a href="logout.php">Sair</a>
    </div>

    <!-- Conteúdo principal do dashboard -->
    <div class="container">
        <h2>Bem-vindo, Administrador!</h2>
        <p>Aqui estão as informações mais recentes e principais ações que você pode realizar.</p>

        <!-- Estatísticas -->
        <div class="statistics">
            <div class="stat">
                <h3>Receitas Geradas Hoje</h3>
                <p>15</p> <!-- Este valor deve ser substituído dinamicamente pelos dados reais do sistema -->
            </div>
            <div class="stat">
                <h3>Faturamento Total do Dia</h3>
                <p>R$ 12.345,67</p> <!-- Este valor deve ser substituído dinamicamente pelos dados reais do sistema -->
            </div>
            <div class="stat">
                <h3>Prescritores Ativos</h3>
                <p>8</p> <!-- Este valor deve ser substituído dinamicamente pelos dados reais do sistema -->
            </div>
        </div>

        <!-- Ações rápidas -->
        <div class="action-buttons">
            <a href="#">Gerar Relatório</a>
            <a href="#">Adicionar Novo Prescritor</a>
            <a href="#">Ver Estatísticas Gerais</a>
        </div>
    </div>

</body>
</html>
