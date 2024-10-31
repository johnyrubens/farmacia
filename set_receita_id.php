<?php
session_start();

if (isset($_GET['receita_id'])) {
    $_SESSION['receita_id'] = $_GET['receita_id'];
    header("Location: visualizar_receita.php");
    exit();
} else {
    echo "Erro: ID da receita não especificado.";
}
?>
