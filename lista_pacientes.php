<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

// Obtém o ID do prescritor logado
$prescritor_id = $_SESSION['prescritor_id'];

// Consulta para obter os pacientes associados ao prescritor logado
$sql = "SELECT id, nome, cpf, telefone, rua, numero, cidade, uf, cep 
        FROM clientes 
        WHERE prescritor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prescritor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Pacientes</title>
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
    </style>
</head>
<body>

<div class="container">
    <h2>Lista de Pacientes</h2>
    <table>
        <tr>
            <th>Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>Endereço</th>
            <th>Cidade</th>
            <th>UF</th>
            <th>CEP</th>
            <!-- <th>Ações</th> -->
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['cpf']; ?></td>
                <td><?php echo $row['telefone']; ?></td>
                <td><?php echo $row['rua'] . ', ' . $row['numero']; ?></td>
                <td><?php echo $row['cidade']; ?></td>
                <td><?php echo $row['uf']; ?></td>
                <td><?php echo $row['cep']; ?></td>
                <!-- <td>
                    <a href="visualizar_paciente.php?id=<?php echo $row['id']; ?>">Visualizar</a>
                </td> -->
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
