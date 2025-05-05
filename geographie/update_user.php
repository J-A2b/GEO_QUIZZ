<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$score = (int)$data['score'];
$level = isset($_SESSION['theme']) ? (int)$_SESSION['theme'] : 1; // par défaut HSK 1

$fichier = "theme{$level}.txt";

$lines = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
$updated = false;

foreach ($lines as &$line) {
    [$user] = explode('|', $line);
    if ($user === $username) {
        $line = "$username|$score";
        $updated = true;
        break;
    }
}

if (!$updated) {
    $lines[] = "$username|$score";
}

file_put_contents($fichier, implode("\n", $lines) . "\n");

