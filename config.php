
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "COVALORENZO";

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