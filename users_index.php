<?php 
    session_start();

    if (empty($_SESSION['prenom']) || empty($_SESSION['password'])) {
        header("location:connexion.php");
        exit;
    }
?> 

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_users.css">
    <title><?php echo $_SESSION['prenom'];?> | Accueil</title>
</head>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header("location:session_end.php");
        exit;
    }
?>
<body>
    <main>
        <div class="left">
            <h1>Bienvenue <?php echo $_SESSION['prenom'];?></h1>
        </div>
        <form method="POST" action="<?php $_SERVER['PHP_SELF']?> ">
            <button type="submit">Deconnexion</button>
        </form>
    </main>
</body>
</html>