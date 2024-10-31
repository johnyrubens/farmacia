<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Inicia a sessão para acessar os dados do usuário logado

// Verifique se o usuário está logado e possui um ID de prescritor
if (!isset($_SESSION['prescritor_id'])) {
    die("Erro: usuário não autenticado.");
}

// Configurações de conexão com o banco de dados
$servername = "193.203.175.98";
$username = "u704604173_mateusligoski1";
$password = "a7@9&s>N4";
$dbname = "u704604173_farmacia";

// Conectando ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recebendo dados do formulário
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$telefone = $_POST['telefone'];
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];
$cidade = $_POST['cidade'];
$uf = $_POST['uf'];
$cep = $_POST['cep'];

// Captura o ID do prescritor logado a partir da sessão
$prescritor_id = $_SESSION['prescritor_id'];

// Preparando o SQL para inserir os dados
$sql = "INSERT INTO clientes (nome, cpf, telefone, rua, numero, complemento, cidade, uf, cep, prescritor_id)
        VALUES ('$nome', '$cpf', '$telefone', '$rua', '$numero', '$complemento', '$cidade', '$uf', '$cep', '$prescritor_id')";

// Executa a inserção e verifica se foi bem-sucedida
if ($conn->query($sql) === TRUE) {
    echo "Paciente cadastrado com sucesso!";
    header("Location: lista_pacientes.php"); // Redireciona para a lista de pacientes (ajuste conforme necessário)
    exit();
} else {
    echo "Erro ao cadastrar paciente: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
