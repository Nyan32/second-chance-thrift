<?php
$servername = "localhost";
$username = "second_chance_thrift";
$password = "admin12345";
$database = "second_chance_thrift";

date_default_timezone_set('Asia/Jakarta');

// Create connection
$mysqli = new mysqli($servername, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
