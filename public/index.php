<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

$s3Client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'eu-west-3',
    'credentials' => [
        'key'    => $config['key'],
        'secret' => $config['secret']
    ]
]);

//$result = $s3Client->putObject([
//    'Bucket' => 'jonathans3',
//    'Key'    => '123456',
//    'Body'   => 'this is the body!'
//]);


//$result = $s3Client->getObject([
//    'Bucket' => 'jonathans3',
//    'Key'    => '123456'
//]);
//
//
//dump($result); die;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AWS PROJECT</title>
</head>
<body>
    <h1>AWS PROJECT</h1>
    <form action="save-image.php" method="POST" enctype="multipart/form-data">
        <label for="image">Sélectionner l'image à envoyer</label><br>
        <input type="file" name="image" id="image"><br>
        <input type="submit" value="Envoyer mon image">
    </form>
</body>
</html>
