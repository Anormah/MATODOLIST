<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file = 'tasks.csv';

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

function writeTasks($file, $tasks) {
    $fileHandle = fopen($file, 'w');
    foreach ($tasks as $task) {
        fputcsv($fileHandle, $task);
    }
    fclose($fileHandle);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tasks = readTasks($file);

    // Supprime les tâches cochées uniquement lors de la soumission du formulaire de suppression
    if (isset($_POST['delete']) && is_array($_POST['delete']) && !empty($_POST['delete'])) {
        $deleteIndexes = array_map('intval', $_POST['delete']); // Convertit les index en entiers
        foreach ($deleteIndexes as $deleteIndex) {
            unset($tasks[$deleteIndex]);
        }
        $tasks = array_values($tasks); 
        writeTasks($file, $tasks);

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    $checkedIndexes = isset($_POST['checked']) ? array_map('intval', $_POST['checked']) : []; 
    foreach ($tasks as $index => &$task) {
        if (in_array($index, $checkedIndexes)) {
            $task['checked'] = 'true';
        } else {
            $task['checked'] = 'false';
        }
    }
    writeTasks($file, $tasks);

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

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$tasks = readTasks($file);

include 'index.html';
?>