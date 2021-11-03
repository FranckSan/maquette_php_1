<?php


$serveur = "localhost";
$login = "root";
$pass = "";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=igs_db1", $login, $pass);
    $connexion -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // echo "ELASSA";
} catch (PDOException $e) {
    echo "pfff".$e->getMessage();
}


?>