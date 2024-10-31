<?php
// Verifica se a sessão já foi iniciada, para evitar múltiplos session_start

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_dir = $_SERVER['DOCUMENT_ROOT'];

// Verifica o tipo de usuário para redirecionar para o dashboard correto
$home_link = "dashboard.php"; // Link padrão
if (isset($_SESSION['tipo_usuario'])) {
    switch ($_SESSION['tipo_usuario']) {
        case 'admin':
            $home_link = "dashboard_administrador.php";
            break;
        case 'prescritor':
            $home_link =  "dashboard_prescritor.php";
            break;
        case 'funcionario':
            $home_link = "dashboard_funcionario.php";
            break;
    }
}
?>

<div class="navbar">
    <a href="<?php echo $home_link; ?>">Home</a>
    <a href="cadastro_de_pacientes.php">Cadastrar Paciente</a>
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
</style>
