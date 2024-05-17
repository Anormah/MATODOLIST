<?php
// Chemin vers le fichier CSV
$file = 'tasks.csv';

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajoute une nouvelle tâche
    if (isset($_POST['title'], $_POST['description'], $_POST['priority'], $_POST['deadline'])) {
        $newTask = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'priority' => $_POST['priority'],
            'deadline' => $_POST['deadline'],
            'checked' => 'false',
        ];

        $fileData = file_exists($file) ? file_get_contents($file) : '';
        $tasks = $fileData !== '' ? json_decode($fileData, true) : [];
        $tasks[] = $newTask;

        file_put_contents($file, json_encode($tasks));
    }

    // Supprime les tâches cochées
    if (isset($_POST['delete'])) {
        $fileData = file_exists($file) ? file_get_contents($file) : '';
        $tasks = $fileData !== '' ? json_decode($fileData, true) : [];

        foreach ($_POST['delete'] as $deleteIndex) {
            unset($tasks[$deleteIndex]);
        }

        $tasks = array_values($tasks); // Réindexe le tableau

        file_put_contents($file, json_encode($tasks));
    }

    // Redirige pour éviter les soumissions de formulaire en double
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Récupère les tâches existantes
$fileData = file_exists($file) ? file_get_contents($file) : '';
$tasks = $fileData !== '' ? json_decode($fileData, true) : [];

// Génère les options pour la liste déroulante de l'année
$yearOptions = '';
for ($i = date('Y'); $i <= date('Y') + 5; $i++) {
    $yearOptions .= "<option value=\"$i\">$i</option>";
}

// Inclut le fichier HTML
include 'index.html';
?>
