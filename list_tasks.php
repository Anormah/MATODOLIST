<?php
$file = fopen('tasks.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
  list($title, $description, $priority, $deadline) = $line;
  $class = $priority;
  if (new DateTime() > new DateTime($deadline)) {
    $class .= ' overdue';
  }
  echo "<p class=\"$class\">$title</p>";
}
fclose($file);