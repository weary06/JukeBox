
<?php
// Configurazione del database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jukebox";

// Creazione della connessione
function getConnection() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verifica della connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    
    return $conn;
}
?>