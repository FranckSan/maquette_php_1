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
    <link rel="stylesheet" href="style_admin.css">
    <title>Acceuil | Dashboard</title>
</head>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        header("location:session_end.php");
        exit;
    }


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

    $take2 = $connexion->prepare("SELECT * FROM users ");
    $take2->execute();
    $resultat = $take2->fetchAll();

    $leng = count($resultat);
    $id = array_column($resultat, 'id');
    $nom = array_column($resultat, 'nom');
    $prenom = array_column($resultat, 'prenom');
    $mail = array_column($resultat, 'mail');
    $type = array_column($resultat, 'type');


?>

<body>
    <main>
        <section class="top">
            <div class="left">
                <h1>Bienvenue <?php echo $_SESSION['prenom'];?></h1>
            </div>  
            <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?> ">
                <button type="submit">Deconnexion</button>
            </form>
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
                    echo "<td>" . $id[$i] . "</td>";
                    echo "<td>" . $nom[$i] . "</td>";
                    echo "<td>" . $prenom[$i] . "</td>";
                    echo "<td>" . $mail[$i] . "</td>";
                    echo "<td>" . $type[$i] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </section>
        <section class="btn">
            
            <a href="ajouter.php"><button class="btn_input">Ajouter</button></a>
            <a href="modifier.php"><button class="btn_input">Modifier</button></a>
            <a href="delete.php"><button class="btn_input">Supprimer</button></a>
            
        </section>
    </main>
</body>
</html>