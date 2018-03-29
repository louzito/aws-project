<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

echo '<h1>AWS PROJECT</h1>';

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


$result = $s3Client->getObject([
    'Bucket' => 'jonathans3',
    'Key'    => '123456'
]);


dump($result); die;

?>