<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

use Aws\Rekognition\RekognitionClient;

$rekognition = new RekognitionClient([
    'region'            => 'us-west-2',
    'version'           => '2016-06-27',
    'credentials' => [
        'key'    => $config['key'],
        'secret' => $config['secret']
    ]
]);


$fileNameImg = 'img2.jpg';
$jpeg_image = imagecreatefromjpeg($fileNameImg);
imagejpeg($jpeg_image, $fileNameImg);
imagedestroy($jpeg_image);

$fp_image = fopen($fileNameImg, 'r');
$image = fread($fp_image, filesize($fileNameImg));
fclose($fp_image);

$result = $rekognition->DetectFaces(array(
        'Image' => array(
            'Bytes' => $image,
        ),
        'Attributes' => array('ALL')
    )
);

$im = imagecreatefromjpeg($fileNameImg);
$imgWidth = imagesx($im);
$imgHeight = imagesy($im);

foreach ($result['FaceDetails'] as $key => $fd) {
    $cropW = $fd['BoundingBox']['Width'] * $imgWidth;
    $cropH = $fd['BoundingBox']['Height'] * $imgHeight;
    $cropX = $fd['BoundingBox']['Left'] * $imgWidth;
    $cropY = $fd['BoundingBox']['Top'] * $imgHeight;

    $im2 = imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
    if ($im2 !== FALSE) {
        imagejpeg($im2, "visage2-$key.jpg");
    }
}

$cmpImg1 = fopen('visage-0.jpg', 'r');
$image1 = fread($cmpImg1, filesize('visage-0.jpg'));
fclose($cmpImg1);

$cmpImg2 = fopen('visage2-0.jpg', 'r');
$image2 = fread($cmpImg2, filesize('visage2-0.jpg'));
fclose($cmpImg2);

$result = $rekognition->compareFaces([
    'SimilarityThreshold' => 50.0,
    'SourceImage' => [
        'Bytes' => $image1,
    ],
    'TargetImage' => [
        'Bytes' => $image2,
    ],
]);

dump($result);die;