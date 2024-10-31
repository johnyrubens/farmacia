<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $crm = isset($_POST['crm']) ? $_POST['crm'] : '';
    $cro = isset($_POST['cro']) ? $_POST['cro'] : '';
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'prescritor';

    // Verifica se as senhas coincidem
    if ($senha != $confirmar_senha) {
        echo "As senhas não coincidem.";
        exit();
    }

    // Processar assinatura
    $assinatura = '';
    if ($tipo_usuario == 'prescritor' && isset($_FILES['assinatura']) && $_FILES['assinatura']['error'] == 0) {
        $assinatura = $_FILES['assinatura']['tmp_name'];
        $assinatura_nome = basename($_FILES['assinatura']['name']);
        $destino = 'assinaturas/' . $assinatura_nome;
        if (!is_dir('assinaturas')) mkdir('assinaturas', 0777, true);
        move_uploaded_file($assinatura, $destino);
        $assinatura = $destino; // Somente define a assinatura se for prescritor
    }

    // Prevenir SQL Injection
    $nome = $conn->real_escape_string($nome);
    $crm = $tipo_usuario == 'prescritor' ? $conn->real_escape_string($crm) : '';
    $cro = $tipo_usuario == 'prescritor' ? $conn->real_escape_string($cro) : '';
    $email = $conn->real_escape_string($email);
    $senha = $conn->real_escape_string($senha);
    $assinatura = $conn->real_escape_string($assinatura);
    $tipo_usuario = $conn->real_escape_string($tipo_usuario);

    // Inserir o novo usuário no banco de dados
    $sql = "INSERT INTO prescritores (nome, crm, cro, email, senha, assinatura, tipo_usuario) 
            VALUES ('$nome', '$crm', '$cro', '$email', '$senha', '$assinatura', '$tipo_usuario')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.html");
    } else {
        echo "Erro: " . $conn->error;
    }

    $conn->close();
}
?>
