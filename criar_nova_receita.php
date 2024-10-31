<?php
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

// Busca a lista de clientes
$clientes_sql = "SELECT id, nome FROM clientes";
$clientes_result = $conn->query($clientes_sql);

// Busca a lista de produtos com posologia
$produtos_sql = "SELECT id, nome, concentracao, posologia FROM produtos";
$produtos_result = $conn->query($produtos_sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Receita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 80px auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        select, input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"], .add-product {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="submit"]:hover, .add-product:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function addProduct() {
            const productSection = document.createElement('div');
            productSection.className = "product-section";

            productSection.innerHTML = `
                <label for="produto">Medicamento:</label>
                <select name="produto_id[]" class="produto-select" required onchange="updatePosologia(this)">
                    <option value="">Selecione um medicamento...</option>
                    <?php
                    if ($produtos_result->num_rows > 0) {
                        $produtos_result->data_seek(0); // Reset result pointer
                        while ($produto = $produtos_result->fetch_assoc()) {
                            echo "<option value='{$produto['id']}' data-posologia='{$produto['posologia']}'>{$produto['nome']} - {$produto['concentracao']}</option>";
                        }
                    }
                    ?>
                </select>

                <label for="posologia">Posologia:</label>
                <input type="text" name="posologia[]" class="posologia-input" readonly>

                <label for="quantidade">Quantidade:</label>
                <input type="number" name="quantidade[]" min="1" required>
            `;

            document.getElementById('products-container').appendChild(productSection);
        }

        function updatePosologia(selectElement) {
            const posologiaInput = selectElement.parentElement.querySelector('.posologia-input');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            posologiaInput.value = selectedOption.getAttribute('data-posologia') || '';
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>Criar Nova Receita</h2>
        <form action="processar_receita.php" method="POST">
            <!-- Cliente -->
            <label for="cliente">Paciente:</label>
            <select id="cliente" name="cliente_id" required>
                <option value="">Selecione um paciente...</option>
                <?php
                if ($clientes_result->num_rows > 0) {
                    while ($cliente = $clientes_result->fetch_assoc()) {
                        echo "<option value='{$cliente['id']}'>{$cliente['nome']}</option>";
                    }
                }
                ?>
            </select>

            <!-- Produtos Dinâmicos -->
            <div id="products-container">
                <div class="product-section">
                    <label for="produto">Medicamento:</label>
                    <select name="produto_id[]" class="produto-select" required onchange="updatePosologia(this)">
                        <option value="">Selecione um medicamento...</option>
                        <?php
                        if ($produtos_result->num_rows > 0) {
                            $produtos_result->data_seek(0); // Reset result pointer
                            while ($produto = $produtos_result->fetch_assoc()) {
                                echo "<option value='{$produto['id']}' data-posologia='{$produto['posologia']}'>{$produto['nome']} - {$produto['concentracao']}</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="posologia">Posologia:</label>
                    <input type="text" name="posologia[]" class="posologia-input" readonly>

                    <label for="quantidade">Quantidade:</label>
                    <input type="number" name="quantidade[]" min="1" required>
                </div>
            </div>

            <!-- Botão para adicionar mais produtos -->
            <button type="button" class="add-product" onclick="addProduct()">Adicionar Produto</button>

            <!-- CID -->
            <label for="cid">CID:</label>
            <input type="text" id="cid" name="cid" required>

            <!-- Botão de envio -->
            <input type="submit" value="Criar Receita">
        </form>
    </div>

</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>
