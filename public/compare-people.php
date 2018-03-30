<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

use Aws\Rekognition\RekognitionClient;

if (isset($_POST) && isset($_FILES['image'])) {
    $rekognition = new RekognitionClient([
        'region'            => 'us-west-2',
        'version'           => '2016-06-27',
        'credentials' => [
            'key'    => $config['key'],
            'secret' => $config['secret']
        ]
    ]);

    $myImage = $_FILES['image'];
    $extension = pathinfo($myImage['name'],PATHINFO_EXTENSION);
    $destination = 'tmp-img/' . uniqid() . '.' . $extension;
    move_uploaded_file($myImage['tmp_name'] , $destination);

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

    foreach ($result['FaceDetails'] as $key => $fd) {
        $cropW = $fd['BoundingBox']['Width'] * $imgWidth;
        $cropH = $fd['BoundingBox']['Height'] * $imgHeight;
        $cropX = $fd['BoundingBox']['Left'] * $imgWidth;
        $cropY = $fd['BoundingBox']['Top'] * $imgHeight;

        $im2 = imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
        if ($im2 !== FALSE) {
            imagejpeg($im2, "tmp-face/$key.jpg");
        }
    }

    $personnes = json_decode(file_get_contents('personne.json'));

    $personnesTrouver = [];

    foreach ($result['FaceDetails'] as $key => $fd) {
        $cmpImg1 = fopen("tmp-face/$key.jpg", 'r');
        $image1 = fread($cmpImg1, filesize("tmp-face/$key.jpg"));
        fclose($cmpImg1);
        $personneTrouver = false;
        $prevSimilarity = 0;
        foreach ($personnes as $personne) {
            $cmpImg2 = fopen($personne->image, 'r');
            $image2 = fread($cmpImg2, filesize($personne->image));
            fclose($cmpImg2);

            try {
                $result = $rekognition->compareFaces([
                    'SimilarityThreshold' => 70.0,
                    'SourceImage' => [
                        'Bytes' => $image1,
                    ],
                    'TargetImage' => [
                        'Bytes' => $image2,
                    ],
                ]);
            } catch (Exception $e) {
//                $e->getMessage();
            }

            if (isset($result['FaceMatches']) && count($result['FaceMatches']) > 0 && $result['FaceMatches'][0]["Similarity"] > 70 && $result['FaceMatches'][0]["Similarity"] > $prevSimilarity) {
                $prevSimilarity = $result['FaceMatches'][0]["Similarity"];
                $personneTrouver = $personne->prenom;
            }
        }

        if ($personneTrouver) {
            if (!in_array($personneTrouver, $personnesTrouver)) {
                $personnesTrouver[] = $personneTrouver;
            }
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title>Compare personne</title>
    <style>
        #result-detect-face, form {
            margin: 20px;
            padding: 20px;
            text-align: center;
        }
        form label {
            font-size: 22px;
            font-weight: bold;
            padding: 10px;
            display: block;
        }
        nav {
            text-align: center;
        }
        nav a {
            display: inline-block;
            height: 45px;
            line-height: 45px;
            background: dodgerblue;
            text-align: center;
            text-transform: uppercase;
            text-decoration: none;
            color: #FFFFFF;
            font-weight: bold;
            padding: 0 10px;
        }
        nav a:hover {
            background: #101fff;
        }
    </style>
</head>
<body>
<nav>
    <a href="bucket-people.php" title="Bucket personnes">Bucket personnes</a>
    <a href="index.php" title="Ajouter personnes">Ajouter personnes</a>
    <a href="show-people.php" title="Ajouter personnes">Voir personnes</a>
    <a href="compare-people.php" title="">Comparer personnes</a>
</nav>
<form action="compare-people.php" method="POST" enctype="multipart/form-data">
    <label for="image">Comparer des visages :</label><br>
    <input type="file" name="image" id="image"><br>
    <input type="submit" value="Envoyer mon image">
</form>
<div id="result-detect-face">
    <?php if (isset($personnesTrouver)) { ?>
        <?php foreach ($personnesTrouver as $pers) { ?>
            <span class="personne"><?php echo $pers; ?></span><br />
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>