<?php
session_start();
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/usuario.php";

$usuario = new Usuario($pdo);

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $usuarioLogin = $usuario->buscarEmail(
        $email
    );

if ($usuarioLogin){

    if ($usuario->validarSenha($senha, $usuarioLogin["senha"])){

        session_regenerate_id(true);

        $_SESSION["usuario_id"] = $usuarioLogin["id"];
        $_SESSION["usuario_nome"] = $usuarioLogin["nome"];
        $_SESSION["usuario_email"] = $usuarioLogin["email"];
        $_SESSION["usuario_perfil"] = $usuarioLogin["perfil"];

        header("Location: dashboard.php");
        exit;

    } else {
       $erro = "E-mail ou senha invalidos.";
    }

} else {
    $erro = "E-mail ou senha invalidos.";
}
    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ClockIn: Ponto login</title>
</head>
<body class="authbody">
    <form method="POST">
        <h2> Login</h2>
        <?php if ($erro !== ""): ?>
        <p><?php echo $erro; ?></p>
        <?php endif; ?>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" required>
        <input type="submit" value="entrar">

    </form>    

</body>
</html>
