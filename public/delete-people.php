<?php
require 'Personne.php';
require dirname(__DIR__) . '/vendor/autoload.php';

if (isset($_POST)) {
    $personnes = json_decode(file_get_contents('personne.json'));

    $personFound = false;
    foreach ($personnes as $key => $personne) {
        if($personFound && file_exists($key .'.jpg')) {
            rename(__DIR__ . '/face/' . $key . '.jpg', __DIR__ . '/face/' . ($key-1) . '.jpg');
        }
        if ($personne->prenom == $_POST['prenom']) {
            unset($personnes[$key]);
            $personFound = true;
        }
    }

    $personnes = array_values($personnes);

    file_put_contents('personne.json', json_encode($personnes));
}
