<?php
$servername = "localhost"; 
$username = "root";       
$password = "";            

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS JukeBox";
if ($conn->query($sql) === TRUE) {
    echo "Database 'JukeBox' creato con successo.<br>";
} else {
    die("Errore nella creazione del database: " . $conn->error);
}

$conn->select_db("JukeBox");

$sql = "
    CREATE TABLE IF NOT EXISTS Cantanti (
        id_cantante INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        cognome VARCHAR(50) NOT NULL,
        nome_darte VARCHAR(50) NOT NULL,
        data_di_nascita date NOT NULL
    );
";
if ($conn->query($sql) === TRUE) {
    echo "Tabella 'Cantanti' creata con successo.<br>";
} else {
    die("Errore nella creazione della tabella: " . $conn->error);
}

$sql = "
    CREATE TABLE IF NOT EXISTS Canzoni (
        id_canzone INT AUTO_INCREMENT PRIMARY KEY,
        titolo VARCHAR(50) NOT NULL,
        genere VARCHAR(50) NOT NULL,
        producer VARCHAR(50) NOT NULL,
        release_date date NOT NULL
    );
";
if ($conn->query($sql) === TRUE) {
    echo "Tabella 'Canzone' creata con successo.<br>";
} else {
    die("Errore nella creazione della tabella: " . $conn->error);
}

$conn->close();
?>
