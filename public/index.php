<?php
require dirname(__DIR__) . '/vendor/autoload.php';


echo '<h1>AWS PROJECT</h1>';

$s3Client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'eu-west-3',
    'credentials' => [
        'key'    => 'AKIAJ7GWMKO7XG5YDTJA',
        'secret' => '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'
    ]
]);

$result = $s3Client->putObject([
    'Bucket' => 'jonathans3',
    'Key'    => '123456',
    'Body'   => 'this is the body!'
]);


$result = $s3Client->getObject([
    'Bucket' => 'jonathans3',
    'Key'    => '123456'
]);


dump($result); die;

?>