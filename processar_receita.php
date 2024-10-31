<?php
session_start();

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

// Captura os dados do formulário
$cliente_id = $_POST['cliente_id'];
$prescritor_id = $_SESSION['prescritor_id'];
$cid = $_POST['cid'];
$produto_ids = $_POST['produto_id'];
$posologias = $_POST['posologia'];
$quantidades = $_POST['quantidade'];

// Insere a receita na tabela `receitas`
$stmt = $conn->prepare("INSERT INTO receitas (cliente_id, prescritor_id, cid, data_prescricao) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $cliente_id, $prescritor_id, $cid);
if ($stmt->execute()) {
    $receita_id = $stmt->insert_id;
    echo "Receita inserida com sucesso!<br>";
} else {
    die("Erro ao inserir receita: " . $stmt->error);
}
$stmt->close();

// Insere os medicamentos na tabela `medicamentos_receita`
$stmt = $conn->prepare("INSERT INTO medicamentos_receita (receita_id, produto_id, posologia, quantidade) VALUES (?, ?, ?, ?)");

for ($i = 0; $i < count($produto_ids); $i++) {
    $produto_id = $produto_ids[$i];
    $posologia = $posologias[$i];
    $quantidade = $quantidades[$i];
    
    $stmt->bind_param("iisi", $receita_id, $produto_id, $posologia, $quantidade);
    if ($stmt->execute()) {
        echo "Medicamento $produto_id inserido com sucesso!<br>";
    } else {
        echo "Erro ao inserir medicamento $produto_id: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// Cria um pedido pendente na tabela `pedidos` para a receita criada
$stmt = $conn->prepare("INSERT INTO pedidos (receita_id, status, data_pedido) VALUES (?, 'Pendente', NOW())");
$stmt->bind_param("i", $receita_id);
if ($stmt->execute()) {
    echo "Pedido criado com sucesso!<br>";
} else {
    die("Erro ao criar pedido: " . $stmt->error);
}
$stmt->close();

// Fecha a conexão com o banco de dados
$conn->close();

// Redireciona para a lista de receitas
header("Location: lista_receitas.php");
exit();
?>
