<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Funcionário</title>
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

        .container {
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 80px auto;
        }

        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 15px;
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
        <a href="lista_de_pedidos.php">Lista de Pedidos</a>
        <a href="logout.php">Sair</a>
    </div>

    <!-- Conteúdo principal do dashboard -->
    <div class="container">
        <h2>Bem-vindo, Funcionário!</h2>
        <p>Aqui estão os pedidos que você precisa gerenciar e acompanhar.</p>

        <div class="action-buttons">
            <a href="#">Ver Todos os Pedidos</a>
            <a href="#">Acompanhar Produção</a>
        </div>
    </div>

</body>
</html>
