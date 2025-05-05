<?php

$user = 'root';
$pass = 'root';
$db = 'samson_test';
$host = 'localhost';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8');
if ($conn->connect_error) {
    throw new Exception('Ошибка подключения: ' . $conn->connect_error);
}
