<?php
$host = '193.203.175.98'; // substitua se Hostinger fornecer um host específico
$dbname = 'u704604173_farmacia'; // seu nome de banco de dados
$user = 'u704604173_mateusligoski1'; // seu usuário do banco de dados
$pass = 'a7@9&s>N4'; // sua senha do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>