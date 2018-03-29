<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

use Aws\Rekognition\RekognitionClient;


$s3Client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'eu-west-3',
    'credentials' => [
        'key'    => $config['key'],
        'secret' => $config['secret']
    ]
]);


$rekognition = new RekognitionClient([
    'region'            => 'us-west-2',
    'version'           => '2016-06-27',
    'credentials' => [
        'key'    => $config['key'],
        'secret' => $config['secret']
    ]
]);

$imageUrl = false;

if (isset($_POST) && isset($_FILES['image'])) {
    $myImage = $_FILES['image'];
    $extension = pathinfo($myImage['name'],PATHINFO_EXTENSION);
    $keyname = uniqid() . '.' . $extension;
    $filepath = $myImage['tmp_name'];
    try {
        $result = $s3Client->putObject(array(
            'Bucket' => 'jonathans3',
            'Key'    => $keyname,
            'SourceFile'   => $filepath,
            'ACL'    => 'public-read'
        ));
        $imageUrl = $result['ObjectURL'];
    } catch (S3Exception $e) {
        echo $e->getMessage() . "\n";
    }
} else {
    echo 'Une erreur c\'est produite.';
}

if ($imageUrl) {
    $fileNameImg = 'upload/image-a-crop.jpg';
    $jpeg_image = imagecreatefromjpeg($imageUrl);
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
            imagejpeg($im2, "image-crop/visage-$key.jpg");
        }
    }

    foreach ($result['FaceDetails'] as $key => $fd) {
        echo '<img src="image-crop/visage-'.$key.'.jpg"><br>';
    }
}