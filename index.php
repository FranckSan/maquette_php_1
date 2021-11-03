<?php 
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style_connexion.css">
        <title>Connectez Vous</title>
    </head>
    <?php
        
        function verif_input($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $serveur = "localhost";
        $login = "root";
        $pass = "";

        try {
            $connexion = new PDO("mysql:host=$serveur;dbname=igs_db1", $login, $pass);
            $connexion -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            // echo "yes";
        } catch (PDOException $e) {
            echo "echec de connexion".$e->getMessage();
        }

        $password = $mail = "";
        $err_mail = $msg_err = $err_pass = "";
       

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
            if (!empty($_POST['mail'])) {
               
                $mail = verif_input($_POST['mail']);
                $mail = strtolower($mail);

            }else {
                $err_mail = "Veuillez entrer votre mail";
            }

            if (!empty($_POST['password'])) {
            
                $password = verif_input($_POST['password']);
                        
            }else {
                $err_pass = "Veuillez entrer votre mot de passe";
                
            }


            $take = $connexion->prepare("SELECT * FROM users WHERE mail='$mail' ");
            $take->execute();
            $resultat = $take->fetchAll();

            $col_mail = array_column($resultat, "mail");
            $col_pass = array_column($resultat, "password");
            $col_type = array_column($resultat, "type");
            $col_prenom = array_column($resultat, "prenom");
            

            if (in_array($mail, $col_mail)) {

                if (password_verify($password, $col_pass[0])) {
                    
                    if (in_array("admin", $col_type)) {

                        $_SESSION['prenom'] = $col_prenom[0];
                        $_SESSION['password'] = $col_pass[0];

                        header("location:admin_index.php");
                        exit;
                    }else {
    
                        $_SESSION['prenom'] = $col_prenom[0];
                        $_SESSION['password'] = $col_pass[0];
    
                        header("location:users_index.php");
                        exit;
                    }

                }else {
                    if (!empty($password)) {
                        $err_pass = "Mot de passe incorrect";
                    }
                }
               

            }else {

                if (!empty($mail) && !empty($password)) {
                    $msg_err = "Vous n'est pas inscrit";
                }
            }

            
        }



    ?>

    <body>
        <main>
            <div class="left">
                <img src="bg.png" alt="image de fond">
            </div>
            <div class="right">
                <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <h4>welcome</h4>
                    <div class="form_middle">
                        <p id="msg_err"><?php echo $msg_err; ?></p>
                        <label id="mail">E-mail <span><?php echo $err_mail ?></span>
                            <input id="mail" type="email" name="mail" placeholder="Exemple@mail.com" value="<?php echo $mail; ?>">
                        </label>
                        <label id="pass">Password <span><?php echo $err_pass ?></span>
                            <input id="password" name="password" type="password" placeholder="8+ caratères">
                        </label>
                        <button type="submit" >Login</button>
                    </div>
                    <div class="form_bottom">
                        <p>Create new account <a href="Inscription.php" id="lien">here</a></p>
                    </div>
                </form>
            </div>
        </main>
    </body>
 
</html>