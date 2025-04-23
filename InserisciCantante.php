<?php
require_once 'config.php';
$conn = getConnection();

$nome_arte = $_POST['nome_arte'];
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$data_nascita = $_POST['data_nascita'];

$check_sql = "SELECT Id_Cantante FROM Cantante WHERE Nome_dArte = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $nome_arte);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "Errore: Esiste già un cantante con questo nome d'arte.";
} else {
    $insert_sql = "INSERT INTO Cantante (nome_darte, Data_Di_Nascita, Nome, Cognome) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssss", $nome_arte, $data_nascita, $nome, $cognome);

    if ($insert_stmt->execute()) {
        echo "Cantante aggiunto con successo!";
    } else {
        echo "Errore durante l'inserimento: " . $insert_stmt->error;
    }

    $insert_stmt->close();
}
echo "</ul>";
echo "<a href='InserisciCantante.html'>aggiungi un altro cantante</a>";
echo "<br>";
echo "<a href='index.html'>Torna alla Home</a>";

$check_stmt->close();
$conn->close();
?>
