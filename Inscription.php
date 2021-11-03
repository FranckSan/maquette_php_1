<?php 
    session_start();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_inscription.css">
    <title>Inscrivez vous</title>
</head>
<?php 

    include "config.php";

    $nom = $prenom = $mail = $password = $password1 = "";
    $errnom = $errprenom = $errmail = $errpassword = $errpassword1 = "";
    //Securisation
    function verif_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }



    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //verification du champ contenant le nom de l'utilisateur et creaion de $nom

        if (!empty($_POST["nom"])) {        

            if (preg_match("/^[a-zA-Z-']*$/",$_POST["nom"])) {    

                $nom = verif_input($_POST["nom"]);
                $_SESSION['nom'] = $nom;

            }else {
                $errnom = "Seules les lettres sont autorisées";
            }
        }
        else {
            $errnom = "Entrer votre nom";
        }



        if (!empty($_POST["prenom"])) {

            if (preg_match("/^[a-zA-Z-' ]*$/",$_POST["prenom"])) {

                $prenom = verif_input($_POST["prenom"]);
                $_SESSION['prenom'] = $prenom;
            
            }else {
                $errprenom = "Seules les lettres sont autorisées";
            }
        }else {
            $errprenom = "Entrer votre prenom";
        }
        

        
        if (!empty($_POST["mail"])) {

            if (filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {

                $mail = verif_input($_POST["mail"]);
                $mail = strtolower($mail);
                $_SESSION['mail'] = $mail;

            }else {
                $errmail = "E-mail invalide";
            }
        }else {
            $errmail = "Entrer votre mail";
        }

        

        if (empty($_POST["password"])) {

            $errpassword = "Entrer un mot de passe";

        }else {

            if (strlen($_POST["password"]) >= 3) {

                if (!preg_match("/[A-Z]/", $_POST["password"]) ) {

                    $errpassword = "Doit contenir au moins une lettre majuscule";

                }else {

                    if (!preg_match("/[0-9]/", $_POST["password"])) {

                        $errpassword = "Doit contenir au moins un chiffre";
                    }
                }
            }
            else {
                $errpassword = "Mot de passe trop court";
            }
        }

        if (!empty($_POST["password1"])) {
            if ($_POST["password"] === $_POST["password1"]) {

                $password = verif_input($_POST["password"]);
                $option = [
                    'cost' => 12,
                ];
                $_SESSION['password'] = password_hash($password, PASSWORD_BCRYPT, $option);
                $affiche_pass = $_SESSION['password'];
            }
            else {
                $errpassword1 = "Mot de passe different";
            }
        }else {
            $errpassword1 = "Confirmer le mot de passe <br>";
        }


        if (!empty($nom) && !empty($prenom) && !empty($mail) && !empty($password) ) {

            $check = $connexion->prepare('SELECT mail FROM users WHERE mail=? ');
            $check-> execute(array($mail));
            $resultat = $check-> fetchall();

            if (count($resultat) < 1) {
                
                $code =  mt_rand(1000,9999);

                // Le message
                $message = "Pour valider votre inscription veillez renseigner le code\r\n<span>Code de confirmation: $code </span>\r\nLe code a une durée de vie limitée, \r\nveillez le renseigner dès sa reception\r\nSi vous n'avez plus accès à la page de verification \r\nveillez reprendre l'inscription\r\n\r\nVotre mot de passe est: $affiche_pass";

                // Envoi du mail
                mail($mail, 'Confimer votre compte', $message);
                
                $insert = $connexion->prepare(
                    "INSERT INTO table_code(code, mail) 
                    VALUES(:code, :mail) "
                    );
                $insert->bindParam(":code", $code);
                $insert->bindParam(":mail", $mail);
                $insert->execute();
                
                header("location:verification.php");
                exit;

            }else {
                $errmail = "Vous etes déja inscris";
            }
        }
    }
?>
<body>
    <main>
        <div class="left">
            <img src="bg_ins.jpg" alt="image de fond">
            
        </div>
        <div class="right">
        
            <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <h4>Create Your Account</h4>
                <div class="form_middle">
                    <label for="nom">Name<span><?php echo $errnom ?></span>
                        <input name="nom" id="nom" type="text" placeholder="San" value="<?php echo $nom ?>">
                    </label>
                    <label for="prenom">First Name<span><?php echo $errprenom ?></span>
                        <input name="prenom" id="prenom" type="text" placeholder="Jean Michelle" value="<?php echo $prenom ?>">
                    </label>
                    <label for="mail">E-mail<span><?php echo $errmail ?></span>
                        <input name="mail" id="mail" type="text" placeholder="xyz@mail.com" value="<?php echo $mail ?>">
                    </label>
                    <label for="password">Password<span><?php echo $errpassword ?></span>
                        <input name="password" id="password" type="password" placeholder="8+ characteres avec moins une lettre majuscule et un chiffre">
                    </label>
                    <label for="password1">Confirm Password<span><?php echo $errpassword1 ?></span>
                        <input name="password1" id="password1" type="password" placeholder="Retype password">
                    </label>
                    <button type="submit" id="log">Login</button>
                </div>
                <div class="form_bottom">
                    <p>Login <a href="index.php" id="lien">Here</a></p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>