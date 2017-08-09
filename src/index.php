<?php
require_once('../vendor/autoload.php');

$vivid = new Vivid('localhost','phonebook','root','password');
$user = [
    'first_name' => 'Renzows',
    'last_name'  => 'Alonzo',
    'middle_name'=> 'Precilla',
    'birthdate' => 'Feb. 25, 1998',
    'address_line1' => 'C. Devenecia st.',
    'address_line2' => 'Brgy Old Zaniga',
    'city' => 'Mandaluyong',
    'province' => 'Manila',
    'mobile_number' => '09363515387',
    'home_number' => '7464287'
];
$vivid->update('users',$user,1);
?>