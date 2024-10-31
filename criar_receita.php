<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Receita</title>
    <style>
        .container { 
            width: 100%; 
            max-width: 1200px; 
            margin: 20px auto; 
            background: #fff; 
            padding: 20px; 
            border-radius: 10px; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
        }
        .form-group input, .form-group select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }
        .add-medicamento-btn { 
            background-color: #4CAF50; 
            color: white; 
            padding: 10px; 
            cursor: pointer; 
            margin-bottom: 15px;
        }
        .medicamento-row { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 10px;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 0;
            margin-bottom: 20px; /* Espaçamento da navbar */
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
    </style>

    <script>
        // Validação de CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, ''); // Remove caracteres não numéricos
            if (cpf.length != 11 || /^(\d)\1{10}$/.test(cpf)) return false; // Verifica formato e repetição
            let soma = 0, resto;

            for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            resto = (soma * 10) % 11;
            if ((resto == 10) || (resto == 11)) resto = 0;
            if (resto != parseInt(cpf.substring(9, 10))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            resto = (soma * 10) % 11;
            if ((resto == 10) || (resto == 11)) resto = 0;
            return resto == parseInt(cpf.substring(10, 11));
        }

        // Validação de CEP
        function validarCEP(cep) {
            cep = cep.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cep.length != 8) {
                alert("CEP inválido!");
                return false;
            }

            // Faz a requisição na API ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert("CEP não encontrado!");
                    } else {
                        // Preenche automaticamente os campos de endereço
                        document.getElementById("rua").value = data.logradouro;
                        document.getElementById("cidade").value = data.localidade;
                        document.getElementById("uf").value = data.uf;
                    }
                })
                .catch(error => {
                    alert("Erro ao buscar o CEP.");
                });
        }

        // Carrega as opções de medicamentos dinamicamente
        const medicamentosOpcoes = `
            <?php
            include 'db_config.php';
            $sql = "SELECT * FROM produtos";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
            }
            ?>
        `;

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("form").addEventListener("submit", function (e) {
                const cpf = document.getElementById("cpf").value;
                const cep = document.getElementById("cep").value;

                if (!validarCPF(cpf)) {
                    e.preventDefault(); // Impede o envio do formulário
                    alert("CPF inválido!");
                }

                validarCEP(cep); // Validação do CEP
            });

            // Função para adicionar medicamentos dinamicamente
            document.getElementById('add-medicamento').addEventListener('click', function () {
                const container = document.getElementById('medicamentos-container');
                const newMedicamento = `
                    <div class="medicamento-row">
                        <div class="form-group">
                            <label>Medicamento:</label>
                            <select name="medicamento[]" required>
                                ${medicamentosOpcoes}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantidade:</label>
                            <input type="number" name="quantidade[]" min="1" value="1" required>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', newMedicamento);
            });
        });
    </script>
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
        <h2>Criar Nova Receita</h2>
        <form action="processar_receita.php" method="POST" id="form-receita">
            <!-- Dados do paciente -->
            <div class="form-group">
                <label for="paciente_nome">Nome Completo:</label>
                <input type="text" id="paciente_nome" name="paciente_nome" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required>
            </div>
            <div class="form-group">
                <label for="rua">Rua:</label>
                <input type="text" id="rua" name="rua" required>
            </div>
            <div class="form-group">
                <label for="numero">Número:</label>
                <input type="text" id="numero" name="numero" required>
            </div>
            <div class="form-group">
                <label for="complemento">Complemento:</label>
                <input type="text" id="complemento" name="complemento">
            </div>
            <div class="form-group">
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade" required>
            </div>
            <div class="form-group">
                <label for="uf">UF:</label>
                <input type="text" id="uf" name="uf" maxlength="2" required>
            </div>
            <div class="form-group">
                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="cep" required onblur="validarCEP(this.value)">
            </div>

            <!-- Medicamentos -->
         <!-- Medicamentos -->
<h3>Medicamentos Prescritos</h3>
<div id="medicamentos-container">
    <div class="medicamento-row">
        <div class="form-group">
            <label>Medicamento:</label>
            <select name="medicamento[]" required>
                <?php
                include 'db_config.php';
                $sql = "SELECT * FROM produtos";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Quantidade:</label>
            <input type="number" name="quantidade[]" min="1" value="1" required>
        </div>
    </div>
</div>

            <button type="button" id="add-medicamento" class="add-medicamento-btn">Adicionar Medicamento</button>
            <input type="submit" value="Criar Receita">
        </form>
    </div>

</body>
</html>
