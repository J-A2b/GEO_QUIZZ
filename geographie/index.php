<?php
session_start();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    $_SESSION['username'] = $username;    
    // Initialisation dans fichiers HSK
    $fichiers = ['utilisateurs.txt', 'theme1.txt', 'theme2.txt', 'theme3.txt', 'theme4.txt'];
    foreach ($fichiers as $fichier) {
        $liste = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
        $existe = false;
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            $ligne_init = "$username|0\n\n";
            file_put_contents($fichier, $ligne_init, FILE_APPEND);
        }
    

}
header("Location: accueil.php");
exit;
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quizz . Les Risques</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Quizz . Les Risques </h1>
        <?php if (!empty($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
