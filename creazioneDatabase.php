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
    CREATE TABLE IF NOT EXISTS Cantante (
        id_cantante INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        cognome varchar(50) NOT NULL,
        nome_dArte VARCHAR(100) NOT NULL UNIQUE,
        data_di_nascita date NOT NULL
    );
";
if ($conn->query($sql) === TRUE) {
    echo "Tabella 'Cantante' creata con successo.<br>";
} else {
    die("Errore nella creazione della tabella: " . $conn->error);
}

$sql = "
    CREATE TABLE IF NOT EXISTS Canzone (
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

$sql = "
    CREATE TABLE IF NOT EXISTS Interpreta (
        id_canzone INT NOT NULL,
        id_cantante INT NOT NULL,
        PRIMARY KEY (id_canzone, id_cantante),
        FOREIGN KEY (id_canzone) REFERENCES Canzone(id_canzone) ON DELETE CASCADE,
        FOREIGN KEY (id_cantante) REFERENCES Cantante(id_cantante) ON DELETE CASCADE
    );
";
if ($conn->query($sql) === TRUE) {
    echo "Tabella 'Interpreta' creata con successo.<br>";
} else {
    die("Errore nella creazione della tabella: " . $conn->error);
}

$conn->close();
?>