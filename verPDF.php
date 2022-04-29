<?php
header("Content-Type: application/json;charset=utf-8");
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day

}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}


require_once('vendor/autoload.php');

// https://parzibyte.me/blog/2019/02/04/unir-combinar-archivos-pdf-php-libmergepdf/
use iio\libmergepdf\Merger;

$request = isset($_POST) && count($_POST) > 0 ? $_POST : json_decode(file_get_contents("php://input"), true);

$UPLOAD_DIR = 'uploads/' . $request['documento'] . '';

$combinador = new Merger;

$existeDocs = false;

foreach ($request['nomes_pdf'] as $arquivo) {
    if (file_exists("$UPLOAD_DIR/$arquivo.pdf")) {
        $existeDocs = true;
        $combinador->addFile('uploads/' . $request['documento'] . '/' . $arquivo . '.pdf');
    }
}

if ($existeDocs == false) {
    echo json_encode(['erro' => true, 'msg' => 'Não há nenhum destes arquivos no servidor.', 'arquivos'=>$request['nomes_pdf'] ]);
    die();
}

$pds_combinados = $combinador->merge();

$nome_novo_arquivo = $request['documento'] . ' - documentos.pdf';

header("Content-type:application/pdf");
header("Content-disposition: inline; filename=$nome_novo_arquivo");
header("content-Transfer-Encoding:binary");
header("Accept-Ranges:bytes");

echo $pds_combinados;
