<?php
require dirname(__DIR__) . '/vendor/autoload.php';


$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

dump($s3);

?>

<h1>AWS PROJECT</h1>
