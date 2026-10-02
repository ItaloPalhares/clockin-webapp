<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["usuario_perfil"] !== "admin") {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/usuario.php";



$usuario = new Usuario($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $usuario->cadastrarColaborador(
        $nome,
        $email,
        $senha
    );
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ClockIn: Cadastro</title>
</head>
<body class="authbody">
    <form method="POST">
        <h2>Cadastro de Colaborador</h2>
         <label for="nome">nome</label>
        <input type="text" name="nome" id="nome" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" required>
        <input type="submit" value="Confirmar cadastro" id="confirmar">

    </form>    

</body>
</html>