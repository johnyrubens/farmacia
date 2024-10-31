<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_config.php';

$receita_id = $_GET['id']; // ID da receita passada por GET

// Buscar os dados da receita no banco de dados
$sql = "SELECT r.*, p.nome AS prescritor_nome 
        FROM receitas r 
        JOIN prescritores p ON r.prescritor_id = p.id 
        WHERE r.id = $receita_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $receita = $result->fetch_assoc();
} else {
    echo "Receita não encontrada.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Receita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 0;
            margin-bottom: 20px;
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

        .medicamentos {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="dashboard.php">Home</a>
        <a href="lista_receitas.php">Minhas Receitas</a>
        <a href="criar_receita.php">Nova Receita</a>
        <a href="perfil.php">Perfil</a>
        <a href="logout.php">Sair</a>
    </div>

    <div class="container">
        <h2>Receita Médica</h2>
        <p><strong>Paciente:</strong> <?php echo $receita['paciente_nome']; ?></p>
        <p><strong>CPF:</strong> <?php echo $receita['cpf']; ?></p>
        <p><strong>Telefone:</strong> <?php echo $receita['telefone']; ?></p>
        <p><strong>Endereço:</strong> <?php echo $receita['rua'] . ', ' . $receita['numero'] . ' - ' . $receita['complemento'] . ', ' . $receita['cidade'] . '/' . $receita['uf'] . ' - ' . $receita['cep']; ?></p>
        <p><strong>Prescritor:</strong> <?php echo $receita['prescritor_nome']; ?></p>

        <!-- Exibir medicamentos prescritos -->
        <div class="medicamentos">
            <h3>Medicamentos Prescritos:</h3>
            <ul>
                <?php
                include 'db_config.php'; // Reabrir conexão para medicamentos
                $sql_medicamentos = "SELECT p.nome, mr.quantidade 
                                     FROM medicamentos_receita mr
                                     JOIN produtos p ON mr.produto_id = p.id
                                     WHERE mr.receita_id = $receita_id";
                $result_medicamentos = $conn->query($sql_medicamentos);

                while ($medicamento = $result_medicamentos->fetch_assoc()) {
                    echo "<li>" . $medicamento['nome'] . " - Quantidade: " . $medicamento['quantidade'] . "</li>";
                }

                $conn->close();
                ?>
            </ul>
        </div>

        <a href="gerar_pdf.php?id=<?php echo $receita_id; ?>">Salvar como PDF</a>
    </div>

</body>
</html>
