<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/ponto.php";

$ponto = new Ponto($pdo);

$usuarioId = $_SESSION["usuario_id"];

$limite = 10;
$pagina = $_GET["pagina"] ?? 1;
$pagina = (int) $pagina;
$totalRegistros = $ponto->contarPorUsuario($usuarioId);
$totalPaginas = max(1, (int) ceil($totalRegistros / $limite));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$offset = ($pagina - 1) * $limite;

$pontos = $ponto->buscarUsuarioPorIdPaginado($usuarioId, $limite, $offset);


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ClockIn: Dashboard</title>
</head>

<body class="dashbody">

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

    <main class="app-layout dashboard-layout">
        <section id="dados-usuario">
            <h2>Dados do usuário</h2>

            <p>
                <strong>Nome:</strong>
                <?php echo $_SESSION["usuario_nome"]; ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo $_SESSION["usuario_email"]; ?>
            </p>
            <form action="registrarponto.php" method="POST" id="formponto">

                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <button type="button" id="registrarponto">
                    Registrar Ponto
                </button>
                <p id="statusponto" role="status"></p>
            </form>

            <button type="button" id="testarcamera">
                Testar câmera
            </button>

            <div id="areadacamera" hidden>

                <h3>Câmera</h3>

                <video id="videoponto" autoplay playsinline muted></video>

                <button type="button" id="tirarfoto">
                    Tirar foto
                </button>

                <canvas id="pegafoto" hidden></canvas>

                <img id="previewfoto" alt="Foto capturada" hidden>

                <button type="button" id="confirmarponto" hidden>
                    Confirmar registro
                </button>

                <button type="button" id="cancelarponto" hidden>
                    Cancelar registro
                </button>

                <button type="button" id="voltarcamera" hidden>
                    Voltar
                </button>

            </div>

            <?php if ($_SESSION["usuario_perfil"] === "admin"): ?>

                <a
                    href="admin.php"
                    target="_blank"
                    rel="noopener noreferrer">
                    Painel Administrativo
                </a>

            <?php endif; ?>

            <button type="button" id="sair" onclick="window.location.href='logout.php'">
                Sair
            </button>
        </section>

        <section id="historico">
            <h2>Meus registros</h2>

            <div class="tabela-container">
                <table>

                    <colgroup>
                        <col class="col-data">
                        <col class="col-hora">
                        <col class="col-localizacao">
                        <col class="col-imagem">
                    </colgroup>

                    <tr>
                        <td>Data</td>
                        <td>Hora</td>
                        <td>Localização</td>
                        <td>Imagem</td>
                    </tr>

                    <?php foreach ($pontos as $registro): ?>

                        <tr>

                            <td>
                                <?php
                                echo date(
                                    "d/m/Y",
                                    strtotime($registro["data_hora"])
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    "H:i",
                                    strtotime($registro["data_hora"])
                                );
                                ?>
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
            <div class="paginacao">

                <?php if ($pagina > 1): ?>

                    <a href="?pagina=<?php echo $pagina - 1; ?>">
                        Anterior
                    </a>

                <?php endif; ?>


                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>

                    <a
                        href="?pagina=<?php echo $i; ?>"
                        class="<?php echo $i === $pagina ? 'pagina-ativa' : ''; ?>">
                        <?php echo $i; ?>
                    </a>

                <?php endfor; ?>


                <?php if ($pagina < $totalPaginas): ?>

                    <a href="?pagina=<?php echo $pagina + 1; ?>">
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

    <script src="js/camera.js"></script>
    <script src="js/ponto.js"></script>
    <script src="js/foto.js"></script>
</body>

</html>