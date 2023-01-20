<?php

$DATABASE_HOSTNAME = $_ENV["DATABASE_NAME"];
$DATABASE_USERNAME = $_ENV["DATABASE_USER"];
$DATABASE_PASSWORD = $_ENV["DATABASE_PASS"];
$DATABASE_NAME = $_ENV["DATABASE_NAME"];

$conn = new mysqli($DATABASE_HOSTNAME, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($conn->connect_error)
{
    die('Database error:' . $conn->connect_error);
}
?>
