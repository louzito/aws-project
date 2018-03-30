<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/config.php';

use Aws\Rekognition\RekognitionClient;

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$s3Client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'eu-west-3',
    'credentials' => [
        'key'    => $config['key'],
        'secret' => $config['secret']
    ]
]);

$result = $s3Client->listObjects(array(
    'Bucket' => 'jonathans3'
));

$nbPage = floor(count($result['Contents'])/10);

$peopleImg = [];

foreach ($result['Contents'] as $key => $img) {
    if ($key >= ($page*10-10) && $key < ($page*10)) {
        if (strpos($img['Key'], 'photos/') !== false) {
            $resultObj = $s3Client->getObject([
                'Bucket' => 'jonathans3',
                'Key'    => $img['Key']
            ]);
            $peopleImg[] = $resultObj['@metadata']['effectiveUri'];
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
        .pagination {
            text-align: center;
            padding-left: 20px 0;
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
<div id="result-detect-face">
    <?php if (isset($peopleImg)) { ?>
        <?php foreach($peopleImg as $key => $pi) { ?>
            <div class="user-to-save">
                <div class="img-container">
                    <img src="<?php echo $pi; ?>">
                </div>
                <div class="form-container">
                    <input type="hidden" name="image" class="image" value="<?php echo $pi; ?>">
                    <input type="text" name="prenom" class="prenom">
                    <input type="submit" class="save-face" value="Enregistrer">
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<div class="pagination">
    <p>Pages</p>
    <?php for($i = 1; $i <= $nbPage; $i++) : ?>
    <a href="bucket-people.php?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? "current-page" : "" ; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
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
            url: 'ajax-save-face-from-bucket.php',
            data: params,
            success: function (retour) {
                $(userToSaveElt).remove();
            }
        });
    });
</script>
</body>
</html>

