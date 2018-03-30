<?php
require 'Personne.php';
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

use Aws\Rekognition\RekognitionClient;

if (isset($_POST)) {

    $rekognition = new RekognitionClient([
        'region'            => 'us-west-2',
        'version'           => '2016-06-27',
        'credentials' => [
            'key'    => $config['key'],
            'secret' => $config['secret']
        ]
    ]);

    $im = imagecreatefromjpeg($_POST['image']);
    imagejpeg($im, 'tmp-img/img-from-bucket.jpg');
    imagedestroy($im);
    $destination = 'tmp-img/img-from-bucket.jpg';

    $fp_image = fopen($destination, 'r');
    $image = fread($fp_image, filesize($destination));
    fclose($fp_image);

    $result = $rekognition->DetectFaces(array(
            'Image' => array(
                'Bytes' => $image,
            ),
            'Attributes' => array('ALL')
        )
    );

    $im = imagecreatefromjpeg($destination);
    $imgWidth = imagesx($im);
    $imgHeight = imagesy($im);

    $fd = $result['FaceDetails'][0];

    $cropW = $fd['BoundingBox']['Width'] * $imgWidth;
    $cropH = $fd['BoundingBox']['Height'] * $imgHeight;
    $cropX = $fd['BoundingBox']['Left'] * $imgWidth;
    $cropY = $fd['BoundingBox']['Top'] * $imgHeight;

    $im2 = imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
    if ($im2 !== FALSE) {
        imagejpeg($im2, "tmp-face/0.jpg");
    }


    $nom_du_fichier = 'personne.json';
    $prenom = $_POST['prenom'];
    $image = "tmp-face/0.jpg";
    $personne = new Personne($prenom, $image);
    $personnPhp = json_decode(file_get_contents($nom_du_fichier));
    $personneIsset = false;
    if ($personnPhp){
        foreach ($personnPhp as $key => $value) {
            if ($value->prenom == $prenom) {
                rename(__DIR__ . '/' . $value->image,__DIR__ . "/face/".$key.".jpg");
                $personne->image = "face/".$key.".jpg";
                $personnPhp[$key] = $personne;
                $personneIsset = true;
            }
        }
    }
    if ($personneIsset == false) {
        if ($personnPhp){
            $key = count($personnPhp);
        }else{
            $key = 0;
        }
        rename($personne->image, "face/".$key.".jpg");
        $personne->image = "face/".$key.".jpg";
        $personnPhp[] = $personne;
    }
    $personneJson = json_encode($personnPhp);
    file_put_contents($nom_du_fichier, $personneJson);
}
