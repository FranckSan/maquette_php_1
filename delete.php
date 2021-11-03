<?php
session_start();

if (!isset($_SESSION['prenom']) || !isset($_SESSION['password'])) {
    header("location:connexion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_delete.css">
    <title>Delete</title>
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

$errnom = $errprenom = $errmail = $errpassword = $errid = $errtype = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ( !empty($_POST['id_select']) && is_numeric($_POST['id_select']) ) {

        $id = verif_input($_POST['id_select']);

        if (in_array($id,$tb_id)) {

            $take2 = $connexion->prepare("SELECT type FROM users  WHERE id='$id' ");
            $take2->execute();
            $resultat = $take2->fetchAll();
            $col_type = array_column($resultat, 'type');

            if (in_array("user",$col_type)) {

                $type = "yes";
            } else {
                $errid = "vous n'etes pas autorisé.e";
            }

        }else {
            $errid = "Entrer un id valide";
        }
        

    }else {
        $errid = "Entrer un id valide";
    }

    


    
        
    

    if (!empty($id) && !empty($type)) {
        
        $delete = $connexion->prepare("DELETE FROM users WHERE id='$id' ");
        $delete->execute();
        header('location:delete.php');
    }
}

?>

<body>
    <main>
        <section class="top">
            <div class="left">
                <h1>Bienvenue <?php echo $_SESSION['prenom']; ?></h1>
            </div>
            <div class="form">
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
                    <label class="label_id" for="number">Entrer l'ID de utilisateur <span><?php echo $errid ?></span>
                        <input type="number" name="id_select" id="small_input">
                    </label>
                </div>

                <div class="btn_bottom">
                    <button type="submit">Modifier</button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>