<?php
require_once 'config.php';
$conn = getConnection();

$titolo = $_POST['titolo'];
$genere = $_POST['genere'];
$release_date = $_POST['release_date'];
$producer = $_POST['producer'];

$sql_find = "SELECT Id_Canzone FROM Canzone 
            WHERE Titolo = ? AND Genere = ? AND Release_Date = ? AND Producer = ?";
$stmt_find = $conn->prepare($sql_find);
$stmt_find->bind_param("ssss", $titolo, $genere, $release_date, $producer);
$stmt_find->execute();
$result = $stmt_find->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_canzone = $row['Id_Canzone'];
    
    $sql_delete_interpreta = "DELETE FROM Interpreta WHERE Id_Canzone = ?";
    $stmt_delete_interpreta = $conn->prepare($sql_delete_interpreta);
    $stmt_delete_interpreta->bind_param("i", $id_canzone);
    $stmt_delete_interpreta->execute();
    $stmt_delete_interpreta->close();
    
    $sql_delete_canzone = "DELETE FROM Canzone WHERE Id_Canzone = ?";
    $stmt_delete_canzone = $conn->prepare($sql_delete_canzone);
    $stmt_delete_canzone->bind_param("i", $id_canzone);
    
    if ($stmt_delete_canzone->execute()) {
        echo "<h3>Canzone eliminata con successo!</h3>";
        echo "<p>La canzone \"$titolo\" è stata rimossa dal database.</p>";
    } else {
        echo "<h3>Errore durante l'eliminazione</h3>";
        echo "<p>Si è verificato un errore durante l'eliminazione della canzone: " . $stmt_delete_canzone->error . "</p>";
    }
    
    $stmt_delete_canzone->close();
} else {
    echo "<h3>Canzone non trovata</h3>";
    echo "<p>Nessuna canzone corrisponde esattamente a tutti gli attributi specificati.</p>";
    echo "<p>Dettagli ricerca:</p>";
    echo "<ul>";
    echo "<li>Titolo: $titolo</li>";
    echo "<li>Genere: $genere</li>";
    echo "<li>Data di pubblicazione: $release_date</li>";
    echo "<li>Producer: $producer</li>";
    echo "</ul>";
}

echo "<a href='CancellaCanzone.html'>Torna al form di eliminazione</a>";
echo "<br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt_find->close();
$conn->close();
?>