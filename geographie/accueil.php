<?php
session_start();
$username = $_SESSION['username'] ?? null;
$theme_num = isset($_GET['theme']) ? intval($_GET['theme']) : 0;
$_SESSION['theme'] = $theme_num;
function getUserData($username, $level) {
    $file = "theme{$level}.txt";
    if (!file_exists($file)) return [0, 0, []];

    $lines = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        [$user, $score] = explode('|', $line) + [null, 0];
        if ($user === $username) {
            return [(int)$score];
        }
    }
    return [0, 0, []];
}


[$user_score] = getUserData($username, $theme_num);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quizz.Les Risques</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    <div class="container">
        <?php if ($theme_num == 0): ?>
        <h1>Quizz . Les Risques</h1>
        <div class="menu">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <a class="button" href="?theme=<?= $i ?>">theme <?= $i ?></a>
            <?php endfor; ?>
        </div>

        <!-- Classement des utilisateurs -->
        <div class="classement">
            <h2>🏆 Classement des utilisateurs</h2>
            <div class="classement-wrapper">
            <div class="table-container">
            <?php
            $utilisateurs = [];

            for ($niv = 1; $niv <= 4; $niv++) {
                $fichier = "theme{$niv}.txt";
                if (!file_exists($fichier)) continue;
                $lignes = file($fichier, FILE_IGNORE_NEW_LINES);
                foreach ($lignes as $ligne) {
                    if (trim($ligne) === '') continue;
                    [$user, $score] = explode('|', $ligne) + [null, 0];
                    if (!isset($utilisateurs[$user])) {
                        $utilisateurs[$user] = ['total' => 0];
                    }
                    $utilisateurs[$user]["theme{$niv}_score"] = (int)$score;
                    $utilisateurs[$user]['total'] += (int)$score;
                }
            }

            // Tri par score total décroissant
            uasort($utilisateurs, fn($a, $b) => $b['total'] <=> $a['total']);

            echo "<table>";
            echo "<thead><tr><th>Utilisateur</th><th>Score</th></tr></thead><tbody>";
            foreach ($utilisateurs as $user => $data) {
                $t1s = $data['theme1_score'] ?? 0;
                $t2s = $data['theme2_score'] ?? 0;
                $t3s = $data['theme3_score'] ?? 0;
                $t4s = $data['theme4_score'] ?? 0;
                echo "<tr>
                    <td data-label='Utilisateur'>$user</td>
                    
                    <td data-label='Total'><strong>{$data['total']}</strong> pts</td>
                </tr>";
            }
            echo "</tbody></table>";
            
            ?>
        </div>
    <?php else: ?>

            <?php
                $json_file = "theme{$theme_num}.json";
                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                } else {
                    echo "<p>Erreur : fichier non trouvé.</p>";
                    exit;
                }
            ?>
            <h1>Theme <?= $theme_num ?> -Quizz</h1>
            <div id="quiz">
                <div id="score">Score : <?= $user_score ?> </div>
                <div id="question"></div>
                <div id="options"></div>
                <div id="feedback"></div>
                <button id="next">Question suivante</button>
            </div>
            <script>
                const syllabus = <?= $json_data ?>;
                const userData = {
                    username: "<?= $username ?>",
                    score: <?= $user_score ?>,
                };
            </script>
            <script src="script.js"></script>
        <?php endif; ?>
        </div>
        </div>
    </div>
    <footer><h6>Tout droits réservés Jean-Antoine Dary®. Code source: <a href="https://github.com/J-A2b/GEO_QUIZZ">ici</a></h6></footer>
</body>
<style>
    /* style du tableau responsive, simple, couleurs rouges */
    /* Style du tableau de classement */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1em;
    background-color: #fff0f0;
    box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2);
    border-radius: 12px;
    overflow: hidden;
    font-family: sans-serif;
}

thead {
    background-color: #3868d8;;
    color: white;
}

thead th {
    padding: 12px;
    text-align: left;
}

tbody tr {
    border-bottom: 1px solid #f2caca;
}

tbody tr:nth-child(even) {
    background-color: #ffe5e5;
}

tbody td {
    padding: 10px 12px;
}

tbody td strong {
    color:  #0c59b2;
}

/* Responsive - affichage mobile */
@media screen and (max-width: 600px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tbody tr {
        margin-bottom: 1em;
        background: #fff5f5;
        border: 1px solid #f0c0c0;
        border-radius: 8px;
        padding: 10px;
    }

    td {
        padding: 8px;
        text-align: right;
        position: relative;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        top: 8px;
        font-weight: bold;
        color: #0c59b2;
        text-align: left;
    }
}

</style>

</html>
