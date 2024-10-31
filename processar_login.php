<?php
session_start(); // Inicia a sessão

// Configurações de conexão com o banco de dados
$servername = "193.203.175.98";
$username = "u704604173_mateusligoski1";
$password = "a7@9&s>N4";
$dbname = "u704604173_farmacia";

// Conectando ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recebe os dados do formulário de login
$email = $_POST['email'];
$senha = $_POST['senha'];

// Verifica as credenciais no banco de dados
$sql = "SELECT id, nome, tipo_usuario FROM prescritores WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login válido: obtém o ID do prescritor, nome e tipo de usuário, e armazena na sessão
    $row = $result->fetch_assoc();
    $_SESSION['prescritor_id'] = $row['id'];
    $_SESSION['prescritor_nome'] = $row['nome'];
    $_SESSION['tipo_usuario'] = $row['tipo_usuario'];

    // Redireciona para o dashboard específico de acordo com o tipo de usuário
    if ($row['tipo_usuario'] == 'admin') {
        header("Location: dashboard_admin.php");
    } elseif ($row['tipo_usuario'] == 'prescritor') {
        header("Location: dashboard_prescritor.php");
    } elseif ($row['tipo_usuario'] == 'funcionario') {
        header("Location: dashboard_funcionario.php");
    } else {
        // Caso o tipo de usuário não seja reconhecido, redireciona para uma página de erro ou de login
        echo "<script>alert('Tipo de usuário desconhecido.'); window.location.href='login.html';</script>";
    }
    exit();
} else {
    // Login inválido
    echo "<script>alert('Email ou senha incorretos.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
