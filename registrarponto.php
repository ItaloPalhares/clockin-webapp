<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/ponto.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$latitude = $_POST["latitude"] ?? null;
$longitude = $_POST["longitude"] ?? null;

if (
    !is_numeric($latitude) || !is_numeric($longitude) ||
    $latitude < -90 || $latitude > 90 ||
    $longitude < -180 || $longitude > 180
) {
    die("Localização inválida.");
}

if (
    !isset($_FILES["imagem"]) || $_FILES["imagem"]["error"] !== UPLOAD_ERR_OK
){
    http_response_code(400);
    die("Foto inválida");
}

$imagem = $_FILES["imagem"];

if ($imagem["size"] <= 0 || $imagem["size"] > 3 *1024 *1024 ) {
    http_response_code(400);
    die("Foto deve possuir no maximo 3MB");
}

$arquivoTemporario = $imagem["tmp_name"];

if (!is_uploaded_file($arquivoTemporario)) {

    http_response_code(400);
    die("Upload inválido.");

}

$formatoInfo = new finfo(FILEINFO_MIME_TYPE);

$tipoArquivo = $formatoInfo->file($arquivoTemporario);

if (
    $tipoArquivo !== "image/jpeg" ||
    getimagesize($arquivoTemporario) === false
) {

    http_response_code(400);
    die("Por favor enviar arquivo compativel. Exemplo: JPEG");

}

$diretorio = dirname(__DIR__, 2) . "/pontoapp_uploads";

if (!is_dir($diretorio)) {

    if (!mkdir($diretorio, 0750, true) && !is_dir($diretorio)) {

        http_response_code(500);
        die("Não foi possível criar o diretório.");

    }
}

$nomeArquivo = bin2hex(random_bytes(16)) . ".jpg";

$caminhoCompleto = $diretorio . "/" . $nomeArquivo;

if (!move_uploaded_file($arquivoTemporario, $caminhoCompleto)) {

    http_response_code(500);
    die("Não foi possível mover arquivo.");

}

try {

    $ponto = new Ponto($pdo);

    $ponto->registrar(
        $_SESSION["usuario_id"], (float) $latitude, (float) $longitude, $nomeArquivo );

         echo "Ponto registrado com sucesso.";


} catch (Throwable $erro) {

    if (is_file($caminhoCompleto)) {
        unlink($caminhoCompleto);
    }

    error_log($erro->getMessage());

    http_response_code(500);

    echo "Falha ao registrar o ponto.";

}

exit;

?>