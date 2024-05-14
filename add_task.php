<?php
$file = fopen('tasks.csv', 'a');
fputcsv($file, $_POST);
fclose($file);
header('Location: index.html');