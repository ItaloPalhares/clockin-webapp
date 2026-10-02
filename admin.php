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
require_once __DIR__ . "/classes/ponto.php";

$ponto = new Ponto($pdo);

$busca = trim($_GET["busca"] ?? "");

$limite = 10;

$pagina = $_GET["pagina"] ?? 1;

if (!is_numeric($pagina) || $pagina < 1) {
    $pagina = 1;
}
$pagina = (int) $pagina;

$totalRegistros = $ponto->contarTodosColaboradores($busca);

$totalPaginas = max(1, (int) ceil($totalRegistros / $limite));

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$offset = ($pagina - 1) * $limite;

$pontos = $ponto->buscarColaboradoresPaginado($limite, $offset, $busca);

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ClockIn: Admin Board</title>
</head>

<body class="adminbody">

    <header class="topbar">
        <div class="topbar-conteudo">

            <a href="dashboard.php">
                <img
                    src="img/clockin-logo.png"
                    alt="ClockIN"
                    class="topbar-logo">
            </a>

        </div>
    </header>

    <main class="app-layout admin-layout">
        <section id="painel">
            <form method="GET">
                <h2>Pesquisar Colaborador</h2>
                <label for="busca">Email ou nome</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    value="<?php echo htmlspecialchars($busca); ?>">
                <input type="submit" value="buscar">
            </form>

            <a href="cadastro.php">Cadastrar novo colaborador</a>


        </section>

        <section id="registros">

            <h2>Registros de colaboradores</h2>

            <div class="tabela-container">
                <table>

                    <colgroup>
                        <col class="col-admin-nome">
                        <col class="col-admin-email">
                        <col class="col-data">
                        <col class="col-hora">
                        <col class="col-localizacao">
                        <col class="col-imagem">
                    </colgroup>

                    <tr>
                        <td>Nome</td>
                        <td>Email</td>
                        <td>Data</td>
                        <td>Hora</td>
                        <td>Localização</td>
                        <td>Imagem</td>
                    </tr>
                    <?php foreach ($pontos as $registro): ?>
                        <tr>
                            <td class="celula-nome">
                                <?php echo htmlspecialchars($registro["usuario_nome"]); ?>
                            </td>
                            <td class="celula-email">
                                <?php echo htmlspecialchars($registro["usuario_email"]); ?>
                            </td>
                            <td>
                                <?php echo date("d/m/Y", strtotime($registro["data_hora"])); ?>
                            </td>
                            <td>
                                <?php echo date("H:i:s", strtotime($registro["data_hora"])); ?>
                            </td>
                            <td class="localizacao-celula">
                                <a
                                    class="localizacao-link"
                                    href="https://www.google.com/maps/search/?api=1&query=<?php echo $registro["latitude"] . "," . $registro["longitude"]; ?>"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    <?php echo $registro["latitude"]; ?>,
                                    <?php echo $registro["longitude"]; ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($registro["imagem"])): ?>

                                    <button
                                        type="button"
                                        class="abrir-foto"
                                        data-ponto-id="<?php echo $registro["id"]; ?>">
                                        <?php echo htmlspecialchars($registro["imagem"]); ?>
                                    </button>

                                <?php else: ?>

                                    Sem foto

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php
            $parametroBusca = "";

            if ($busca !== "") {
                $parametroBusca = "&busca=" . urlencode($busca);
            }
            ?>

            <div class="paginacao">

                <?php if ($pagina > 1): ?>

                    <a href="?pagina=<?php echo $pagina - 1; ?><?php echo $parametroBusca; ?>">
                        Anterior
                    </a>

                <?php endif; ?>


                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>

                    <a
                        href="?pagina=<?php echo $i; ?><?php echo $parametroBusca; ?>"
                        class="<?php echo $i === $pagina ? 'pagina-ativa' : ''; ?>">
                        <?php echo $i; ?>
                    </a>

                <?php endfor; ?>


                <?php if ($pagina < $totalPaginas): ?>

                    <a href="?pagina=<?php echo $pagina + 1; ?><?php echo $parametroBusca; ?>">
                        Próxima
                    </a>

                <?php endif; ?>

            </div>

        </section>

    </main>

    <div id="modalfoto" class="modal-foto" hidden>

        <div class="modal-foto-conteudo">

            <img
                id="imagemregistro"
                src=""
                alt="Foto do registro de ponto">

            <button type="button" id="fecharfoto">
                Fechar
            </button>

        </div>

    </div>


    <script src="js/foto.js"></script>

</body>

</html>