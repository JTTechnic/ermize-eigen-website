<?php
// config/database.php

// Laad de autoloading van Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Gebruik Dotenv om de .env-waarden te laden
use Dotenv\Dotenv;

// Laad .env bestand
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Verkrijg de databaseconfiguratie uit .env
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];

// Maak de databaseverbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// Zorg ervoor dat je geen extra witruimtes in je query's hebt
$conn->set_charset("utf8mb4");
?>
