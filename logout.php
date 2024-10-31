<?php
session_start();

// Destruir todas as sessões
$_SESSION = array();

// Se desejar destruir completamente a sessão, também deve deletar o cookie de sessão.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header("Location: login.html");
exit();
?>
