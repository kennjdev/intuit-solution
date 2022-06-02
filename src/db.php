<?php


$host = "db"; 
$dbname="intuit"; 
$username = "root"; 
$password = "secret"; 
$db = new PDO("mysql:host=$host; dbname=$dbname; charset=utf8", $username, $password);  
