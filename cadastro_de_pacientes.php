<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica o tipo de usuário para redirecionar para o dashboard correto
$home_link = "dashboard.php"; // Link padrão
if (isset($_SESSION['tipo_usuario'])) {
    switch ($_SESSION['tipo_usuario']) {
        case 'admin':
            $home_link = "dashboard_admin.php";
            break;
        case 'prescritor':
            $home_link = "dashboard_prescritor.php";
            break;
        case 'funcionario':
            $home_link = "dashboard_funcionario.php";
            break;
    }
}
?>

<div class="navbar">
    <a href="<?php echo $home_link; ?>">Home</a>
    <a href="cadastro_pacientes.php">Cadastrar Paciente</a>
    <a href="lista_pacientes.php">Lista de Pacientes</a>
    <a href="logout.php">Sair</a>
</div>

<style>
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

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }
    </style>

    <script>
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
    </script>
</head>
<body>

    <div class="container">
        <h2>Cadastro de Paciente</h2>
        <form action="processar_paciente.php" method="POST">
            <label for="nome">Nome Completo:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" required>

            <label for="rua">Rua:</label>
            <input type="text" id="rua" name="rua" required>

            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" required>

            <label for="complemento">Complemento:</label>
            <input type="text" id="complemento" name="complemento">

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" required>

            <label for="uf">UF:</label>
            <input type="text" id="uf" name="uf" maxlength="2" required>

            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" required onblur="validarCEP(this.value)">

            <input type="submit" value="Cadastrar Paciente">
        </form>
    </div>
</body>
</html>
