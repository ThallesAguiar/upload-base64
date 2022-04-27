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

use Dompdf\Dompdf;



$request = isset($_POST) && count($_POST) > 0 ? $_POST : json_decode(file_get_contents("php://input"), true);


// $documento = $request['documento'];
// $nome = $request['nomeArquivo'];
$files = $request['file64'];

$documento = "123456789";
$nome = "CPF";

$UPLOAD_DIR = "uploads/$documento";

$html = '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAD - '.$nome.'</title>
</head>
<body>';

foreach ($files as $file) {
    $html .= "<img style='max-width: 90%;' src='$file'>";
}

$html .='</body></html>';


$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->setIsHtml5ParserEnabled(true);
$dompdf->setOptions($options);
$dompdf->loadHtml($html);
$dompdf->render();

$file = $dompdf->output();





if (is_dir($UPLOAD_DIR)) {
    // echo "A Pasta Existe";
    if (file_exists("$UPLOAD_DIR/$nome.pdf")) {
        // echo "Arquivo encontrado"; DELETA ELE E SUBSTITUI          
        if (unlink("$UPLOAD_DIR/$nome.pdf")) {
            file_put_contents("$UPLOAD_DIR/$nome.pdf", $file, true);
            echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
            // die();
        }
    } else {
        // echo "Arquivo não encontrado"; CRIA O ARQUIVO
        file_put_contents("$UPLOAD_DIR/$nome.pdf", $file, true);
        echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
        // die();
    }
} else {
    // echo "A Pasta não Existe"; CRIA A PASTA E O ARQUIVO
    mkdir($UPLOAD_DIR, 0755);
    file_put_contents("$UPLOAD_DIR/$nome.pdf", $file, true);
    echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
    // die();
}

















// // foreach($files as $file){

    
    
    // if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
    //     $file = substr($file, strpos($file, ',') + 1);
    //     $type = strtolower($type[1]); // jpg, png, gif
    
    //     if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
    //         // throw new \Exception('invalid image type');
    //         echo json_encode(['erro' => true, 'msg' => 'invalid image type']);
    //         die();
    //     }
    //     $file = str_replace(' ', '+', $file);
    //     $file = base64_decode($file);
    
    //     if ($file === false) {
    //         // throw new \Exception('base64_decode failed');
    //         echo json_encode(['erro' => true, 'msg' => 'base64_decode failed']);
    //         die();
    //     }
    
    //     if (is_dir($UPLOAD_DIR)) {
    //         // echo "A Pasta Existe";
    //         if (file_exists("$UPLOAD_DIR/$nome.{$type}")) {
    //             // echo "Arquivo encontrado"; DELETA ELE E SUBSTITUI          
    //             if (unlink("$UPLOAD_DIR/$nome.{$type}")) {
    //                 file_put_contents("$UPLOAD_DIR/$nome.{$type}", $file, true);
    //                 echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
    //                 die();
    //             }
    //         } else {
    //             // echo "Arquivo não encontrado"; CRIA O ARQUIVO
    //             file_put_contents("$UPLOAD_DIR/$nome.{$type}", $file, true);
    //             echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
    //             die();
    //         }
    //     } else {
    //         // echo "A Pasta não Existe"; CRIA A PASTA E O ARQUIVO
    //         mkdir($UPLOAD_DIR, 0755);
    //         file_put_contents("$UPLOAD_DIR/$nome.{$type}", $file, true);
    //         echo json_encode(['erro' => false, 'msg' => 'Arquivo salvo com sucesso']);
    //         die();
    //     }
    // } else {
    //     throw new \Exception('did not match data URI with image data');
    //     echo json_encode(['erro' => true, 'msg' => 'did not match data URI with image data']);
    //     die();
    // }
// // }
