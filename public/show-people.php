<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$personnes = json_decode(file_get_contents('personne.json'));

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
        .user-to-save .form-container p { padding-left: 15px; }
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
    <a href="index.php" title="Ajouter personnes">Ajouter personnes</a>
    <a href="show-people.php" title="Ajouter personnes">Voir personnes</a>
    <a href="compare-people.php" title="">Comparer personnes</a>
</nav>
<div id="result-detect-face">
    <?php if (isset($personnes)) { ?>
        <?php foreach ($personnes as $pers) { ?>
            <div class="user-to-save">
                <div class="img-container">
                    <img src="<?php echo $pers->image; ?>">
                </div>
                <div class="form-container">
                    <p><?php echo $pers->prenom; ?></p>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>