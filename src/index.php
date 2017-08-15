<?php

require_once('../vendor/autoload.php');
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$vivid = new Vivid;

$results = $vivid->table('users')
      ->limit(2)
      ->get();

foreach($results as $result) {
    echo $result->first_name . '<br>';;
}