<?php

session_start();

if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom']) || !isset($_SESSION['mail']) || !isset($_SESSION['password']) ) {
    header("location:index.php");
    exit;
}

$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$mail = $_SESSION['mail'];
$password = $_SESSION['password'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_verification.css">
    <title>Confirmer l'inscription</title>
</head>
<?php 

    $serveur = "localhost";
    $login = "root";
    $pass = "";

    $errmsg = "";

    function verif_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    try {
        $connexion = new PDO("mysql:host=$serveur;dbname=igs_db1", $login, $pass);
        $connexion -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "echec de connexion".$e->getMessage();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (!empty($_POST['code_insert']) && is_numeric($_POST['code_insert'])) {
            $code_insert = verif_input($_POST['code_insert']);

            $check = $connexion->prepare("SELECT code FROM table_code WHERE mail='$mail' ");
            $check -> execute();
            $resultat = $check-> fetchall();

            $col = array_column($resultat, 'code');

            if (in_array($code_insert, $col)) {
                $insert = $connexion->prepare(
                    "INSERT INTO users(nom, prenom, mail, password) 
                    VALUES(:nom, :prenom, :mail, :password)"
                );
                $insert->bindParam(":nom", $nom);
                $insert->bindParam(":prenom", $prenom);
                $insert->bindParam(":mail", $mail);
                $insert->bindParam(":password", $password);
                $insert->execute();

                session_unset();
                session_destroy();

                header("location:connexion.php");
                exit;
            }else {
                $errmsg = "Le code est incorrect";
            }

        }
        else {
            $errmsg = "Entrer le code reçu par mail";
        }
        
    }
    
?>
<body>
    <main>
        <section>
            <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input name="code_insert" type="text" placeholder="Entrer le code"> <span></span>
                <button type="submit">valider</button>
                <p>Verifiez vos mail pour avoir votre code</p>
                <span><?php echo $errmsg; ?></span>
            </form>
            
            <p>Pas de code? Inscrivez-vous <a href="inscription.php">ici</a></p>
        </section>

    </main>

</body>
</html>