<?php
require_once('./funcoes.php');
$documento = isset($_GET['documento']) ? $_GET['documento'] : null;
$erro = null;

if ($documento) {
    $documento = limpaCPF($documento);
    // die("Informe uma documento");
    $url = "https://minha.unifebe.edu.br/api/gad/documentacaoAluno.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['documento' => "$documento"]);
    $docs = json_decode(curl_exec($ch));
    // var_dump($docs);die;

    if (empty($docs)) {
        $erro = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>CPF inválido</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>';
    }
    
    $url = "https://minha.unifebe.edu.br/api/gad/tiposDocumentos.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $tiposDocs = json_decode(curl_exec($ch));
}





?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>GAD</title>
</head>

<body>
    <div class="container-fluid table-responsive">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">GAD - Gestão Acadêmica de Documentos</h1>
        </div>

        <?php
        if ($erro) {
            echo $erro;
        }
        ?>

        <section class="container">
            <form method="GET">
                <div class="form-group">
                    <label for="pesquisaDoc">Informe o CPF</label>
                    <input type="text" class="form-control" id="pesquisaDoc" name="documento" placeholder="insira aqui o CPF">
                    <!-- <small id="emailHelp" class="form-text text-muted">Nunca vamos compartilhar seu email, com ninguém.</small> -->
                </div>
                <button type="submit" id="pesquisar" class="btn btn-primary btn-sm mb-5">Pesquisar</button>
            </form>

            <form action="./verPDF.php" method="POST" target="_blank">
                <input type="hidden" name="documento" value="<?php echo $documento?>">
                <?php if ($documento && !$erro) : ?>
                    <dl class="row" id="docsList">
                        <dt class="col-sm-3">Nome</dt>
                        <dd class="col-sm-9"><?php echo $docs->nome ?></dd>

                        <dt class="col-sm-3">E-mail</dt>
                        <dd class="col-sm-9"><?php echo $docs->email ?></dd>
                    </dl>

                    <div class="form-group">
                        <label for="docsForMentor">Selecione um tipo de documento</label>
                        <select class="form-control" id="docsForMentor">
                            <option disabled selected>Selecione um documento</option>
                            <?php foreach ($tiposDocs as $tipos) : ?>
                                <option value="<?php echo RemoveCaracteresEspeciais($tipos->DESCRICAO) ?>"><?php echo $tipos->TIPO_DOC . " - " . $tipos->DESCRICAO ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <table class="table table-hover table-sm table-responsive-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col"><input onclick='verificaStatus(this)' name="checkTodos" type="checkbox"> #</th>
                                <th scope="col">Documento</th>
                                <th scope="col">Status</th>
                                <th scope="col">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docs->documentacao as $doc) : ?>
                                <tr>
                                    <td>
                                        <div class="form-group form-check">
                                            <input value="<?php echo RemoveCaracteresEspeciais($doc->descricao) ?>" name="nomes_pdf[]" type="checkbox" class="form-check-input">
                                        </div>
                                    </td>


                                    <td><b><?php echo $doc->descricao ?></b></td>
                                    <td><?php echo $doc->status ?></td>
                                    <td><?php echo  $doc->motivo ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif ?>
                <button type="submit" class="btn btn-info btn-sm"> Ver documentos </button>
            </form>

        </section>


    </div>
</body>
<script src="node_modules/scanner-js/dist/scanner.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.asprise.com/scannerjs/scanner.js"></script> -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="./checkbox.js"></script>

<script>
    var documento = '<?php echo $documento ?>'
    var doc_name;

    $('#docsForMentor').change(() => {
        // console.log("heeeey")
        doc_name = $('#docsForMentor').val();
        scan()
    })

    $(document).on('keypress', function(e) {
        if (e.which == 13) {
            $('#pesquisar').click();
        }
    });
</script>

<script type="text/javascript">
    // Need to upload scanned images to server or save them on hard disk? Please refer to the dev guide: http://asprise.com/document-scan-upload-image-browser/ie-chrome-firefox-scanner-docs.html
    // For more scanning code samples, please visit https://github.com/Asprise/scannerjs.javascript-scanner-access-in-browsers-chrome-ie.scanner.js

    var scanRequest = {
        "use_asprise_dialog": true, // Whether to use Asprise Scanning Dialog
        "show_scanner_ui": false, // Whether scanner UI should be shown
        "twain_cap_setting": { // Optional scanning settings
            "ICAP_PIXELTYPE": "TWPT_RGB" // Color
        },
        "output_settings": [{
            "type": "return-base64",
            "format": "jpg"
        }]
    };

    /** Triggers the scan */
    function scan() {
        scanner.scan(displayImagesOnPage, scanRequest);
    }

    /** Processes the scan result */
    function displayImagesOnPage(successful, mesg, response) {
        if (!successful) { // On error
            console.error('Failed: ' + mesg);
            return;
        }
        if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) { // User cancelled.
            console.info('User cancelled');
            return;
        }

        var scannedImages = scanner.getScannedImages(response, true, false); // returns an array of ScannedImage

        let imagens = [];

        for (var i = 0;
            (scannedImages instanceof Array) && i < scannedImages.length; i++) {
            var scannedImage = scannedImages[i];
            imagens.push(scannedImage.src);
            // var elementImg = scanner.createDomElementFromModel({
            //     'name': 'img',
            //     'attributes': {
            //         'class': 'scanned',
            //         'src': scannedImage.src
            //     }
            // });


            // (document.getElementById('images') ? document.getElementById('images') : document.body).appendChild(elementImg);
        }

        salvarDoc(doc_name, imagens, documento);
    }

    function salvarDoc(doc_name, file, documento) {
        axios.post('https://www5.unifebe.edu.br/gad/upload.php', {
                documento: documento,
                nomeArquivo: doc_name,
                file64: file
            })
            .then(function(response) {
                alert(response.data.msg);
                // console.log(response)
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    setTimeout(() => {
        $('.close').click();
    }, 2000);
</script>

</html>