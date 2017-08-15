<?php

require_once('../vendor/autoload.php');

$vivid = new Vivid('localhost', 'phonebook', 'root', 'password');

$results = $vivid->table('users')
      ->limit(2)
      ->get();

foreach($results as $result) {
    echo $result->first_name . '<br>';;
}