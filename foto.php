<?php

session_start();

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/ponto.php";

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    exit;
}

$pontoId = $_GET["id"] ?? null;

if (!is_numeric($pontoId) || $pontoId < 1) {
    http_response_code(400);
    exit;
}

$pontoId = (int) $pontoId;

$ponto = new Ponto($pdo);

$registro = $ponto->buscarPorId($pontoId);

if (!$registro) {
    http_response_code(404);
    exit;
}

$dono = (int) $registro["users_id"] === (int) $_SESSION["usuario_id"];

$admin = $_SESSION["usuario_perfil"] === "admin";

if (!$dono && !$admin) {
    http_response_code(403);
    exit;
}

$nomeImagem = $registro["imagem"];

if (empty($nomeImagem)) {
    http_response_code(404);
    exit;
}
$diretorio = dirname(__DIR__, 2) . "/pontoapp_uploads";

$caminhoImagem = $diretorio . "/" . basename($nomeImagem);
if (!is_file($caminhoImagem)) {
    http_response_code(404);
    exit;
}

header("Content-Type: image/jpeg");
header("Content-Length: " . filesize($caminhoImagem));
header("Cache-Control: private, no-store, max-age=0");
header("X-Content-Type-Options: nosniff");

readfile($caminhoImagem);

exit;
