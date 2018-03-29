<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title>Identifier personne</title>
</head>
<body>
    <form action="detect-face.php" method="POST" enctype="multipart/form-data">
        <label for="image">Insérer une image avec des visages :</label><br>
        <input type="file" name="image" id="image"><br>
        <input type="submit" value="Envoyer mon image">
    </form>
    <div id="result-detect-face"></div>
    <script>

    </script>
</body>
</html>