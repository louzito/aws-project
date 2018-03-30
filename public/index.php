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
    <title>Identifier personne</title>
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
        .user-to-save { margin: 10px 0;}
        .user-to-save .img-container {
            display: inline-block;
            height: 80px;
            vertical-align: top;
        }
        .user-to-save .img-container img {
            height: 100%;
        }
        .user-to-save .form-container {
            line-height: 80px;
            display: inline-block;
            vertical-align: top;
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
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <label for="image">Insérer une image avec des visages :</label><br>
        <input type="file" name="image" id="image"><br>
        <input type="submit" value="Envoyer mon image">
    </form>
    <div id="result-detect-face">
    <?php if (isset($result) && $result['FaceDetails']) { ?>
        <?php foreach($result['FaceDetails'] as $key => $fd) { ?>
        <div class="user-to-save">
            <div class="img-container">
                <img src="<?php echo "tmp-face/$key.jpg"; ?>">
            </div>
            <div class="form-container">
                <input type="hidden" name="image" class="image" value="<?php echo "tmp-face/$key.jpg"; ?>">
                <input type="text" name="prenom" class="prenom">
                <input type="submit" class="save-face" value="Enregistrer">
            </div>
        </div>
        <?php } ?>
    <?php } ?>
    </div>
    <script>
        $('.save-face').click(function(e) {
            var userToSaveElt = $(this).closest('.user-to-save');

            var params = {
                'image' : $(userToSaveElt).find('.image').val(),
                'prenom' : $(userToSaveElt).find('.prenom').val()
            };

            $.ajax({
                type: "POST",
                url: 'ajax-save-face.php',
                data: params,
                success: function (retour) {
                    $(userToSaveElt).remove();
                }
            });
        });
    </script>
</body>
</html>