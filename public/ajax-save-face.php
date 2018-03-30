<?php
require 'Personne.php';
require dirname(__DIR__) . '/vendor/autoload.php';
if (isset($_POST)) {
    $nom_du_fichier = 'personne.json';
    $prenom = $_POST['prenom'];
    $image = $_POST['image'];
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
