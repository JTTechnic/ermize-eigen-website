<?php
// backend.php

// Include de database configuratie
require_once '../../config/database.php';

// Verkrijg de type parameter
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'login') {
    // Login functionaliteit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Basis SQL-injectie bescherming
        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);

        // Verkrijg de gebruiker uit de database
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Vergelijk de wachtwoorden
            if (password_verify($password, $user['password'])) {
                echo "Login succesvol";
            } else {
                echo "Ongeldig wachtwoord";
            }
        } else {
            echo "Gebruiker niet gevonden";
        }
    }
} elseif ($type === 'register') {
    // Registratie functionaliteit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Basis SQL-injectie bescherming
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);
        $confirm_password = $conn->real_escape_string($confirm_password);

        if ($password === $confirm_password) {
            // Controleer of de gebruiker al bestaat
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($sql);

            if ($result->num_rows === 0) {
                // Versleutel het wachtwoord
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Voeg de gebruiker toe aan de database
                $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
                if ($conn->query($sql) === TRUE) {
                    echo "Registratie succesvol";
                } else {
                    echo "Fout bij registratie: " . $conn->error;
                }
            } else {
                echo "Gebruiker bestaat al";
            }
        } else {
            echo "Wachtwoorden komen niet overeen";
        }
    }
} else {
    echo "Ongeldig type opgegeven";
}

// Sluit de connectie
$conn->close();
