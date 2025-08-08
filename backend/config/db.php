<?php
$host = 'localhost';
$dbname = 'belantara';
$username = 'root'; // Default Laragon
$password = '';     // Default Laragon (kosong)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>