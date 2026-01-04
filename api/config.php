<?php
// api/config.php

// Ganti kredensial ini nanti dengan Environment Variables agar aman
$servername = "sql200.byetcluster.com";
$username = "b11_36956301";
$password = "xjgphvny"; // SEGERA GANTI PASSWORD INI DI HOSTING!
$dbname = "b11_36956301_todolist";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>