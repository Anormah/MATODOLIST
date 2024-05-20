<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chemin vers le fichier CSV
$file = 'tasks.csv';

// Fonction pour lire les tâches depuis le fichier CSV
function readTasks($file) {
    $tasks = [];
    if (file_exists($file)) {
        $fileHandle = fopen($file, 'r');
        while (($data = fgetcsv($fileHandle, 1000, ',')) !== FALSE) {
            $tasks[] = [
                'title' => $data[0],
                'description' => $data[1],
                'priority' => $data[2],
                'deadline' => $data[3],
                'checked' => $data[4]
            ];
        }
        fclose($fileHandle);
    }
    return $tasks;
}

// Fonction pour écrire les tâches dans le fichier CSV
function writeTasks($file, $tasks) {
    $fileHandle = fopen($file, 'w');
    foreach ($tasks as $task) {
        fputcsv($fileHandle, $task);
    }
    fclose($fileHandle);
}

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tasks = readTasks($file);

    // Supprime les tâches cochées uniquement lors de la soumission du formulaire de suppression
    if (isset($_POST['delete']) && is_array($_POST['delete']) && !empty($_POST['delete'])) {
        $deleteIndexes = array_map('intval', $_POST['delete']); // Convertit les index en entiers
        foreach ($deleteIndexes as $deleteIndex) {
            unset($tasks[$deleteIndex]);
        }
        $tasks = array_values($tasks); // Réindexe le tableau
        writeTasks($file, $tasks);

        // Redirige pour éviter les soumissions de formulaire en double
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Met à jour l'état des tâches cochées et ajoute de nouvelles tâches si nécessaire
    $checkedIndexes = isset($_POST['checked']) ? array_map('intval', $_POST['checked']) : []; // Convertit les index en entiers
    foreach ($tasks as $index => &$task) {
        if (in_array($index, $checkedIndexes)) {
            $task['checked'] = 'true';
        } else {
            $task['checked'] = 'false';
        }
    }
    writeTasks($file, $tasks);

    // Ajoute une nouvelle tâche
    if (isset($_POST['title'], $_POST['description'], $_POST['priority'], $_POST['deadline'])) {
        $newTask = [
            $_POST['title'],
            $_POST['description'],
            $_POST['priority'],
            $_POST['deadline'],
            'false'
        ];
        $tasks[] = $newTask;
        writeTasks($file, $tasks);

        // Redirige pour éviter les soumissions de formulaire en double
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Récupère les tâches existantes
$tasks = readTasks($file);

// Inclut le fichier HTML
include 'index.html';
?>