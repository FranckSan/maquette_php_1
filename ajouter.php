<?php
session_start();

if (!isset($_SESSION['prenom']) || !isset($_SESSION['password'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_ajouter.css">
    <title>Ajouter | Dashboard</title>
</head>
<?php

$serveur = "localhost";
$login = "root";
$pass = "";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=igs_db1", $login, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "pass";
} catch (PDOException $e) {
    echo "echec de connexion" . $e->getMessage();
}

$take = $connexion->prepare("SELECT * FROM users ");
$take->execute();
$resultat = $take->fetchAll();

$leng = count($resultat);
$tb_id = array_column($resultat, 'id');
$tb_nom = array_column($resultat, 'nom');
$tb_prenom = array_column($resultat, 'prenom');
$tb_mail = array_column($resultat, 'mail');
$tb_type = array_column($resultat, 'type');

function verif_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errnom = $errprenom = $errmail = $errpassword = $errpassword2 = $errid = $errtype = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['type'])) {
        $type = verif_input($_POST['type']);
    }else {
        $errtype = "cocher une case";
    }

    if (!empty($_POST["new_nom"])) {        

        if (preg_match("/^[a-zA-Z-']*$/",$_POST["new_nom"])) {    

            $nom = verif_input($_POST["new_nom"]);

        }else {
            $errnom = "Seules les lettres sont autorisées";
        }
    }
    else {
        $errnom = "Entrer un nom";
    }

    if (!empty($_POST["new_prenom"])) {

        if (preg_match("/^[a-zA-Z-' ]*$/",$_POST["new_prenom"])) {

            $prenom = verif_input($_POST["new_prenom"]);
        
        }else {
            $errprenom = "Seules les lettres sont autorisées";
        }
    }else {
        $errprenom = "Entrer un prenom";
    }

    if (!empty($_POST["new_mail"])) {
        
        if (filter_var($_POST["new_mail"], FILTER_VALIDATE_EMAIL)) {
            
            $mailv = verif_input($_POST["new_mail"]);
            $mail = strtolower($mailv);
            echo $mail;
        }else {
            $errmail = "Mail invalide";
        }
    }else {
        $errmail = "Entrer le mail";
    }

    

    if (empty($_POST["new_password"])) {

        $errpassword = "Entrer un mot de passe";

    }else {

        if (strlen($_POST["new_password"]) >= 8) {

            if (!preg_match("/[A-Z]/", $_POST["new_password"]) ) {

                $errpassword2 = "Doit contenir au moins une lettre majuscule";
                $errpassword = "incorrect";

            }else {

                if (!preg_match("/[0-9]/", $_POST["new_password"])) {

                    $errpassword2 = "Doit contenir au moins un chiffre";
                    $errpassword = "incorrect";
                }else {
                    
                    $password1 = verif_input($_POST["new_password"]);
                    $option = [
                        'cost' => 12,
                    ];
                    $_password = password_hash($password1, PASSWORD_BCRYPT, $option);
                }
            }
        }
        else {
            $errpassword = "Mot de passe trop court";
        }
    }

    if (!empty($nom) && !empty($prenom) && !empty($mail) && !empty($password)) {

        $insert = $connexion->prepare("INSERT INTO users(nom, prenom, mail, password, type ) VALUES(:nom, :prenom, :mail, :password, :type) ");
        $insert->bindParam(":nom", $nom);
        $insert->bindParam(":prenom", $prenom);
        $insert->bindParam(":mail", $mail);
        $insert->bindParam(":password", $password);
        $insert->bindParam(":type", $type);
        $insert->execute();
        header('location:ajouter.php');
    }else {
        $errid = "réessayez";
    }
}

?>

<body>
    <main>
        <section class="top">
            <div class="left">
                <h1>Bienvenue <?php echo $_SESSION['prenom']; ?></h1>
            </div>
            <div class="form" >
                <a href="admin_index.php"><button >Acceuil</button></a>
            </div>
        </section>
        <section class="middle">
            <table>
                <tr>
                    <caption>Table Users</caption>
                    <th>ID</th>
                    <th>NOM</th>
                    <th>PRENOM</th>
                    <th>EMAIL</th>
                    <th>TYPE</th>
                </tr>
                <?php
                for ($i = 0; $i <= $leng - 1; $i++) {
                    echo "<tr>";
                    echo "<td>" . $tb_id[$i] . "</td>";
                    echo "<td>" . $tb_nom[$i] . "</td>";
                    echo "<td>" . $tb_prenom[$i] . "</td>";
                    echo "<td>" . $tb_mail[$i] . "</td>";
                    echo "<td>" . $tb_type[$i] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </section>
        <section class="btn">
            <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="modifier">

                <div class="btn_top">
                    <div class="champs">
                        <label for="new_nom" class="label_input">Nom <span><?php echo $errnom ?></span>
                            <input type="text" name="new_nom" class="form_input">
                        </label>
                        <label for="new_mail" class="label_input">Email <span><?php echo $errmail ?></span>
                            <input type="text" name="new_mail" class="form_input">
                        </label>
                    </div>
                    <div class="champs">
                        <label for="new_prenom" class="label_input">Prenom <span><?php echo $errprenom ?></span>
                            <input type="text" name="new_prenom" class="form_input">
                        </label>
                        <label for="new_password" class="label_input">Mot de passe <span><?php echo $errpassword ?></span>
                            <input type="text" name="new_password" class="form_input" placeholder="<?php echo $errpassword2;?>">
                        </label>
                    </div>
                    
                </div>    
                    <div class="btn_middle">
                        <label for="admin" class="radio">ADMIN 
                            <input type="radio" name="type" value="admin" id="admin" class="radio_input">
                        </label>
                        <label for="user" class="radio">USER
                            <input type="radio" name="type" value="user" id="user" class="radio_input"><span><?php echo $errtype ?></span>
                        </label>
                    </div>
                    
                
                
                <div class="btn_bottom">
                    <button type="submit">Ajouter</button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>